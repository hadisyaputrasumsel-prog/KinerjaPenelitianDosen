@extends('layouts.app')

@section('content')
<div class="glass" style="padding: 2rem; margin-bottom: 2rem;">
    <h1 style="font-size: 1.8rem; margin-bottom: 0.5rem; color: var(--primary);">Laporan Akreditasi DTPS</h1>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Tabel standar 3.b.2 dan 3.b.4 untuk keperluan akreditasi program studi.</p>
</div>

<div class="glass" style="padding: 2rem; margin-bottom: 2rem;">
    <h2 style="font-size: 1.4rem; margin-bottom: 1.5rem; color: var(--primary);">Tabel 3.b.2 Penelitian DTPS</h2>
    <div style="overflow-x: auto;">
        <table class="accreditation-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 50px;">No.</th>
                    <th rowspan="2">Sumber Pembiayaan</th>
                    <th colspan="3" style="text-align: center;">Jumlah Judul Penelitian</th>
                    <th rowspan="2" style="text-align: center;">Jumlah</th>
                </tr>
                <tr>
                    <th style="text-align: center;">TS-2</th>
                    <th style="text-align: center;">TS-1</th>
                    <th style="text-align: center;">TS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($table_3b2 as $row)
                <tr>
                    <td>{{ $row['no'] }}</td>
                    <td>{!! nl2br(e($row['sumber'])) !!}</td>
                    <td style="text-align: center;">{{ $row['ts2'] ?: '' }}</td>
                    <td style="text-align: center;">{{ $row['ts1'] }}</td>
                    <td style="text-align: center;">{{ $row['ts'] }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $row['jumlah'] }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" style="text-align: center; font-weight: bold;">Jumlah</td>
                    <td style="text-align: center; font-weight: bold;">{{ collect($table_3b2)->sum('ts2') }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ collect($table_3b2)->sum('ts1') }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ collect($table_3b2)->sum('ts') }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ collect($table_3b2)->sum('jumlah') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="glass" style="padding: 2rem; margin-bottom: 2rem;">
    <h2 style="font-size: 1.4rem; margin-bottom: 1.5rem; color: var(--primary);">Tabel 3.b.4 Pengelaran/Pameran/presentasi/publikasi Ilmiah DTPS</h2>
    <div style="overflow-x: auto;">
        <table class="accreditation-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 50px;">No.</th>
                    <th rowspan="2">Jenis Publikasi</th>
                    <th colspan="3" style="text-align: center;">Jumlah Judul</th>
                    <th rowspan="2" style="text-align: center;">Jumlah</th>
                </tr>
                <tr>
                    <th style="text-align: center;">TS-2</th>
                    <th style="text-align: center;">TS-1</th>
                    <th style="text-align: center;">TS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($table_3b4 as $row)
                <tr>
                    <td>{{ $row['no'] }}</td>
                    <td>{{ $row['jenis'] }}</td>
                    <td style="text-align: center;">{{ $row['ts2'] ?: '0' }}</td>
                    <td style="text-align: center;">{{ $row['ts1'] }}</td>
                    <td style="text-align: center;">{{ $row['ts'] }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $row['jumlah'] }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="2" style="text-align: center; font-weight: bold;">Jumlah</td>
                    <td style="text-align: center; font-weight: bold;">{{ collect($table_3b4)->sum('ts2') }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ collect($table_3b4)->sum('ts1') }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ collect($table_3b4)->sum('ts') }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ collect($table_3b4)->sum('jumlah') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
    .accreditation-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
        background: rgba(255, 255, 255, 0.5);
        border-radius: 10px;
        overflow: hidden;
    }
    .accreditation-table th, .accreditation-table td {
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-glass);
        font-size: 0.9rem;
    }
    .accreditation-table th {
        background: var(--primary);
        color: white;
        font-weight: 600;
    }
    .accreditation-table tr:nth-child(even) {
        background: rgba(0, 0, 0, 0.02);
    }
    .accreditation-table tr:hover {
        background: rgba(0, 0, 0, 0.05);
    }
    .accreditation-table .total-row {
        background: rgba(215, 172, 124, 0.2) !important;
    }
</style>
@endsection
