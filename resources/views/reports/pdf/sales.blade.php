{{-- resources/views/reports/pdf/sales.blade.php --}}
@extends('reports.pdf.layout')

@section('content')

@php
  $totalRevenue = $data->sum('total');
  $totalTrx     = $data->count();
  $avgTrx       = $totalTrx > 0 ? $totalRevenue / $totalTrx : 0;

  $byMethod = $data->groupBy('payment_method');
@endphp

{{-- Summary --}}
<div class="summary-grid">
  <div class="summary-card">
    <div class="label">Total Transaksi</div>
    <div class="value">{{ number_format($totalTrx) }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Total Omzet</div>
    <div class="value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Rata-rata Transaksi</div>
    <div class="value">Rp {{ number_format($avgTrx, 0, ',', '.') }}</div>
  </div>
  @foreach($byMethod as $method => $items)
  <div class="summary-card">
    <div class="label">{{ strtoupper($method) }}</div>
    <div class="value">{{ $items->count() }}x</div>
  </div>
  @endforeach
</div>

{{-- Table --}}
<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Tanggal</th>
      <th>No. Invoice</th>
      <th>Pelanggan</th>
      <th>Kasir</th>
      <th>Metode</th>
      <th class="center">Item</th>
      <th class="right">Total (Rp)</th>
    </tr>
  </thead>
  <tbody>
    @forelse($data as $i => $row)
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $row->created_at?->format('d/m/Y H:i') }}</td>
      <td class="mono">{{ $row->invoice_number }}</td>
      <td>{{ $row->customer?->name ?? 'Walk-in' }}</td>
      <td>{{ $row->user?->name }}</td>
      <td>
        <span class="badge badge-gray">{{ strtoupper($row->payment_method) }}</span>
      </td>
      <td class="center">{{ $row->items_count ?? $row->items->count() }}</td>
      <td class="right">{{ number_format($row->total, 0, ',', '.') }}</td>
    </tr>
    @empty
    <tr><td colspan="8" class="center">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
  <tfoot>
    <tr>
      <td colspan="7" class="right">TOTAL</td>
      <td class="right">{{ number_format($totalRevenue, 0, ',', '.') }}</td>
    </tr>
  </tfoot>
</table>

@endsection