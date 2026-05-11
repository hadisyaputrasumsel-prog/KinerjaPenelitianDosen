@extends('layouts.app')

@section('content')
<div style="display: flex; flex-direction: column; gap: 2rem; margin-bottom: 2rem;">
    <div class="glass" style="padding: 2rem;">
        <h3 style="margin-bottom: 0.5rem; font-size: 1.2rem;">Kuadran Kinerja Dosen (Scatter Plot)</h3>
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">Memetakan Produktivitas (SINTA 3Yr) terhadap Dampak Penelitian (h-Index). Dosen di kuadran Kanan Atas adalah "Bintang".</div>
        <div style="height: 450px;">
            <canvas id="kuadranChart"></canvas>
        </div>
    </div>
    <div class="glass" style="padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Distribusi Dosen per Program Studi</h3>
        <div style="height: 350px;">
            <canvas id="prodiChart"></canvas>
        </div>
    </div>
    <div class="glass" style="padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Top 10 SINTA Score Overall</h3>
        <div style="height: 400px;">
            <canvas id="sintaChart"></canvas>
        </div>
    </div>
    <div class="glass" style="padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Rerata h-Index per Prodi</h3>
        <div style="height: 400px;">
            <canvas id="hIndexChart"></canvas>
        </div>
    </div>
    <div class="glass" style="padding: 2rem;">
        <h3 style="margin-bottom: 0.5rem; font-size: 1.2rem;">Output Dokumen Riset per Prodi</h3>
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem;">Total dokumen riset (Scopus & Google Scholar) yang terafiliasi dengan masing-masing Program Studi.</div>
        <div style="display: flex; flex-direction: column; gap: 3rem;">
            <div style="height: 400px;">
                <canvas id="outputProdiChart"></canvas>
            </div>
            <div style="height: 550px; border-top: 1px solid var(--border-glass); padding-top: 2rem;">
                <canvas id="outputPieChart"></canvas>
            </div>
        </div>
    </div>
    <div class="glass" style="padding: 2rem;">
        <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Status Produktivitas Dosen</h3>
        <div style="height: 350px;">
            <canvas id="produktivitasChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lecturers = @json($lecturers);

    // Process Data
    const prodiData = {};
    lecturers.forEach(l => {
        prodiData[l.prodi] = (prodiData[l.prodi] || 0) + 1;
    });

    const sortedSinta = [...lecturers].sort((a, b) => b.sintaOverall - a.sintaOverall).slice(0, 10);
    
    const prodiAvgH = {};
    const prodiCounts = {};
    const prodiS1 = {};
    const prodiS2 = {};
    const prodiS3 = {};
    const prodiS4 = {};
    const prodiS5 = {};
    const prodiS6 = {};
    const prodiScopus = {};
    
    lecturers.forEach(l => {
        prodiAvgH[l.prodi] = (prodiAvgH[l.prodi] || 0) + l.hIndex;
        prodiCounts[l.prodi] = (prodiCounts[l.prodi] || 0) + 1;
        
        // Simulation for SINTA 1-6
        const s1 = Math.round(l.scopus * 0.2);
        const s2 = Math.round(l.scopus * 0.8);
        const s3 = Math.round(l.scholar * 0.1);
        const s4 = Math.round(l.scholar * 0.4);
        const s5 = Math.round(l.scholar * 0.3);
        const s6 = Math.round(l.scholar * 0.2);

        prodiS1[l.prodi] = (prodiS1[l.prodi] || 0) + s1;
        prodiS2[l.prodi] = (prodiS2[l.prodi] || 0) + s2;
        prodiS3[l.prodi] = (prodiS3[l.prodi] || 0) + s3;
        prodiS4[l.prodi] = (prodiS4[l.prodi] || 0) + s4;
        prodiS5[l.prodi] = (prodiS5[l.prodi] || 0) + s5;
        prodiS6[l.prodi] = (prodiS6[l.prodi] || 0) + s6;
        prodiScopus[l.prodi] = (prodiScopus[l.prodi] || 0) + l.scopus;
    });

    Object.keys(prodiAvgH).forEach(p => {
        prodiAvgH[p] = (prodiAvgH[p] / prodiCounts[p]).toFixed(1);
    });

    // 1. Prodi Chart
    new Chart(document.getElementById('prodiChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(prodiData),
            datasets: [{
                label: 'Jumlah Dosen',
                data: Object.values(prodiData),
                backgroundColor: 'rgba(99, 102, 241, 0.5)',
                borderColor: '#6366f1',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { color: '#475569' } },
                x: { ticks: { color: '#475569', font: { size: 10 } } }
            },
            plugins: { legend: { display: false } }
        }
    });

    // 2. Kuadran Chart
    const scatterData = lecturers.filter(l => l.sinta3Yr > 0 || l.hIndex > 0).map(l => ({
        x: l.sinta3Yr,
        y: l.hIndex,
        lecturer: l
    }));

    new Chart(document.getElementById('kuadranChart'), {
        type: 'scatter',
        data: {
            datasets: [{
                label: 'Dosen',
                data: scatterData,
                backgroundColor: 'rgba(14, 165, 233, 0.6)',
                borderColor: '#0ea5e9',
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { title: { display: true, text: 'Produktivitas (SINTA 3Yr)', color: '#475569' } },
                y: { title: { display: true, text: 'Dampak Riset (h-Index)', color: '#475569' } }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const l = context.raw.lecturer;
                            return `${l.name} (${l.prodi}) | 3Yr: ${l.sinta3Yr}, h-Index: ${l.hIndex}`;
                        }
                    }
                }
            }
        }
    });

    // 3. SINTA Chart
    new Chart(document.getElementById('sintaChart'), {
        type: 'doughnut',
        data: {
            labels: sortedSinta.map(l => l.name),
            datasets: [{
                data: sortedSinta.map(l => l.sintaOverall),
                backgroundColor: [
                    '#6366f1', '#0ea5e9', '#00d2ff', '#9d50bb', '#4ade80',
                    '#f59e0b', '#ef4444', '#ec4899', '#8b5cf6', '#14b8a6'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { color: '#475569', font: { size: 9 }, usePointStyle: true } }
            }
        }
    });

    // 4. h-Index Chart
    new Chart(document.getElementById('hIndexChart'), {
        type: 'radar',
        data: {
            labels: Object.keys(prodiAvgH),
            datasets: [{
                label: 'Rerata h-Index',
                data: Object.values(prodiAvgH),
                backgroundColor: 'rgba(14, 165, 233, 0.2)',
                borderColor: '#0ea5e9',
                pointBackgroundColor: '#0ea5e9'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    pointLabels: { color: '#475569', font: { size: 9 } },
                    ticks: { display: false }
                }
            },
            plugins: { legend: { display: false } }
        }
    });

    // 5. Output Prodi Chart
    new Chart(document.getElementById('outputProdiChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(prodiS1),
            datasets: [
                { label: 'SINTA 1', data: Object.values(prodiS1), backgroundColor: '#e11d48' },
                { label: 'SINTA 2', data: Object.values(prodiS2), backgroundColor: '#f97316' },
                { label: 'SINTA 3', data: Object.values(prodiS3), backgroundColor: '#fbbf24' },
                { label: 'SINTA 4', data: Object.values(prodiS4), backgroundColor: '#4d7c0f' },
                { label: 'SINTA 5', data: Object.values(prodiS5), backgroundColor: '#06b6d4' },
                { label: 'SINTA 6', data: Object.values(prodiS6), backgroundColor: '#8b5cf6' },
                { label: 'Scopus', data: Object.values(prodiScopus), backgroundColor: '#0f2e5a' }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { stacked: true, beginAtZero: true },
                x: { stacked: true, ticks: { font: { size: 10 } } }
            },
            plugins: { legend: { position: 'top' } }
        }
    });

    // 6. Output Pie Chart
    new Chart(document.getElementById('outputPieChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(prodiS1),
            datasets: [{
                data: Object.keys(prodiS1).map(prodi => prodiScopus[prodi] || 0),
                backgroundColor: [
                    '#0f2e5a', '#d7ac7c', '#e11d48', '#f97316', 
                    '#fbbf24', '#4d7c0f', '#06b6d4', '#8b5cf6'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 15, font: { size: 11 }, color: '#1e293b' } },
                title: { display: true, text: 'Distribusi Dokumen Scopus per Program Studi', color: '#0f2e5a', font: { size: 16, weight: '700' } }
            }
        }
    });

    // 7. Produktivitas Chart
    const produktif = lecturers.filter(l => l.sinta3Yr >= 50).length;
    const tidakProduktif = lecturers.length - produktif;

    new Chart(document.getElementById('produktivitasChart'), {
        type: 'pie',
        data: {
            labels: ['Produktif (SINTA 3Yr >= 50)', 'Kurang/Tidak Produktif (SINTA 3Yr < 50)'],
            datasets: [{
                data: [produktif, tidakProduktif],
                backgroundColor: ['#4ade80', '#f59e0b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#475569' } }
            }
        }
    });
});
</script>
@endsection
