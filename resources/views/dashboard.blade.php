@extends('layouts.app')

@section('content')
<div class="stats-grid">
    <div class="stat-card glass">
        <div class="stat-label">Total Dosen</div>
        <div class="stat-value">{{ $stats['totalLecturers'] }}</div>
        <div style="font-size: 0.7rem; color: #4ade80;"><i class="fas fa-arrow-up"></i> Terverifikasi PDDikti</div>
    </div>
    <div class="stat-card glass">
        <div class="stat-label">Output Penelitian</div>
        <div class="stat-value">{{ $stats['totalResearch'] }}</div>
        <div style="font-size: 0.7rem; color: #4ade80;"><i class="fas fa-chart-line"></i> Akumulasi 3 Tahun</div>
    </div>
    <div class="stat-card glass">
        <div class="stat-label">Rerata SINTA Score</div>
        <div class="stat-value">{{ $stats['avgSinta'] }} <span style="font-size:1rem;color:var(--text-muted)">/ {{ $stats['avgSinta3Yr'] }}</span></div>
        <div style="font-size: 0.7rem; color: var(--primary);">Status: Overall / 3Yr</div>
    </div>
    <div class="stat-card glass">
        <div class="stat-label">Rasio Produktivitas</div>
        <div class="stat-value">{{ $stats['productivityRatio'] }}%</div>
        <div style="font-size: 0.7rem; color: var(--primary);">{{ $stats['productiveCount'] }} Produktif | {{ $stats['lessActiveCount'] }} Kurang Aktif</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <div class="glass" style="padding: 1.5rem; text-align: center;">
        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Kinerja Unggul (SINTA 3Yr > Rata-rata)</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #4ade80;">{{ $stats['unggulCount'] }} Dosen</div>
    </div>
    <div class="glass" style="padding: 1.5rem; text-align: center;">
        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Kinerja Baik (SINTA 3Yr &le; Rata-rata)</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #0ea5e9;">{{ $stats['baikCount'] }} Dosen</div>
    </div>
    <div class="glass" style="padding: 1.5rem; text-align: center;">
        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Perlu Peningkatan (SINTA 3Yr = 0)</div>
        <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">{{ $stats['perluCount'] }} Dosen</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
    <div class="glass" style="padding: 2rem;">
        <h2 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Top Performance (SINTA 3Yr Score)</h2>
        <div style="height: 300px;">
            <canvas id="topPerformersChart"></canvas>
        </div>
    </div>
    <div style="display: flex; flex-direction: column; gap: 2rem;">
        <div class="glass" style="padding: 2rem; border: 1px solid rgba(239, 68, 68, 0.3);">
            <h2 style="margin-bottom: 0.5rem; font-size: 1.2rem; color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Early Warning System</h2>
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">Dosen dengan SINTA 3Yr = 0 (Total SINTA > 100)</div>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @foreach(collect($lecturers)->filter(fn($l) => $l['sinta3Yr'] == 0 && $l['sintaOverall'] > 100)->take(3) as $l)
                    <div onclick='window.showLecturerDetail(@json($l))' style="padding: 1rem; background: rgba(239, 68, 68, 0.05); border-radius: 8px; cursor: pointer;" class="glass-hover" data-id="{{ $l['id'] }}">
                        <div style="font-weight: 600; font-size: 0.9rem;">{{ $l['name'] }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem; display: flex; justify-content: space-between;">
                            <span>{{ $l['prodi'] }}</span>
                            <span style="color: #ef4444; font-weight: bold;">Overall: {{ $l['sintaOverall'] }}</span>
                        </div>
                        <div class="sinta-id-badge" style="display: none; margin-top: 0.25rem;">
                            <span class="pill" style="background: var(--secondary); color: var(--primary); font-weight: 700; font-size: 0.65rem; border: none; padding: 0.2rem 0.5rem;">SINTA ID: <span class="sinta-id-val"></span></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const topLecturers = @json(collect($lecturers)->sortByDesc('sinta3Yr')->take(5)->values());
        
        new Chart(document.getElementById('topPerformersChart'), {
            type: 'bar',
            data: {
                labels: topLecturers.map(l => l.name),
                datasets: [{
                    label: 'SINTA 3Yr Score',
                    data: topLecturers.map(l => l.sinta3Yr),
                    backgroundColor: 'rgba(15, 46, 90, 0.6)',
                    borderColor: '#0f2e5a',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { beginAtZero: true },
                    y: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });

        // Read SINTA IDs from localStorage and show on cards
        const savedSintaIds = JSON.parse(localStorage.getItem('sintaIds') || '{}');
        document.querySelectorAll('.glass-hover').forEach(card => {
            const id = card.dataset.id;
            if (id && savedSintaIds[id]) {
                const badge = card.querySelector('.sinta-id-badge');
                if (badge) {
                    const val = card.querySelector('.sinta-id-val');
                    val.innerText = savedSintaIds[id];
                    badge.style.display = 'block';
                }
            }
        });
    });
</script>

<style>
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card { padding: 1.5rem; }
    .stat-value { font-size: 1.8rem; font-weight: 700; margin: 0.5rem 0; }
    .stat-label { color: var(--text-muted); font-size: 0.85rem; }
</style>
@endsection
