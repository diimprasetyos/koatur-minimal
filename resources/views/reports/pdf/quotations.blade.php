{{-- resources/views/reports/pdf/quotations.blade.php --}}
@extends('reports.pdf.layout')

@section('content')

@php
  $total      = $data->count();
  $accepted   = $data->where('status', 'accepted')->count();
  $totalValue = $data->whereNotIn('status', ['rejected','expired'])->sum('total_amount');
  $convRate   = $total > 0 ? round(($accepted / $total) * 100, 1) : 0;
@endphp

<div class="summary-grid">
  <div class="summary-card">
    <div class="label">Total Penawaran</div>
    <div class="value">{{ $total }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Diterima</div>
    <div class="value" style="color:#16a34a">{{ $accepted }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Conversion Rate</div>
    <div class="value">{{ $convRate }}%</div>
  </div>
  <div class="summary-card">
    <div class="label">Nilai Potensial</div>
    <div class="value">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Tanggal</th>
      <th>No. Penawaran</th>
      <th>Pelanggan</th>
      <th>Dibuat Oleh</th>
      <th>Berlaku Sampai</th>
      <th>Status</th>
      <th class="center">Item</th>
      <th class="right">Total (Rp)</th>
    </tr>
  </thead>
  <tbody>
    @forelse($data as $i => $row)
    @php
      $statusColor = match($row->status) {
        'accepted' => 'badge-success',
        'sent'     => 'badge-info',
        'rejected' => 'badge-danger',
        'expired'  => 'badge-warning',
        default    => 'badge-gray',
      };
    @endphp
    <tr>
      <td>{{ $i + 1 }}</td>
      <td>{{ $row->created_at?->format('d/m/Y') }}</td>
      <td class="mono">{{ $row->code }}</td>
      <td>{{ $row->customer?->name ?? '-' }}</td>
      <td>{{ $row->user?->name }}</td>
      <td>{{ $row->valid_until?->format('d/m/Y') ?? '-' }}</td>
      <td><span class="badge {{ $statusColor }}">{{ strtoupper($row->status) }}</span></td>
      <td class="center">{{ $row->items->count() }}</td>
      <td class="right">{{ number_format($row->total_amount, 0, ',', '.') }}</td>
    </tr>
    @empty
    <tr><td colspan="9" class="center">Tidak ada data.</td></tr>
    @endforelse
  </tbody>
  <tfoot>
    <tr>
      <td colspan="8" class="right">TOTAL NILAI</td>
      <td class="right">{{ number_format($totalValue, 0, ',', '.') }}</td>
    </tr>
  </tfoot>
</table>

@endsection