<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { font-family: DejaVu Sans, Helvetica, Arial, sans-serif; }
        body { color: #1f2937; margin: 0; padding: 24px; font-size: 11px; }
        .head { display: flex; align-items: center; justify-content: space-between; border-bottom: 3px solid #16a34a; padding-bottom: 12px; margin-bottom: 4px; }
        .brand { font-size: 18px; font-weight: bold; color: #064e3b; }
        .brand span { color: #16a34a; }
        .meta { text-align: right; font-size: 10px; color: #6b7280; line-height: 1.5; }
        h1 { font-size: 14px; margin: 14px 0 2px; color: #064e3b; }
        .period { font-size: 10px; color: #6b7280; margin-bottom: 12px; }
        .badge-in, .badge-out { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-in { background: #dcfce7; color: #15803d; }
        .badge-out { background: #fee2e2; color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead th { background: #16a34a; color: #fff; font-size: 10px; padding: 8px 10px; text-align: left; border: 1px solid #0d7a3f; }
        tbody td { padding: 7px 10px; border: 1px solid #e5e7eb; }
        tbody tr:nth-child(even) td { background: #f8faf9; }
        .num { text-align: right; font-weight: bold; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e5e7eb; font-size: 9px; color: #9ca3af; display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="head">
        <div class="brand">In-Out <span>Vendor</span></div>
        <div class="meta">
            Dibuat: {{ now()->format('d/m/Y H:i') }}<br>
            in-out-vendor
        </div>
    </div>
    <h1>{{ $title }}</h1>
    @if($period)<div class="period">{{ $period }}</div>@endif
    <table>
        <thead>
            <tr>
                @foreach($headers as $i => $header)
                    <th {{ ($i >= 3 && in_array($header, ['Qty', 'Qty Sebelum', 'Qty Sesudah'], true)) ? 'style="text-align:right"' : '' }}>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $i => $cell)
                        @php $isBadge = $i === 1; $isNum = is_float($cell); @endphp
                        <td class="{{ $isNum ? 'num' : '' }}">
                            @if($isBadge)
                                <span class="{{ $cell === 'Masuk' ? 'badge-in' : 'badge-out' }}">{{ $cell === 'Masuk' ? 'Barang Masuk' : 'Barang Keluar' }}</span>
                            @elseif($isNum)
                                {{ number_format($cell, 2) }}
                            @else
                                {{ $cell }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($headers) }}" style="text-align:center;color:#9ca3af;padding:20px">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">
        <span>Total data: {{ count($rows) }} baris</span>
        <span>&copy; 2026 In-Out Vendor</span>
    </div>
</body>
</html>
