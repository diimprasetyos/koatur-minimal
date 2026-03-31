{{-- resources/views/reports/pdf/purchase_return.blade.php --}}
@extends('reports.pdf.layout')

@section('content')

@php
  $total    = $data->sum('total_return');
  $approved = $data->where('status', 'approved')->count();
  $pending  = $data->where('status', 'pending')->count();
  $rejected = $data->where('status', 'rejected')->count();
@endphp

<div class="summary-grid">
  <div class="summary-card">
    <div class="label">Total Transaksi</div>
    <div class="value">{{ $data->count() }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Total Retur</div>
    <div class="value">Rp {{ number_format($total, 0, ',', '.') }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Approved</div>
    <div class="value" style="color:#16a34a">{{ $approved }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Pending</div>
    <div class="value" style="color:#d97706">{{ $pending }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Rejected</div>
    <div class="value" style="color:#dc2626">{{ $rejected }}</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Tanggal</th>
      <th>No. Retur</th>
      <th>No. PO Asal</th>
      <th>Supplier</th>
      <th>Diproses Oleh</th>
      <th>Metode Return</th>
      <th>Status</th>
      <th class="center">Item</th>
      <th class="right">Total Retur (Rp)</th>
    </tr>
  </thead>
  <tbody>
    @forelse($data as $i => $row)
    @php
      $statusColor = match($row->status) {
        'approved' => 'badge-success',
        'pending'  => 'badge-warning',
        'rejected' => 'badge-danger',
        default    => 'badge-gray',
      };
    @endphp
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $row->return_date?->format('d/m/Y') }}</td>
      <td class="mono">{{ $row->reference_number }}</td>
      <td class="mono">{{ $row->purchase?->reference_number ?? '-' }}</td>
      <td>{{ $row->supplier?->name ?? '-' }}</td>
      <td>{{ $row->user?->name }}</td>
      <td>{{ $row->return_method ?? '-' }}</td>
      <td><span class="badge {{ $statusColor }}">{{ strtoupper($row->status) }}</span></td>
      <td class="center">{{ $row->items->count() }}</td>
      <td class="right">{{ number_format($row->total_return, 0, ',', '.') }}</td>
    </tr>
    @empty
    <tr><td colspan="10" class="center">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
  <tfoot>
    <tr>
      <td colspan="9" class="right">TOTAL RETUR</td>
      <td class="right">{{ number_format($total, 0, ',', '.') }}</td>
    </tr>
  </tfoot>
</table>

@endsection