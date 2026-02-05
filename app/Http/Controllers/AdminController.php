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
            'address' => 'required_if:location_type,offline',
        ]);

        $schedule = \App\Models\Schedule::findOrFail($scheduleId);
        $schedule->update($request->all());

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
            'meeting_link' => 'nullable|url',
        ]);

        $order = Order::findOrFail($orderId);

        \App\Models\Schedule::create([
            'order_id' => $order->id,
            'title' => $request->title,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'meeting_link' => $request->meeting_link,
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

        return view('admin.finance.index', compact('finances', 'totalRevenue', 'totalCogs', 'totalProfit'));
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