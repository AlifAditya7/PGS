<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan PGS - {{ $summary['month'] }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .summary-table { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .summary-table td { padding: 8px; border: 1px solid #eee; }
        .summary-label { font-weight: bold; background-color: #f9fafb; width: 30%; }
        .detail-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .detail-table th, .detail-table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        .detail-table th { background-color: #f3f4f6; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 50px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 style="margin:0;">LAPORAN KEUANGAN BULANAN</h1>
        <p style="margin:5px 0;">PT. PGS Consulting Indonesia</p>
        <p style="margin:0; color: #666;">Periode: {{ $summary['month'] }}</p>
    </div>

    <h3>Ringkasan Eksekutif</h3>
    <table class="summary-table">
        <tr>
            <td class="summary-label">Total Pendapatan (Revenue)</td>
            <td class="text-right font-bold" style="color: #2563eb;">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="summary-label">Total Biaya Operasional (COGS)</td>
            <td class="text-right font-bold" style="color: #dc2626;">Rp {{ number_format($summary['total_cogs'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="summary-label">Laba Bersih (Net Profit)</td>
            <td class="text-right font-bold" style="color: #16a34a; font-size: 14px;">Rp {{ number_format($summary['total_profit'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <h3>Rincian Transaksi</h3>
    <table class="detail-table">
        <thead>
            <tr>
                <th>No. Order</th>
                <th>Customer</th>
                <th>Layanan</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">COGS</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($finances as $finance)
            <tr>
                <td>{{ $finance->order->order_number }}</td>
                <td>{{ $finance->order->user->name }}</td>
                <td>{{ $finance->order->service->name }}</td>
                <td class="text-right">Rp {{ number_format($finance->revenue, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($finance->cogs, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($finance->net_profit, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d F Y, H:i') }}</p>
        <br><br><br>
        <p><strong>Finance Department PGS</strong></p>
    </div>
</body>
</html>
