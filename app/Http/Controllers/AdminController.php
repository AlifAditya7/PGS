<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Document;
use App\Models\Payment;
use App\Models\Finance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\PGSNotification;
use Illuminate\Support\Facades\Notification;

use App\Models\ActivityLog;

class AdminController extends Controller
{
    /**
     * Tampilkan Riwayat Aktivitas
     */
    public function activityLogs()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(50);
        return view('admin.activity-logs.index', compact('logs'));
    }

    /**
     * Tampilkan Dashboard Analytics
     */
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $stats = [
                'total_users' => \App\Models\User::role('customer')->count(),
                'total_orders' => \App\Models\Order::count(),
                'total_revenue' => \App\Models\Finance::sum('revenue'),
                'total_profit' => \App\Models\Finance::sum('revenue') - \App\Models\Finance::sum('cogs'),
            ];

            $chartCategories = \App\Models\Service::withCount('orders')->get();
            $monthlyRevenue = \App\Models\Finance::selectRaw('SUM(revenue) as total, MONTH(created_at) as month')
                ->groupBy('month')->orderBy('month')->get();

            return view('dashboard', compact('stats', 'chartCategories', 'monthlyRevenue'));
        }

        // Customer Dashboard Logic
        $topServices = \App\Models\Service::latest()->take(3)->get();
        
        // Ambil semua jadwal yang status ordernya 'active' milik user ini
        $upcomingSchedules = \App\Models\Schedule::whereHas('order', function($q) use ($user) {
            $q->where('user_id', $user->id)->where('status', 'active');
        })
        ->with('order.service')
        ->orderBy('start_time')
        ->get();

        return view('dashboard', compact('topServices', 'upcomingSchedules'));
    }

    /**
     * Tampilkan semua order untuk admin
     */
    public function index()
    {
        $orders = Order::with(['user', 'service', 'documents', 'payments'])->latest()->get();
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Disverifikasi Dokumen
     */
    public function unverifyDocument($documentId)
    {
        $document = Document::findOrFail($documentId);
        $document->update(['status' => 'uploaded']);

        ActivityLog::log('UNVERIFY_DOC', 'Membatalkan verifikasi dokumen Order #' . $document->order->order_number);

        return redirect()->back()->with('success', 'Verifikasi dokumen dibatalkan.');
    }

    /**
     * Hapus Order
     */
    public function destroyOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        $orderNum = $order->order_number;
        $order->delete();

        ActivityLog::log('DELETE_ORDER', 'Menghapus data pendaftaran Order #' . $orderNum);

        return redirect()->route('admin.orders.index')->with('success', 'Order berhasil dihapus.');
    }

    /**
     * Update Jadwal
     */
    public function updateSchedule(Request $request, $scheduleId)
    {
        $request->validate([
            'title' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location_type' => 'required|in:online,offline',
            'meeting_link' => 'required_if:location_type,online|nullable|url',
            'address' => 'required_if:location_type,offline|nullable|string',
            'latitude' => 'required_if:location_type,offline|nullable|numeric',
            'longitude' => 'required_if:location_type,offline|nullable|numeric',
        ]);

        $schedule = \App\Models\Schedule::findOrFail($scheduleId);
        
        $data = $request->all();
        if ($request->location_type == 'online') {
            $data['address'] = null;
            $data['latitude'] = null;
            $data['longitude'] = null;
        } else {
            $data['meeting_link'] = null;
        }

        $schedule->update($data);

        ActivityLog::log('UPDATE_SCHEDULE', 'Mengubah rincian jadwal untuk Order #' . $schedule->order->order_number);

        // Notify Customer
        $schedule->order->user->notify(new PGSNotification(
            'Pembaruan Jadwal Pelaksanaan',
            'Ada perubahan pada jadwal layanan ' . $schedule->order->service->name . '. Silakan cek dashboard Anda.',
            route('orders.my-orders')
        ));

        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui.');
    }

    /**
     * Update COGS Terperinci
     */
    public function updateCogsDetailed(Request $request, $financeId)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $finance = Finance::findOrFail($financeId);
        
        $totalCogs = 0;
        $items = $request->items;
        foreach ($items as &$item) {
            $item['total'] = $item['qty'] * $item['price'];
            $totalCogs += $item['total'];
        }

        $finance->update([
            'cogs' => $totalCogs,
            'expense_items' => $items
        ]);

        ActivityLog::log('UPDATE_COGS', 'Memperbarui rincian COGS untuk Order #' . $finance->order->order_number);

        return redirect()->back()->with('success', 'Rincian COGS berhasil diperbarui.');
    }

    /**
     * Verifikasi Pembayaran
     */
    public function verifyPayment($paymentId)
    {
        $payment = Payment::findOrFail($paymentId);
        $payment->update(['status' => 'paid']);

        $order = $payment->order;
        $order->update(['status' => 'paid']);

        // Update status dokumen invoice menjadi verified
        Document::where('order_id', $order->id)->where('type', 'invoice')->update(['status' => 'verified']);

        // Catat ke tabel Finance sebagai revenue
        Finance::updateOrCreate(
            ['order_id' => $order->id],
            ['revenue' => $payment->amount]
        );

        ActivityLog::log('VERIFY_PAYMENT', 'Memverifikasi pembayaran untuk Order #' . $order->order_number);

        // Notify Customer
        $order->user->notify(new PGSNotification(
            'Pembayaran Diterima',
            'Pembayaran Anda untuk order ' . $order->order_number . ' telah diverifikasi.',
            route('orders.my-orders')
        ));

        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi. Status order kini PAID.');
    }

    /**
     * Tetapkan Jadwal Layanan
     */
    public function setSchedule(Request $request, $orderId)
    {
        $request->validate([
            'title' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location_type' => 'required|in:online,offline',
            'meeting_link' => 'required_if:location_type,online|nullable|url',
            'address' => 'required_if:location_type,offline|nullable|string',
            'latitude' => 'required_if:location_type,offline|nullable|numeric',
            'longitude' => 'required_if:location_type,offline|nullable|numeric',
        ]);

        $order = Order::findOrFail($orderId);

        \App\Models\Schedule::create([
            'order_id' => $order->id,
            'title' => $request->title,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'location_type' => $request->location_type,
            'meeting_link' => $request->location_type == 'online' ? $request->meeting_link : null,
            'address' => $request->location_type == 'offline' ? $request->address : null,
            'latitude' => $request->location_type == 'offline' ? $request->latitude : null,
            'longitude' => $request->location_type == 'offline' ? $request->longitude : null,
        ]);

        $order->update(['status' => 'active']);

        ActivityLog::log('SET_SCHEDULE', 'Menetapkan jadwal awal pelaksanaan untuk Order #' . $order->order_number);

        // Notify Customer
        $order->user->notify(new PGSNotification(
            'Jadwal Pelaksanaan Ditetapkan',
            'Jadwal untuk layanan ' . $order->service->name . ' telah diatur oleh Admin.',
            route('orders.my-orders')
        ));

        return redirect()->back()->with('success', 'Jadwal telah ditetapkan dan status order kini ACTIVE.');
    }

    /**
     * Dashboard Keuangan
     */
    public function financeIndex()
    {
        $finances = Finance::with('order.service', 'order.user')
            ->latest()
            ->get();

        // Group by Month and Year
        $groupedFinances = $finances->groupBy(function($date) {
            return \Carbon\Carbon::parse($date->created_at)->format('F Y');
        });

        $totalRevenue = $finances->sum('revenue');
        $totalCogs = $finances->sum('cogs');
        $totalProfit = $totalRevenue - $totalCogs;

        $availableItems = \App\Models\CogsItem::all();
        $availableFacilitators = \App\Models\Facilitator::all();

        return view('admin.finance.index', compact('groupedFinances', 'totalRevenue', 'totalCogs', 'totalProfit', 'availableItems', 'availableFacilitators'));
    }

    /**
     * Download Laporan Keuangan Bulanan (PDF)
     */
    public function downloadFinanceReport(Request $request)
    {
        $monthYear = $request->month_year; // Format: "February 2026"
        $date = \Carbon\Carbon::parse($monthYear);
        
        $finances = Finance::with('order.service', 'order.user')
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->get();

        $summary = [
            'month' => $monthYear,
            'total_revenue' => $finances->sum('revenue'),
            'total_cogs' => $finances->sum('cogs'),
            'total_profit' => $finances->sum('revenue') - $finances->sum('cogs'),
        ];

        $pdf = Pdf::loadView('pdf.finance_report', compact('finances', 'summary'));
        return $pdf->download('Laporan_Keuangan_' . str_replace(' ', '_', $monthYear) . '.pdf');
    }

    /**
     * Update COGS
     */
    public function updateCogs(Request $request, $financeId)
    {
        $request->validate(['cogs' => 'required|numeric|min:0']);
        $finance = Finance::findOrFail($financeId);
        $finance->update(['cogs' => $request->cogs]);

        return redirect()->back()->with('success', 'Biaya operasional (COGS) berhasil diupdate.');
    }

    /**
     * Verifikasi surat yang diupload dan terbitkan invoice
     */
    public function verifyDocument($documentId)
    {
        $document = Document::findOrFail($documentId);
        $document->update(['status' => 'verified']);

        $order = $document->order;

        // Jika yang diverifikasi adalah surat tanda tangan, terbitkan invoice
        if ($document->type == 'signed_letter') {
            Document::updateOrCreate(
                ['order_id' => $order->id, 'type' => 'invoice'],
                ['status' => 'draft']
            );
            
            $order->update(['status' => 'confirmed']);

            ActivityLog::log('VERIFY_DOC', 'Memverifikasi surat konfirmasi untuk Order #' . $order->order_number);

            // Notify Customer
            $order->user->notify(new PGSNotification(
                'Surat Diverifikasi',
                'Surat konfirmasi Anda telah diverifikasi. Invoice pembayaran telah diterbitkan.',
                route('orders.my-orders')
            ));
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diverifikasi. Invoice telah diterbitkan.');
    }
}