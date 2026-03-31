{{-- resources/views/reports/pdf/stock.blade.php --}}
@extends('reports.pdf.layout')

@section('content')

@php
  $tracked    = $data->where('track_stock', true);
  $outOfStock = $tracked->where('stock', '<=', 0)->count();
  $lowStock   = $tracked->where('stock', '>', 0)->where('stock', '<=', 5)->count();
  $totalValue = $tracked->sum(fn($p) => $p->stock * $p->cost_price);
@endphp

<div class="summary-grid">
  <div class="summary-card">
    <div class="label">Total Produk</div>
    <div class="value">{{ number_format($data->count()) }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Stok Habis</div>
    <div class="value" style="color:#dc2626">{{ $outOfStock }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Hampir Habis (≤5)</div>
    <div class="value" style="color:#d97706">{{ $lowStock }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Total Nilai Stok</div>
    <div class="value">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Produk</th>
      <th>SKU</th>
      <th>Kategori</th>
      <th class="center">Stok</th>
      <th class="right">Harga Jual (Rp)</th>
      <th class="right">Modal (Rp)</th>
      <th class="right">Nilai Stok (Rp)</th>
    </tr>
  </thead>
  <tbody>
    @forelse($data as $i => $row)
    @php
      $stockColor = match(true) {
        !$row->track_stock       => '',
        $row->stock <= 0         => 'color:#dc2626;font-weight:700',
        $row->stock <= 5         => 'color:#d97706;font-weight:700',
        default                  => 'color:#16a34a',
      };
      $stockLabel = $row->track_stock ? $row->stock : '∞';
    @endphp
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $row->name }}</td>
      <td class="mono">{{ $row->sku ?? '—' }}</td>
      <td>{{ $row->category?->name ?? '—' }}</td>
      <td class="center" style="{{ $stockColor }}">{{ $stockLabel }}</td>
      <td class="right">{{ number_format($row->price, 0, ',', '.') }}</td>
      <td class="right">{{ number_format($row->cost_price, 0, ',', '.') }}</td>
      <td class="right">
        {{ $row->track_stock ? number_format($row->stock * $row->cost_price, 0, ',', '.') : '—' }}
      </td>
    </tr>
    @empty
    <tr><td colspan="8" class="center">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
  <tfoot>
    <tr>
      <td colspan="7" class="right">TOTAL NILAI STOK</td>
      <td class="right">{{ number_format($totalValue, 0, ',', '.') }}</td>
    </tr>
  </tfoot>
</table>

@endsection