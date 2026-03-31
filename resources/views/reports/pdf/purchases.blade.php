{{-- resources/views/reports/pdf/purchases.blade.php --}}
@extends('reports.pdf.layout')

@section('content')

@php
  $total    = $data->sum('total');
  $paid     = $data->sum('paid');
  $due      = $data->sum('due');
  $totalTrx = $data->count();
@endphp

<div class="summary-grid">
  <div class="summary-card">
    <div class="label">Total Transaksi</div>
    <div class="value">{{ number_format($totalTrx) }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Total Pembelian</div>
    <div class="value">Rp {{ number_format($total, 0, ',', '.') }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Terbayar</div>
    <div class="value">Rp {{ number_format($paid, 0, ',', '.') }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Sisa Hutang</div>
    <div class="value" style="color: {{ $due > 0 ? '#dc2626' : '#16a34a' }}">
      Rp {{ number_format($due, 0, ',', '.') }}
    </div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Tanggal</th>
      <th>No. Referensi</th>
      <th>Supplier</th>
      <th>Status PO</th>
      <th>Status Bayar</th>
      <th class="center">Item</th>
      <th class="right">Total (Rp)</th>
      <th class="right">Terbayar (Rp)</th>
      <th class="right">Hutang (Rp)</th>
    </tr>
  </thead>
  <tbody>
    @forelse($data as $i => $row)
    @php
      $statusColor = match($row->status) {
        'received' => 'badge-success',
        'ordered'  => 'badge-info',
        'partial'  => 'badge-warning',
        default    => 'badge-gray',
      };
      $payColor = match($row->payment_status) {
        'paid'    => 'badge-success',
        'partial' => 'badge-warning',
        'unpaid'  => 'badge-danger',
        default   => 'badge-gray',
      };
    @endphp
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $row->purchase_date?->format('d/m/Y') }}</td>
      <td class="mono">{{ $row->reference_number }}</td>
      <td>{{ $row->supplier?->name ?? '-' }}</td>
      <td><span class="badge {{ $statusColor }}">{{ strtoupper($row->status) }}</span></td>
      <td><span class="badge {{ $payColor }}">{{ strtoupper($row->payment_status) }}</span></td>
      <td class="center">{{ $row->items->count() }}</td>
      <td class="right">{{ number_format($row->total, 0, ',', '.') }}</td>
      <td class="right">{{ number_format($row->paid, 0, ',', '.') }}</td>
      <td class="right" style="{{ $row->due > 0 ? 'color:#dc2626' : '' }}">
        {{ number_format($row->due, 0, ',', '.') }}
      </td>
    </tr>
    @empty
    <tr><td colspan="10" class="center">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
  <tfoot>
    <tr>
      <td colspan="7" class="right">TOTAL</td>
      <td class="right">{{ number_format($total, 0, ',', '.') }}</td>
      <td class="right">{{ number_format($paid, 0, ',', '.') }}</td>
      <td class="right">{{ number_format($due, 0, ',', '.') }}</td>
    </tr>
  </tfoot>
</table>

@endsection