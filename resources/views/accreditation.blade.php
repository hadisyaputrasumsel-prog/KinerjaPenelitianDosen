@extends('layouts.app')

@section('content')
<div class="glass" style="padding: 2rem; margin-bottom: 2rem;">
    <h1 style="font-size: 1.8rem; margin-bottom: 1.5rem; color: var(--primary);">Laporan Akreditasi DTPS</h1>
    
    <form method="GET" action="{{ route('accreditation') }}" style="display: flex; gap: 1rem; align-items: flex-end;">
        <div style="flex: 1; max-width: 400px;">
            <label for="prodi" style="display: block; font-size: 0.9rem; color: var(--text-muted); margin-bottom: 0.5rem;">Filter Berdasarkan Program Studi:</label>
            <select name="prodi" id="prodi" style="width: 100%; padding: 0.8rem; border-radius: 8px; border: 1px solid var(--border-glass); background: white; font-family: 'Inter', sans-serif;">
                <option value="">-- Semua Program Studi --</option>
                @foreach($prodis as $p)
                    <option value="{{ $p }}" {{ $selectedProdi == $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="glass glass-hover" style="background: var(--primary); color: white; border: none; padding: 0.8rem 1.5rem; border-radius: 8px; cursor: pointer; font-weight: 600;">
            <i class="fas fa-filter"></i> Terapkan Filter
        </button>
        @if($selectedProdi)
            <a href="{{ route('accreditation') }}" style="padding: 0.8rem 1.5rem; color: #ef4444; text-decoration: none; font-weight: 600; font-size: 0.9rem;"><i class="fas fa-times"></i> Hapus Filter</a>
        @endif
    </form>
</div>

<div class="glass" style="padding: 2rem; margin-bottom: 2rem;">
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
                    <td style="text-align: center;">
                        @if($row['ts2'] > 0)
                            <a href="#" class="clickable-number" data-items="{{ json_encode($row['ts2_items']) }}" data-title="Penelitian TS-2">{{ $row['ts2'] }}</a>
                        @else
                            {{ $row['ts2'] ?: '' }}
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($row['ts1'] > 0)
                            <a href="#" class="clickable-number" data-items="{{ json_encode($row['ts1_items']) }}" data-title="Penelitian TS-1">{{ $row['ts1'] }}</a>
                        @else
                            {{ $row['ts1'] }}
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($row['ts'] > 0)
                            <a href="#" class="clickable-number" data-items="{{ json_encode($row['ts_items']) }}" data-title="Penelitian TS">{{ $row['ts'] }}</a>
                        @else
                            {{ $row['ts'] }}
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        @if($row['jumlah'] > 0)
                            <a href="#" class="clickable-number" data-items="{{ json_encode($row['jumlah_items']) }}" data-title="Total Penelitian">{{ $row['jumlah'] }}</a>
                        @else
                            {{ $row['jumlah'] }}
                        @endif
                    </td>
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
                    <td style="text-align: center;">
                        @if($row['ts2'] > 0)
                            <a href="#" class="clickable-number" data-items="{{ json_encode($row['ts2_items']) }}" data-title="Publikasi TS-2">{{ $row['ts2'] }}</a>
                        @else
                            {{ $row['ts2'] ?: '0' }}
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($row['ts1'] > 0)
                            <a href="#" class="clickable-number" data-items="{{ json_encode($row['ts1_items']) }}" data-title="Publikasi TS-1">{{ $row['ts1'] }}</a>
                        @else
                            {{ $row['ts1'] }}
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($row['ts'] > 0)
                            <a href="#" class="clickable-number" data-items="{{ json_encode($row['ts_items']) }}" data-title="Publikasi TS">{{ $row['ts'] }}</a>
                        @else
                            {{ $row['ts'] }}
                        @endif
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        @if($row['jumlah'] > 0)
                            <a href="#" class="clickable-number" data-items="{{ json_encode($row['jumlah_items']) }}" data-title="Total Publikasi">{{ $row['jumlah'] }}</a>
                        @else
                            {{ $row['jumlah'] }}
                        @endif
                    </td>
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

    /* Modal Styles */
    .clickable-number {
        color: var(--primary);
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
    }
    .clickable-number:hover {
        text-decoration: underline;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
        backdrop-filter: blur(5px);
    }
    .modal-content {
        margin: 10% auto;
        padding: 2rem;
        width: 60%;
        max-width: 600px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 15px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--border-glass);
        padding-bottom: 1rem;
        margin-bottom: 1rem;
    }
    .modal-header h3 {
        margin: 0;
        color: var(--primary);
    }
    .close-btn {
        font-size: 1.5rem;
        font-weight: bold;
        cursor: pointer;
        color: var(--text-muted);
    }
    .close-btn:hover {
        color: var(--primary);
    }
    .modal-body ul {
        list-style: none;
        padding: 0;
    }
    .modal-body li {
        padding: 0.75rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .modal-body li:last-child {
        border-bottom: none;
    }
    .article-title {
        font-weight: 500;
        color: var(--text);
    }
    .article-link {
        color: var(--primary);
        text-decoration: none;
        font-weight: 600;
    }
    .article-link:hover {
        text-decoration: underline;
    }
</style>

<!-- Modal -->
<div id="articleModal" class="modal">
    <div class="modal-content glass">
        <div class="modal-header">
            <h3 id="modalTitle">Daftar Artikel</h3>
            <span class="close-btn" onclick="closeModal()">&times;</span>
        </div>
        <div class="modal-body">
            <ul id="articleList"></ul>
        </div>
    </div>
</div>

<script>
    function openModal(title, items) {
        document.getElementById('modalTitle').innerText = title;
        const list = document.getElementById('articleList');
        list.innerHTML = '';
        
        items.forEach(item => {
            const li = document.createElement('li');
            
            const titleSpan = document.createElement('span');
            titleSpan.className = 'article-title';
            titleSpan.innerText = item.title;
            
            const actionSpan = document.createElement('span');
            const a = document.createElement('a');
            a.href = `https://scholar.google.com/scholar?q=${encodeURIComponent(item.title)}`;
            a.target = '_blank';
            a.className = 'article-link';
            a.innerText = 'Cari di Scholar';
            
            actionSpan.appendChild(a);
            
            li.appendChild(titleSpan);
            li.appendChild(actionSpan);
            list.appendChild(li);
        });
        
        document.getElementById('articleModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('articleModal').style.display = 'none';
    }

    // Close on click outside
    window.onclick = function(event) {
        const modal = document.getElementById('articleModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    // Attach event listeners to all clickable numbers
    document.addEventListener('DOMContentLoaded', function() {
        const links = document.querySelectorAll('.clickable-number');
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const title = this.getAttribute('data-title');
                const items = JSON.parse(this.getAttribute('data-items'));
                openModal(title, items);
            });
        });
    });
</script>
@endsection
