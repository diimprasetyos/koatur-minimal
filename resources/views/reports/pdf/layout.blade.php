{{-- resources/views/reports/pdf/layout.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 10px;
    color: #1a1a1a;
    background: #fff;
  }

  .page-header {
    border-bottom: 2px solid #1e3a5f;
    padding-bottom: 8px;
    margin-bottom: 14px;
  }

  .page-header h1 {
    font-size: 16px;
    color: #1e3a5f;
    font-weight: 700;
  }

  .page-header .meta {
    font-size: 9px;
    color: #666;
    margin-top: 3px;
  }

  .page-header .meta span {
    margin-right: 16px;
  }

  /* Summary cards */
  .summary-grid {
    display: flex;
    gap: 8px;
    margin-bottom: 14px;
    flex-wrap: wrap;
  }

  .summary-card {
    flex: 1;
    min-width: 100px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 10px;
    background: #f8fafc;
  }

  .summary-card .label {
    font-size: 8px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
  }

  .summary-card .value {
    font-size: 13px;
    font-weight: 700;
    color: #1e3a5f;
    margin-top: 2px;
  }

  /* Table */
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 9px;
  }

  thead th {
    background: #1e3a5f;
    color: #fff;
    padding: 6px 7px;
    text-align: left;
    font-weight: 600;
    white-space: nowrap;
  }

  thead th.right { text-align: right; }
  thead th.center { text-align: center; }

  tbody tr:nth-child(even) { background: #f8fafc; }

  tbody td {
    padding: 5px 7px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: top;
  }

  tbody td.right  { text-align: right; }
  tbody td.center { text-align: center; }
  tbody td.mono   { font-family: 'DejaVu Sans Mono', monospace; font-size: 8.5px; }

  /* Badge-like status */
  .badge {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 8px;
    font-weight: 600;
  }
  .badge-success  { background: #dcfce7; color: #166534; }
  .badge-warning  { background: #fef9c3; color: #854d0e; }
  .badge-danger   { background: #fee2e2; color: #991b1b; }
  .badge-info     { background: #dbeafe; color: #1e40af; }
  .badge-gray     { background: #f1f5f9; color: #475569; }

  /* Footer totals row */
  tfoot td {
    padding: 6px 7px;
    font-weight: 700;
    font-size: 9.5px;
    background: #eef2ff;
    border-top: 2px solid #1e3a5f;
  }
  tfoot td.right { text-align: right; }

  .page-footer {
    margin-top: 14px;
    border-top: 1px solid #e2e8f0;
    padding-top: 6px;
    font-size: 8px;
    color: #94a3b8;
    display: flex;
    justify-content: space-between;
  }
</style>
</head>
<body>

{{-- Header --}}
<div class="page-header">
  <h1>{{ $title }}</h1>
  <div class="meta">
    <span>🏪 {{ $tenant->name }}</span>
    <span>📅 Periode: {{ $dateInfo }}</span>
    <span>🖨️ Dicetak: {{ $printedAt }}</span>
  </div>
</div>

@yield('content')

<div class="page-footer">
  <span>{{ $tenant->name }} — {{ $title }}</span>
  <span>Dicetak {{ $printedAt }}</span>
</div>

</body>
</html>