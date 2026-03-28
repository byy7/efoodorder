<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pesanan</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a1a;
        }

        /* ── Header ─────────────────────────────── */
        .report-header {
            background: #2D6A4F;
            color: #fff;
            padding: 18px 24px;
            margin-bottom: 20px;
        }
        .report-header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .report-header p { font-size: 11px; opacity: 0.85; }

        /* ── Meta row ────────────────────────────── */
        .meta-grid {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            border-collapse: collapse;
        }
        .meta-cell {
            display: table-cell;
            width: 25%;
            padding: 8px 12px;
            background: #f5f5f5;
            border: 1px solid #e0e0e0;
            vertical-align: top;
        }
        .meta-cell .label  { color: #666; font-size: 9px; text-transform: uppercase; margin-bottom: 2px; }
        .meta-cell .value  { font-weight: bold; font-size: 12px; }

        /* ── Summary cards ───────────────────────── */
        .summary-row {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 6px;
            margin-bottom: 18px;
        }
        .summary-card {
            display: table-cell;
            width: 25%;
            padding: 10px 12px;
            border-radius: 4px;
            vertical-align: middle;
        }
        .summary-card .s-label { font-size: 9px; text-transform: uppercase; margin-bottom: 3px; }
        .summary-card .s-value { font-size: 16px; font-weight: bold; }
        .card-pending   { background: #FFF3CD; color: #856404; border-left: 3px solid #FFC107; }
        .card-confirmed { background: #D1E7DD; color: #0A3622; border-left: 3px solid #198754; }
        .card-cancelled { background: #F8D7DA; color: #58151C; border-left: 3px solid #DC3545; }
        .card-revenue   { background: #CCE5FF; color: #004085; border-left: 3px solid #0D6EFD; }

        /* ── Table ───────────────────────────────── */
        table.orders {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.orders thead tr {
            background: #2D6A4F;
            color: #fff;
        }
        table.orders th {
            padding: 7px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.orders td {
            padding: 6px 8px;
            border-bottom: 1px solid #e8e8e8;
            vertical-align: middle;
        }
        table.orders tbody tr:nth-child(even) { background: #f9f9f9; }

        /* status badges */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-warning  { background:#FFF3CD; color:#856404; }
        .badge-success  { background:#D1E7DD; color:#0A3622; }
        .badge-danger   { background:#F8D7DA; color:#58151C; }
        .badge-info     { background:#CCE5FF; color:#004085; }
        .badge-secondary{ background:#E9ECEF; color:#495057; }

        /* ── Totals footer ───────────────────────── */
        .totals-row td {
            font-weight: bold;
            background: #E8F5E9;
            border-top: 2px solid #2D6A4F;
        }

        /* ── Footer ──────────────────────────────── */
        .report-footer {
            font-size: 9px;
            color: #888;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            padding-top: 8px;
            margin-top: 8px;
        }
    </style>
</head>
<body>

{{-- Header --}}
<div class="report-header">
    <h1>Laporan Pesanan</h1>
    <p>{{ config('app.name') }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y, H:i') }}</p>
</div>

{{-- Meta: filter info --}}
<div class="meta-grid">
    <div class="meta-cell">
        <div class="label">Periode</div>
        <div class="value">
            {{ \Carbon\Carbon::parse($dateStart)->format('d M Y') }}
            &ndash;
            {{ \Carbon\Carbon::parse($dateEnd)->format('d M Y') }}
        </div>
    </div>
    <div class="meta-cell">
        <div class="label">Status Pesanan</div>
        <div class="value">{{ $statusLabel }}</div>
    </div>
    <div class="meta-cell">
        <div class="label">Metode Pembayaran</div>
        <div class="value">{{ $paymentMethodLabel }}</div>
    </div>
    <div class="meta-cell">
        <div class="label">Total Data</div>
        <div class="value">{{ $orders->count() }} pesanan</div>
    </div>
</div>

{{-- Summary cards --}}
<div class="summary-row">
    <div class="summary-card card-pending">
        <div class="s-label">Diproses</div>
        <div class="s-value">{{ $summary['pending'] }}</div>
    </div>
    <div class="summary-card card-confirmed">
        <div class="s-label">Selesai</div>
        <div class="s-value">{{ $summary['confirmed'] }}</div>
    </div>
    <div class="summary-card card-cancelled">
        <div class="s-label">Dibatalkan</div>
        <div class="s-value">{{ $summary['cancelled'] }}</div>
    </div>
    <div class="summary-card card-revenue">
        <div class="s-label">Total Pendapatan</div>
        <div class="s-value">Rp.{{ number_format($summary['revenue'], 2, ',', '.') }}</div>
    </div>
</div>

{{-- Orders table --}}
<table class="orders">
    <thead>
    <tr>
        <th style="width:30px">No</th>
        <th>No. Pesanan</th>
        <th>Tanggal</th>
        <th>Pelanggan</th>
        <th>Tipe</th>
        <th>Pembayaran</th>
        <th>Status</th>
        <th style="text-align:right">Total</th>
    </tr>
    </thead>
    <tbody>
    @forelse($orders as $i => $order)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $order->order_number }}</td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $order->customer?->name ?? '-' }}</td>
            <td>{{ $order->type === 'dine_in' ? 'Dine In' : 'Takeaway' }}</td>
            <td>
                        <span class="badge {{ $order->payment_method === 'cash' ? 'badge-secondary' : 'badge-info' }}">
                            {{ $order->payment_method === 'cash' ? 'Tunai' : 'Non-Tunai' }}
                        </span>
                &nbsp;
                <span class="badge {{ $order->payment_status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                            {{ $order->payment_status === 'completed' ? 'Lunas' : 'Belum' }}
                        </span>
            </td>
            <td>
                @php
                    $badges = [
                        'pending'   => ['badge-warning',   'Diproses'],
                        'confirmed' => ['badge-success',   'Selesai'],
                        'completed' => ['badge-success',   'Selesai'],
                        'cancelled' => ['badge-danger',    'Dibatalkan'],
                    ];
                    [$cls, $lbl] = $badges[$order->status] ?? ['badge-secondary', $order->status];
                @endphp
                <span class="badge {{ $cls }}">{{ $lbl }}</span>
            </td>
            <td style="text-align:right">Rp {{ number_format($order->amount, 0, ',', '.') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="8" style="text-align:center; padding:20px; color:#999;">
                Tidak ada data pesanan pada periode ini
            </td>
        </tr>
    @endforelse
    </tbody>
    @if($orders->isNotEmpty())
        <tfoot>
        <tr class="totals-row">
            <td colspan="7" style="text-align:right">TOTAL PENDAPATAN</td>
            <td style="text-align:right">
                Rp {{ number_format($orders->sum('amount'), 0, ',', '.') }}
            </td>
        </tr>
        </tfoot>
    @endif
</table>

<div class="report-footer">
    Laporan ini digenerate otomatis oleh sistem &nbsp;&bull;&nbsp; {{ config('app.name') }} &nbsp;&bull;&nbsp; {{ now()->format('d M Y H:i:s') }}
</div>

</body>
</html>
