<!DOCTYPE html>
<html>
<head>
    <title>Invoice - {{ $order->order_number }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .invoice-title { font-size: 24px; font-bold: true; color: #1a56db; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table th, .details-table td { padding: 12px; border: 1px solid #eee; text-align: left; }
        .details-table th { background-color: #f8fafc; color: #475569; }
        .total-box { margin-top: 20px; text-align: right; font-size: 18px; font-weight: bold; }
        .footer { margin-top: 50px; font-size: 12px; color: #64748b; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="invoice-title">INVOICE</div>
        <p>{{ \App\Models\Setting::get('company_name', 'PT. PGS Consulting & Training') }}</p>
    </div>

    <div style="margin-bottom: 20px;">
        <p><strong>Tagihan Kepada:</strong><br>
        {{ $order->user->name }}<br>
        {{ $order->user->email }}</p>
        
        <p><strong>Detail Transaksi:</strong><br>
        No. Invoice: #INV-{{ $order->order_number }}<br>
        Tanggal: {{ date('d F Y') }}</p>
    </div>

    <table class="details-table">
        <thead>
            <tr>
                <th>Deskripsi Layanan</th>
                <th>Kategori</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->service->name }}</td>
                <td>{{ ucfirst($order->service->category) }}</td>
                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        TOTAL PEMBAYARAN: Rp {{ number_format($order->total_price, 0, ',', '.') }}
    </div>

    <div style="margin-top: 40px; border: 1px dashed #cbd5e1; padding: 15px;">
        <p style="margin-top: 0;"><strong>Metode Pembayaran (Transfer Bank):</strong></p>
        <p style="margin-bottom: 0;">{{ \App\Models\Setting::get('bank_name') }}: {{ \App\Models\Setting::get('bank_account_number') }}<br>
        A/N: {{ \App\Models\Setting::get('bank_account_name') }}</p>
    </div>

    <div class="footer">
        <p>Harap unggah bukti transfer di portal PGS setelah melakukan pembayaran.</p>
    </div>
</body>
</html>
