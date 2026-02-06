<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Document;
use App\Models\Payment;
use App\Models\Finance;
use Illuminate\Http\Request;

class AdminController extends Controller
{
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
        return redirect()->back()->with('success', 'Verifikasi dokumen dibatalkan.');
    }

    /**
     * Hapus Order
     */
    public function destroyOrder($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->delete();
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

        return redirect()->back()->with('success', 'Jadwal telah ditetapkan dan status order kini ACTIVE.');
    }

    /**
     * Dashboard Keuangan
     */
    public function financeIndex()
    {
        $finances = Finance::with('order.service', 'order.user')->latest()->get();
        $totalRevenue = $finances->sum('revenue');
        $totalCogs = $finances->sum('cogs');
        $totalProfit = $totalRevenue - $totalCogs;

        $availableItems = \App\Models\CogsItem::all();
        $availableFacilitators = \App\Models\Facilitator::all();

        return view('admin.finance.index', compact('finances', 'totalRevenue', 'totalCogs', 'totalProfit', 'availableItems', 'availableFacilitators'));
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
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diverifikasi. Invoice telah diterbitkan.');
    }
}