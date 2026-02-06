<!DOCTYPE html>
<html>
<head>
    <title>Confirmation Letter - {{ $order->order_number }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .content { margin-bottom: 30px; }
        .footer { margin-top: 50px; }
        .signature-box { margin-top: 50px; width: 300px; border-top: 1px solid #000; text-align: center; }
        .details-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .details-table th, .details-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        .details-table th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SURAT KONFIRMASI LAYANAN</h1>
        <p>{{ \App\Models\Setting::get('company_name', 'PT. PGS Consulting & Training') }}</p>
    </div>

    <div class="content">
        <p>Kepada Yth,</p>
        <p><strong>{{ $order->user->name }}</strong></p>
        <p>Terima kasih telah memilih layanan kami. Berikut adalah rincian pendaftaran Anda:</p>

        <table class="details-table">
            <tr>
                <th>No. Order</th>
                <td>{{ $order->order_number }}</td>
            </tr>
            <tr>
                <th>Layanan</th>
                <td>{{ $order->service->name }}</td>
            </tr>
            <tr>
                <th>Kategori</th>
                <td>{{ ucfirst($order->service->category) }}</td>
            </tr>
            <tr>
                <th>Total Biaya</th>
                <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>

        <p>Mohon segera melakukan tanda tangan pada surat ini dan mengunggahnya kembali ke sistem kami untuk melanjutkan ke proses verifikasi dan pembayaran.</p>
    </div>

    <div class="footer">
        <p>Hormat kami,</p>
        <br><br>
        <p><strong>Management PGS</strong></p>
        
        <div style="float: right;">
            <p>Penerima / Customer</p>
            <div class="signature-box">
                <br><br><br>
                ( {{ $order->user->name }} )
            </div>
        </div>
    </div>
</body>
</html>
