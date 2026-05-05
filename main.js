import { lecturers, publications, stats } from './data.js';

const contentArea = document.getElementById('content-area');
const pageTitle = document.getElementById('page-title');
const navLinks = document.querySelectorAll('.nav-link');
const modalContainer = document.getElementById('modal-container');
const modalBody = document.getElementById('modal-body');
const closeModal = document.getElementById('close-modal');

// Close modal logic
closeModal.addEventListener('click', () => {
    modalContainer.style.display = 'none';
});

window.addEventListener('click', (e) => {
    if (e.target === modalContainer) {
        modalContainer.style.display = 'none';
    }
});

// Helper to render templates
function render(template) {
    contentArea.innerHTML = '';
    const div = document.createElement('div');
    div.className = 'animate-fade';
    div.innerHTML = template;
    contentArea.appendChild(div);
}

// Pages
const pages = {
    dashboard: () => {
        pageTitle.innerText = 'Dashboard Kinerja';
        render(`
            <div class="stats-grid">
                <div class="stat-card glass glass-hover">
                    <div class="stat-label">Total Dosen</div>
                    <div class="stat-value">${stats.totalLecturers}</div>
                    <div style="font-size: 0.7rem; color: #4ade80;"><i class="fas fa-arrow-up"></i> Terverifikasi PDDikti</div>
                </div>
                <div class="stat-card glass glass-hover">
                    <div class="stat-label">Output Penelitian</div>
                    <div class="stat-value">${stats.totalResearch}</div>
                    <div style="font-size: 0.7rem; color: #4ade80;"><i class="fas fa-chart-line"></i> Akumulasi 3 Tahun</div>
                </div>
                <div class="stat-card glass glass-hover">
                    <div class="stat-label">Rerata SINTA Score</div>
                    <div class="stat-value">${stats.avgSinta} <span style="font-size:1rem;color:var(--text-muted)">/ ${stats.avgSinta3Yr}</span></div>
                    <div style="font-size: 0.7rem; color: var(--neon-blue);">Status: Overall / 3Yr</div>
                </div>
                <div class="stat-card glass glass-hover">
                    <div class="stat-label">Rasio Produktivitas</div>
                    <div class="stat-value">${Math.round((lecturers.filter(l => l.sinta3Yr >= 50).length / lecturers.length) * 100)}%</div>
                    <div style="font-size: 0.7rem; color: var(--neon-purple);">${lecturers.filter(l => l.sinta3Yr >= 50).length} Produktif | ${lecturers.filter(l => l.sinta3Yr < 50).length} Kurang Aktif</div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                <div class="glass" style="padding: 1.5rem; text-align: center;">
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Kinerja Unggul (SINTA 3Yr > Rata-rata)</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #4ade80;">${lecturers.filter(l => l.sinta3Yr > stats.avgSinta3Yr).length} Dosen</div>
                </div>
                <div class="glass" style="padding: 1.5rem; text-align: center;">
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Kinerja Baik (SINTA 3Yr &le; Rata-rata)</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #0ea5e9;">${lecturers.filter(l => l.sinta3Yr > 0 && l.sinta3Yr <= stats.avgSinta3Yr).length} Dosen</div>
                </div>
                <div class="glass" style="padding: 1.5rem; text-align: center;">
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.5rem;">Perlu Peningkatan (SINTA 3Yr = 0)</div>
                    <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">${lecturers.filter(l => l.sinta3Yr === 0).length} Dosen</div>
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
                    <div class="glass" style="padding: 2rem;">
                        <h2 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Publikasi Terbaru</h2>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            ${publications.map(p => `
                                <div style="padding: 1rem; background: rgba(255,255,255,0.03); border-radius: 8px; cursor: pointer; transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.08)'" onmouseout="this.style.background='rgba(255,255,255,0.03)'" onclick="window.showLecturerDetail(${p.lecturerId})">
                                    <div style="font-weight: 600; font-size: 0.9rem;">${p.title}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem; display: flex; justify-content: space-between; align-items: center;">
                                        <span><i class="fas fa-user-circle"></i> ${lecturers.find(l => l.id === p.lecturerId)?.name || 'Unknown'} • ${p.year}</span>
                                        <span style="color: var(--neon-blue); font-size: 0.7rem;"><i class="fas fa-search"></i> Detail Dosen</span>
                                    </div>
                                    <div class="pill" style="display: inline-block; margin-top: 0.5rem; font-size: 0.6rem; background: var(--primary-glow); border-color: var(--primary);">${p.source}</div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    
                    <div class="glass" style="padding: 2rem; border: 1px solid rgba(239, 68, 68, 0.3);">
                        <h2 style="margin-bottom: 0.5rem; font-size: 1.2rem; color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Early Warning System</h2>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">Dosen dengan SINTA 3Yr = 0 (Total SINTA &gt; 100)</div>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            ${lecturers.filter(l => l.sinta3Yr === 0 && l.sintaOverall > 100).slice(0, 3).map(l => `
                                <div style="padding: 1rem; background: rgba(239, 68, 68, 0.05); border-radius: 8px; cursor: pointer; transition: background 0.3s;" onmouseover="this.style.background='rgba(239, 68, 68, 0.1)'" onmouseout="this.style.background='rgba(239, 68, 68, 0.05)'" onclick="window.showLecturerDetail(${l.id})">
                                    <div style="font-weight: 600; font-size: 0.9rem;">${l.name}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem; display: flex; justify-content: space-between;">
                                        <span>${l.prodi}</span>
                                        <span style="color: #ef4444; font-weight: bold;">Overall: ${l.sintaOverall}</span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `);

        setTimeout(() => {
            initDashboardCharts();
            document.getElementById('download-report')?.addEventListener('click', handleDownloadReport);
        }, 100);
    },

    lecturers: () => {
        pageTitle.innerText = 'Database Kinerja Dosen';
        render(`
            <div class="search-container" style="display: flex; gap: 1rem; align-items: center;">
                <div style="position: relative; flex: 1;">
                    <i class="fas fa-search" style="position: absolute; left: 1.5rem; top: 1.1rem; color: var(--text-muted);"></i>
                    <input type="text" class="search-input" placeholder="Cari nama dosen atau program studi..." id="lecturer-search" style="padding-left: 3.5rem; margin-bottom: 0;">
                </div>
                <select id="performance-filter" class="glass" style="padding: 1rem; border-radius: 12px; color: white; border: 1px solid var(--border-glass); outline: none;">
                    <option value="all">Semua Kinerja</option>
                    <option value="unggul">Kinerja Unggul (> Rata-rata)</option>
                    <option value="baik">Kinerja Baik (&le; Rata-rata)</option>
                    <option value="perlu">Perlu Peningkatan (0)</option>
                </select>
            </div>
            <div class="lecturer-grid" id="lecturer-list">
                ${renderLecturerCards([...lecturers].sort((a, b) => b.sinta3Yr - a.sinta3Yr))}
            </div>
        `);

        const filterData = () => {
            const query = document.getElementById('lecturer-search').value.toLowerCase();
            const perf = document.getElementById('performance-filter').value;
            
            const filtered = lecturers.filter(l => {
                const matchesSearch = l.name.toLowerCase().includes(query) || l.prodi.toLowerCase().includes(query);
                let matchesPerf = true;
                if (perf === 'unggul') matchesPerf = l.sinta3Yr > stats.avgSinta3Yr;
                else if (perf === 'baik') matchesPerf = l.sinta3Yr > 0 && l.sinta3Yr <= stats.avgSinta3Yr;
                else if (perf === 'perlu') matchesPerf = l.sinta3Yr === 0;
                
                return matchesSearch && matchesPerf;
            });
            
            // Sort by SINTA 3Yr descending
            filtered.sort((a, b) => b.sinta3Yr - a.sinta3Yr);
            
            document.getElementById('lecturer-list').innerHTML = renderLecturerCards(filtered);
        };

        document.getElementById('lecturer-search').addEventListener('input', filterData);
        document.getElementById('performance-filter').addEventListener('change', filterData);
    },

    crawl: () => {
        pageTitle.innerText = 'Crawl Engine (SINTA, Scholar, Scopus)';
        render(`
            <div class="glass" style="padding: 3rem; text-align: center; max-width: 800px; margin: 0 auto;">
                <div style="font-size: 3rem; margin-bottom: 1.5rem; color: var(--primary);">
                    <i class="fas fa-spider fa-spin"></i>
                </div>
                <h2>Crawl Research Data</h2>
                <p style="color: var(--text-muted); margin-bottom: 2rem;">Sinkronisasi data otomatis dari berbagai database index penelitian.</p>
                
                <div style="display: flex; gap: 1rem; justify-content: center; margin-bottom: 2rem;">
                    <div class="glass" style="padding: 1rem; flex: 1;">
                        <img src="https://sinta.kemdikbud.go.id/assets/img/logo-sinta.png" style="height: 30px; filter: grayscale(1) invert(1); opacity: 0.7;">
                    </div>
                    <div class="glass" style="padding: 1rem; flex: 1;">
                        <i class="fab fa-google" style="font-size: 1.5rem; color: var(--text-muted);"></i> Scholar
                    </div>
                    <div class="glass" style="padding: 1rem; flex: 1;">
                        <i class="fas fa-book" style="font-size: 1.5rem; color: var(--text-muted);"></i> Scopus
                    </div>
                </div>

                <button id="start-crawl" class="glass glass-hover" style="background: var(--primary); border: none; padding: 1rem 2rem; color: white; font-weight: 600; cursor: pointer; border-radius: 12px;">
                    Start Multi-Source Crawling
                </button>

                <div id="crawl-status" style="margin-top: 2rem; display: none;">
                    <div style="height: 4px; background: rgba(255,255,255,0.1); border-radius: 10px; overflow: hidden; margin-bottom: 1rem;">
                        <div id="crawl-progress" style="height: 100%; width: 0%; background: linear-gradient(to right, var(--neon-blue), var(--neon-purple)); transition: width 0.3s;"></div>
                    </div>
                    <p id="crawl-log" style="font-family: monospace; font-size: 0.8rem; color: #4ade80;"></p>
                </div>
            </div>
        `);

        document.getElementById('start-crawl').addEventListener('click', startCrawlSimulation);
    },

    analytics: () => {
        pageTitle.innerText = 'Research Analytics';
        render(`
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
                    <h3 style="margin-bottom: 1.5rem; font-size: 1.2rem;">Status Produktivitas Dosen</h3>
                    <div style="height: 350px;">
                        <canvas id="produktivitasChart"></canvas>
                    </div>
                </div>
            </div>
        `);

        // Initialize Charts
        setTimeout(() => {
            initAnalyticsCharts();
        }, 100);
    }
};

function initAnalyticsCharts() {
    const prodiData = {};
    lecturers.forEach(l => {
        prodiData[l.prodi] = (prodiData[l.prodi] || 0) + 1;
    });

    const sortedSinta = [...lecturers].sort((a, b) => b.sintaOverall - a.sintaOverall).slice(0, 10);
    
    const prodiAvgH = {};
    const prodiCounts = {};
    lecturers.forEach(l => {
        prodiAvgH[l.prodi] = (prodiAvgH[l.prodi] || 0) + l.hIndex;
        prodiCounts[l.prodi] = (prodiCounts[l.prodi] || 0) + 1;
    });
    Object.keys(prodiAvgH).forEach(p => {
        prodiAvgH[p] = (prodiAvgH[p] / prodiCounts[p]).toFixed(1);
    });

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
                y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#94a3b8' } },
                x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } }
            },
            plugins: { legend: { display: false } }
        }
    });

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
                x: { 
                    title: { display: true, text: 'Produktivitas (SINTA 3Yr)', color: '#94a3b8' },
                    grid: { color: 'rgba(255,255,255,0.1)' }, 
                    ticks: { color: '#94a3b8' }
                },
                y: { 
                    title: { display: true, text: 'Dampak Riset (h-Index)', color: '#94a3b8' },
                    grid: { color: 'rgba(255,255,255,0.1)' }, 
                    ticks: { color: '#94a3b8' }
                }
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
            },
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const l = scatterData[index].lecturer;
                    window.showLecturerDetail(l.id);
                }
            },
            onHover: (event, elements) => {
                event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
            }
        }
    });

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
                legend: { position: 'right', labels: { color: '#94a3b8', font: { size: 9 }, usePointStyle: true } }
            }
        }
    });

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
                    grid: { color: 'rgba(255,255,255,0.1)' },
                    angleLines: { color: 'rgba(255,255,255,0.1)' },
                    pointLabels: { color: '#94a3b8', font: { size: 9 } },
                    ticks: { display: false }
                }
            },
            plugins: { legend: { display: false } }
        }
    });

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
                legend: { position: 'bottom', labels: { color: '#94a3b8' } }
            }
        }
    });
}

function initDashboardCharts() {
    const sorted = [...lecturers].sort((a, b) => b.sinta3Yr - a.sinta3Yr).slice(0, 5);
    
    new Chart(document.getElementById('topPerformersChart'), {
        type: 'bar',
        data: {
            labels: sorted.map(l => l.name),
            datasets: [{
                label: 'SINTA 3Yr Score',
                data: sorted.map(l => l.sinta3Yr),
                backgroundColor: 'rgba(14, 165, 233, 0.5)',
                borderColor: '#0ea5e9',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#94a3b8' } },
                y: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 10 } } }
            },
            plugins: { legend: { display: false } },
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const lecturerId = sorted[index].id;
                    window.showLecturerDetail(lecturerId);
                }
            },
            onHover: (event, elements) => {
                event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
            }
        }
    });
}

function renderLecturerCards(data) {
    if (data.length === 0) return '<p style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">Tidak ada dosen yang ditemukan.</p>';
    
    return data.map(l => `
        <div class="lecturer-card glass glass-hover">
            <div class="lecturer-header">
                <div class="avatar">${l.name.charAt(0)}</div>
                <div class="lecturer-info">
                    <h3>${l.name}</h3>
                    <p>${l.prodi}</p>
                </div>
            </div>
            <div class="performance-pills">
                <div class="pill">SINTA Overall: ${l.sintaOverall}</div>
                <div class="pill">SINTA 3Yr: ${l.sinta3Yr}</div>
                <div class="pill">Scholar: ${l.scholar}</div>
                <div class="pill">Scopus: ${l.scopus}</div>
                <div class="pill">h-Index: ${l.hIndex}</div>
                ${l.scopusHIndex ? `<div class="pill" style="border-color: var(--neon-blue);">Scopus h-Idx: ${l.scopusHIndex}</div>` : ''}
            </div>
            <div style="margin-top: auto;">
                <button onclick="window.showLecturerDetail(${l.id})" style="width: 100%; padding: 0.5rem; background: rgba(255,255,255,0.05); border: 1px solid var(--border-glass); border-radius: 8px; color: var(--text-main); font-size: 0.8rem; cursor: pointer;" class="glass-hover">Lihat Detail</button>
            </div>
        </div>
    `).join('');
}

window.showLecturerDetail = (id) => {
    const lecturer = lecturers.find(l => l.id === id);
    if (!lecturer) return;

    modalBody.innerHTML = `
        <div class="lecturer-header" style="margin-bottom: 2rem;">
            <div class="avatar" style="width: 80px; height: 80px; font-size: 2rem;">${lecturer.name.charAt(0)}</div>
            <div>
                <h2 style="font-size: 1.8rem;">${lecturer.name}</h2>
                <p style="color: var(--text-muted); font-size: 1rem;">${lecturer.prodi}</p>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-card">
                <div class="detail-label">SINTA Overall</div>
                <div class="detail-value" style="color: var(--primary);">${lecturer.sintaOverall}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">SINTA 3Yr</div>
                <div class="detail-value" style="color: var(--neon-blue);">${lecturer.sinta3Yr}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">Scholar Citations</div>
                <div class="detail-value" style="color: var(--secondary);">${lecturer.scholar}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">Scopus Documents</div>
                <div class="detail-value" style="color: var(--neon-blue);">${lecturer.scopus}</div>
            </div>
            <div class="detail-card">
                <div class="detail-label">h-Index</div>
                <div class="detail-value" style="color: var(--neon-purple);">${lecturer.hIndex}</div>
            </div>
        </div>

        <div style="margin-top: 2rem;">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem;">Top Publications</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                ${publications.filter(p => p.lecturerId === lecturer.id).length > 0 
                    ? publications.filter(p => p.lecturerId === lecturer.id).map(p => `
                        <div style="padding: 1rem; background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid var(--border-glass);">
                            <div style="font-weight: 600;">${p.title}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.3rem;">${p.source} • ${p.year}</div>
                        </div>
                    `).join('')
                    : '<p style="color: var(--text-muted); font-size: 0.9rem; font-style: italic;">Belum ada data publikasi terperinci untuk dosen ini.</p>'
                }
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <a href="https://sinta.kemdiktisaintek.go.id/authors/profile/${lecturer.id}" target="_blank" class="glass" style="flex: 1; padding: 1rem; text-align: center; text-decoration: none; color: white; font-size: 0.9rem;">
                <i class="fas fa-external-link-alt"></i> Profil SINTA
            </a>
            <a href="#" class="glass" style="flex: 1; padding: 1rem; text-align: center; text-decoration: none; color: white; font-size: 0.9rem;">
                <i class="fas fa-file-download"></i> Export CV
            </a>
        </div>
    `;

    modalContainer.style.display = 'flex';
};

function startCrawlSimulation() {
    const btn = document.getElementById('start-crawl');
    const status = document.getElementById('crawl-status');
    const progress = document.getElementById('crawl-progress');
    const log = document.getElementById('crawl-log');
    
    btn.disabled = true;
    btn.innerText = 'Crawling in Progress...';
    status.style.display = 'block';
    
    const logs = [
        'Initializing connection to PDDikti...',
        'Syncing names with SINTA Database...',
        'Scraping Google Scholar citations for SUYANTI...',
        'Fetching Scopus API for MARZUKI ALIE...',
        'Updating publication metadata for 100 lecturers...',
        'Calculating h-Index trends...',
        'Finalizing data synchronization...'
    ];
    
    let step = 0;
    const interval = setInterval(() => {
        if (step >= logs.length) {
            clearInterval(interval);
            btn.innerText = 'Crawl Finished';
            log.innerText = 'Synchronization Complete. Data is now real-time.';
            return;
        }
        
        const perc = ((step + 1) / logs.length) * 100;
        progress.style.width = `${perc}%`;
        log.innerText = `> ${logs[step]}`;
        step++;
    }, 1500);
}

function handleDownloadReport() {
    const btn = document.getElementById('download-report');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
    
    setTimeout(() => {
        try {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'pt', 'a4');
            
            // Header
            doc.setFontSize(18);
            doc.setFont("helvetica", "bold");
            doc.text('Laporan Kinerja Penelitian Dosen', 40, 40);
            
            doc.setFontSize(12);
            doc.setFont("helvetica", "normal");
            doc.text('Universitas Indo Global Mandiri', 40, 55);
            doc.text('Tanggal Unduh: ' + new Date().toLocaleDateString('id-ID'), 40, 70);
            
            // Stats summary
            doc.setFontSize(11);
            doc.text('Statistik Eksekutif:', 40, 95);
            doc.text(`- Total Dosen Terverifikasi: ${stats.totalLecturers}`, 50, 110);
            doc.text(`- Rerata SINTA Score Overall: ${stats.avgSinta}`, 50, 125);
            doc.text(`- Rerata SINTA Score 3Yr: ${stats.avgSinta3Yr}`, 50, 140);
            doc.text(`- Dosen Kinerja Unggul (3Yr > Rata-rata): ${lecturers.filter(l => l.sinta3Yr > stats.avgSinta3Yr).length}`, 50, 155);
            
            // Table
            const sortedLecturers = [...lecturers].sort((a, b) => b.sinta3Yr - a.sinta3Yr);
            const tableData = sortedLecturers.map((l, index) => [
                index + 1,
                l.name,
                l.prodi,
                l.sinta3Yr,
                l.sintaOverall,
                l.hIndex
            ]);

            doc.autoTable({
                startY: 175,
                head: [['No', 'Nama Dosen', 'Program Studi', 'SINTA 3Yr', 'SINTA Overall', 'h-Index']],
                body: tableData,
                theme: 'grid',
                headStyles: { fillColor: [99, 102, 241] },
                styles: { fontSize: 8, cellPadding: 4 },
                columnStyles: {
                    0: { cellWidth: 30, halign: 'center' },
                    3: { halign: 'center' },
                    4: { halign: 'center' },
                    5: { halign: 'center' }
                }
            });
            
            // Generate off-screen charts for PDF
            const hiddenDiv = document.createElement('div');
            hiddenDiv.style.width = '1200px';
            hiddenDiv.style.position = 'absolute';
            hiddenDiv.style.left = '-9999px';
            document.body.appendChild(hiddenDiv);

            // Chart 1: Distribusi Prodi
            const canvasProdi = document.createElement('canvas');
            canvasProdi.width = 600;
            canvasProdi.height = 300;
            hiddenDiv.appendChild(canvasProdi);

            const prodiData = {};
            lecturers.forEach(l => { prodiData[l.prodi] = (prodiData[l.prodi] || 0) + 1; });
            
            new Chart(canvasProdi, {
                type: 'bar',
                data: {
                    labels: Object.keys(prodiData),
                    datasets: [{ label: 'Jumlah Dosen', data: Object.values(prodiData), backgroundColor: 'rgba(99, 102, 241, 0.8)' }]
                },
                options: { animation: false, responsive: false, plugins: { legend: { display: false } } }
            });
            const imgProdi = canvasProdi.toDataURL('image/png');

            // Chart 2: Top 10 SINTA Score Overall
            const canvasSinta = document.createElement('canvas');
            canvasSinta.width = 400;
            canvasSinta.height = 300;
            hiddenDiv.appendChild(canvasSinta);

            const sortedSinta = [...lecturers].sort((a, b) => b.sintaOverall - a.sintaOverall).slice(0, 10);
            new Chart(canvasSinta, {
                type: 'doughnut',
                data: {
                    labels: sortedSinta.map(l => l.name),
                    datasets: [{ data: sortedSinta.map(l => l.sintaOverall), backgroundColor: ['#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16', '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9'] }]
                },
                options: { animation: false, responsive: false }
            });
            const imgSinta = canvasSinta.toDataURL('image/png');

            // Chart 3: Rerata h-Index per Prodi
            const canvasHIndex = document.createElement('canvas');
            canvasHIndex.width = 600;
            canvasHIndex.height = 300;
            hiddenDiv.appendChild(canvasHIndex);

            const prodiAvgH = {};
            const prodiCounts = {};
            lecturers.forEach(l => { prodiAvgH[l.prodi] = (prodiAvgH[l.prodi] || 0) + l.hIndex; prodiCounts[l.prodi] = (prodiCounts[l.prodi] || 0) + 1; });
            Object.keys(prodiAvgH).forEach(p => { prodiAvgH[p] = (prodiAvgH[p] / prodiCounts[p]).toFixed(1); });

            new Chart(canvasHIndex, {
                type: 'bar',
                data: {
                    labels: Object.keys(prodiAvgH),
                    datasets: [{ label: 'Rerata h-Index', data: Object.values(prodiAvgH), backgroundColor: 'rgba(236, 72, 153, 0.8)' }]
                },
                options: { animation: false, responsive: false, plugins: { legend: { display: false } } }
            });
            const imgHIndex = canvasHIndex.toDataURL('image/png');

            // Chart 4: Status Produktivitas
            const canvasProd = document.createElement('canvas');
            canvasProd.width = 400;
            canvasProd.height = 300;
            hiddenDiv.appendChild(canvasProd);

            const produktif = lecturers.filter(l => l.sinta3Yr >= 50).length;
            const kurangProduktif = lecturers.length - produktif;

            new Chart(canvasProd, {
                type: 'pie',
                data: {
                    labels: ['Produktif (SINTA >= 50)', 'Kurang Aktif (SINTA < 50)'],
                    datasets: [{ data: [produktif, kurangProduktif], backgroundColor: ['#4ade80', '#f87171'] }]
                },
                options: { animation: false, responsive: false }
            });
            const imgProd = canvasProd.toDataURL('image/png');

            document.body.removeChild(hiddenDiv);

            // Add Charts to PDF after table
            doc.addPage();
            doc.setFontSize(16);
            doc.setFont("helvetica", "bold");
            doc.text('Lampiran Analitik Kinerja Dosen', 40, 40);
            
            doc.setFontSize(12);
            doc.text('1. Distribusi Dosen per Program Studi', 40, 70);
            doc.addImage(imgProdi, 'PNG', 40, 80, 400, 200);

            doc.text('2. Top 10 SINTA Score Overall', 40, 320);
            doc.addImage(imgSinta, 'PNG', 40, 330, 250, 187.5);

            doc.addPage();
            doc.setFontSize(12);
            doc.text('3. Rerata h-Index per Prodi', 40, 40);
            doc.addImage(imgHIndex, 'PNG', 40, 50, 400, 200);

            doc.text('4. Status Produktivitas Dosen', 40, 290);
            doc.addImage(imgProd, 'PNG', 40, 300, 250, 187.5);
            
            doc.save('Laporan_Kinerja_Riset_UIGM.pdf');
            
            btn.innerHTML = '<i class="fas fa-check"></i> Berhasil Unduh';
            btn.style.background = '#22c55e';
            
        } catch(e) {
            alert('Gagal membuat PDF. Pastikan koneksi internet aktif untuk memuat library jsPDF.');
            console.error(e);
            btn.innerHTML = '<i class="fas fa-times"></i> Gagal';
            btn.style.background = '#ef4444';
        }
        
        setTimeout(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            btn.style.background = 'var(--primary)';
        }, 3000);
    }, 500);
}

// Navigation logic
navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        navLinks.forEach(l => l.classList.remove('active'));
        link.classList.add('active');
        const page = link.getAttribute('data-page');
        pages[page]();
    });
});

// Initial load
pages.dashboard();
