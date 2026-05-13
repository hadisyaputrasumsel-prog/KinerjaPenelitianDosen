@extends('layouts.app')

@section('content')
<div class="search-container" style="display: flex; gap: 1rem; align-items: center; margin-bottom: 2rem;">
    <div style="position: relative; flex: 1;">
        <i class="fas fa-search" style="position: absolute; left: 1.5rem; top: 1.1rem; color: var(--text-muted);"></i>
        <input type="text" class="search-input" placeholder="Cari nama dosen atau program studi..." id="lecturer-search" style="width: 100%; padding: 1rem; padding-left: 3.5rem; border-radius: 12px; border: 1px solid var(--border-glass);">
    </div>
</div>

<div class="lecturer-grid" id="lecturer-list">
    @foreach($lecturers as $l)
        <div class="lecturer-card glass" data-id="{{ $l['id'] }}">
            <div class="lecturer-header">
                <div class="avatar">{{ substr($l['name'], 0, 1) }}</div>
                <div class="lecturer-info">
                    <h3>{{ $l['name'] }}</h3>
                    <p>{{ $l['prodi'] }}</p>
                    <div class="sinta-id-badge" style="display: none; margin-top: 0.25rem;">
                        <span class="pill" style="background: var(--secondary); color: var(--primary); font-weight: 700; font-size: 0.65rem; border: none; padding: 0.2rem 0.5rem;">SINTA ID: <span class="sinta-id-val"></span></span>
                    </div>
                    <div class="status-badge" style="display: none; margin-top: 0.25rem;">
                        <span class="pill status-val" style="font-weight: 700; font-size: 0.65rem; border: none; padding: 0.2rem 0.5rem; color: white;"></span>
                    </div>
                </div>
            </div>
            <div class="performance-pills">
                <div class="pill">SINTA Overall: {{ $l['sintaOverall'] }}</div>
                <div class="pill">SINTA 3Yr: {{ $l['sinta3Yr'] }}</div>
                <div class="pill">Scholar: {{ $l['scholar'] }}</div>
                <div class="pill">Scopus: {{ $l['scopus'] }}</div>
            </div>
            <button onclick='window.showLecturerDetail(@json($l))' class="glass glass-hover" style="margin-top: auto; padding: 0.5rem; background: var(--primary); color: white; border: none; border-radius: 8px; font-size: 0.8rem; cursor: pointer; font-weight: 600;">Lihat Detail</button>
        </div>
    @endforeach
</div>

<style>
    .lecturer-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
    .lecturer-card { padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; }
    .lecturer-header { display: flex; align-items: center; gap: 1rem; }
    .avatar { width: 50px; height: 50px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; }
    .performance-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }
    .pill { padding: 0.25rem 0.75rem; border-radius: 20px; font-size: 0.7rem; background: rgba(0, 0, 0, 0.05); border: 1px solid var(--border-glass); }
</style>

<script>
    document.getElementById('lecturer-search').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.lecturer-card');
        cards.forEach(card => {
            const name = card.querySelector('h3').innerText.toLowerCase();
            const prodi = card.querySelector('p').innerText.toLowerCase();
            if (name.includes(query) || prodi.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Read SINTA IDs and Statuses from localStorage and show on cards
    const savedSintaIds = JSON.parse(localStorage.getItem('sintaIds') || '{}');
    const savedStatuses = JSON.parse(localStorage.getItem('lecturerStatuses') || '{}');
    
    document.querySelectorAll('.lecturer-card').forEach(card => {
        const id = card.dataset.id;
        
        // Handle SINTA ID
        if (savedSintaIds[id]) {
            const badge = card.querySelector('.sinta-id-badge');
            const val = card.querySelector('.sinta-id-val');
            val.innerText = savedSintaIds[id];
            badge.style.display = 'block';
        }
        
        // Handle Status
        const status = savedStatuses[id] || 'Aktif';
        const statusBadge = card.querySelector('.status-badge');
        const statusVal = card.querySelector('.status-val');
        
        statusVal.innerText = status;
        statusVal.style.background = status === 'Aktif' ? '#22c55e' : (status === 'Pensiun' ? '#f59e0b' : '#ef4444');
        statusBadge.style.display = 'block';
    });
</script>
@endsection
