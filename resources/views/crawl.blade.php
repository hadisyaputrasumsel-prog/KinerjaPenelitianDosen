@extends('layouts.app')

@section('content')
<div class="glass" style="padding: 3rem; text-align: center; max-width: 800px; margin: 0 auto;">
    <div style="font-size: 3rem; margin-bottom: 1.5rem; color: var(--primary);">
        <i class="fas fa-spider"></i>
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

    <div style="margin-bottom: 1.5rem; max-width: 400px; margin-left: auto; margin-right: auto;">
        <input type="text" id="scholar-name" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-glass); border-radius: 10px; background: rgba(255,255,255,0.8);" placeholder="Masukkan Nama Dosen (Contoh: Ahmad Sanmorino)">
    </div>

    <button id="start-crawl" class="glass glass-hover" style="background: var(--primary); border: none; padding: 1rem 2rem; color: white; font-weight: 600; cursor: pointer; border-radius: 12px;">
        Cari di Scholar
    </button>

    <div id="crawl-status" style="margin-top: 2rem; display: none;">
        <div style="height: 4px; background: rgba(0,0,0,0.1); border-radius: 10px; overflow: hidden; margin-bottom: 1rem;">
            <div id="crawl-progress" style="height: 100%; width: 0%; background: linear-gradient(to right, #0ea5e9, #8b5cf6); transition: width 0.3s;"></div>
        </div>
        <div id="crawl-log" style="font-family: monospace; font-size: 0.9rem; color: var(--text-main); text-align: left; background: rgba(0,0,0,0.03); padding: 1rem; border-radius: 10px;"></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('start-crawl');
    const status = document.getElementById('crawl-status');
    const progress = document.getElementById('crawl-progress');
    const log = document.getElementById('crawl-log');
    const spiderIcon = document.querySelector('.fa-spider');
    
    btn.addEventListener('click', function() {
        const name = document.getElementById('scholar-name').value;
        if (!name) {
            alert('Silakan masukkan nama dosen!');
            return;
        }

        btn.disabled = true;
        btn.innerText = 'Searching on Scholar...';
        status.style.display = 'block';
        spiderIcon.classList.add('fa-spin');
        progress.style.width = '30%';
        log.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Mencari profil <b>${name}</b> di Google Scholar...`;

        fetch(`/crawl-scholar?name=${encodeURIComponent(name)}`)
            .then(r => r.json())
            .then(data => {
                progress.style.width = '100%';
                spiderIcon.classList.remove('fa-spin');
                btn.disabled = false;
                btn.innerText = 'Cari di Scholar';

                if (data.success) {
                    log.innerHTML = `
                        <div style="color: #22c55e; font-weight: bold; margin-bottom: 0.5rem;"><i class="fas fa-check-circle"></i> Berhasil menemukan profil!</div>
                        <div style="margin-bottom: 0.5rem;"><b>Scholar ID:</b> ${data.scholarId}</div>
                        <div style="margin-bottom: 0.5rem;"><b>Jumlah Publikasi:</b> ${data.publications_count}</div>
                        
                        ${data.titles ? `
                            <div style="margin-top: 1rem; font-weight: bold;">Top 5 Publikasi:</div>
                            <ul style="text-align: left; margin-top: 0.5rem; padding-left: 1.5rem;">
                                ${data.titles.map(t => `<li style="margin-bottom: 0.3rem;">${t}</li>`).join('')}
                            </ul>
                        ` : ''}
                    `;
                } else {
                    log.innerHTML = `<div style="color: #ef4444;"><i class="fas fa-times-circle"></i> Error: ${data.message}</div>`;
                }
            })
            .catch(err => {
                progress.style.width = '100%';
                spiderIcon.classList.remove('fa-spin');
                btn.disabled = false;
                btn.innerText = 'Cari di Scholar';
                log.innerHTML = `<div style="color: #ef4444;"><i class="fas fa-times-circle"></i> Error: Gagal menghubungi server.</div>`;
            });
    });
});
</script>
@endsection
