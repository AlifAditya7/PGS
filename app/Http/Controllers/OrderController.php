<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Service;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    /**
     * Tampilkan katalog layanan untuk customer
     */
    public function index()
    {
        $services = Service::all();
        return view('orders.catalog', compact('services'));
    }

    /**
     * Upload Surat yang sudah ditanda tangan
     */
    public function uploadSignedLetter(Request $request, $orderId)
    {
        $request->validate([
            'signed_file' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);

        if ($request->file('signed_file')) {
            $path = $request->file('signed_file')->store('signed_letters', 'public');

            // Update atau buat entitas dokumen Signed Letter
            Document::updateOrCreate(
                ['order_id' => $order->id, 'type' => 'signed_letter'],
                ['file_path' => $path, 'status' => 'uploaded']
            );

            // Update status order menjadi confirmed (menunggu verifikasi admin)
            $order->update(['status' => 'confirmed']);

            return redirect()->back()->with('success', 'Surat berhasil diupload. Admin akan segera memverifikasi.');
        }

        return redirect()->back()->with('error', 'Gagal mengupload file.');
    }

    /**
     * Download Surat Konfirmasi
     */
    public function downloadLetter($orderId)
    {
        $order = Order::where('user_id', Auth::id())->with('service', 'user')->findOrFail($orderId);
        
        $pdf = Pdf::loadView('pdf.confirmation_letter', compact('order'));
        
        return $pdf->download('Confirmation_Letter_' . $order->order_number . '.pdf');
    }

    /**
     * Download Invoice
     */
    public function downloadInvoice($orderId)
    {
        $order = Order::where('user_id', Auth::id())->with('service', 'user')->findOrFail($orderId);
        
        $pdf = Pdf::loadView('pdf.invoice', compact('order'));
        
        return $pdf->download('Invoice_' . $order->order_number . '.pdf');
    }

    /**
     * Upload Bukti Pembayaran
     */
    public function uploadPaymentProof(Request $request, $orderId)
    {
        $request->validate([
            'payment_proof' => 'required|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $order = Order::where('user_id', Auth::id())->findOrFail($orderId);

        if ($request->file('payment_proof')) {
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');

            // Simpan data di tabel payments
            \App\Models\Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'proof_path' => $path,
                'status' => 'pending'
            ]);

            return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload. Admin akan segera memverifikasi.');
        }

        return redirect()->back()->with('error', 'Gagal mengupload file.');
    }

    /**
     * Proses pendaftaran layanan (Checkout)
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id'
        ]);

        $service = Service::find($request->service_id);

        // Buat Order baru
        $order = Order::create([
            'order_number' => 'PGS-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
            'user_id' => Auth::id(),
            'service_id' => $service->id,
            'status' => 'pending',
            'total_price' => $service->price,
        ]);

        // Buat entitas Dokumen untuk Confirm Letter
        Document::create([
            'order_id' => $order->id,
            'type' => 'confirmation_letter',
            'status' => 'draft',
        ]);

        return redirect()->route('orders.my-orders')->with('success', 'Pendaftaran berhasil. Silakan download dan tanda tangan surat konfirmasi.');
    }

    /**
     * Tampilkan daftar order milik customer
     */
    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::id())->with('service', 'documents')->latest()->get();
        return view('orders.my-orders', compact('orders'));
    }
}