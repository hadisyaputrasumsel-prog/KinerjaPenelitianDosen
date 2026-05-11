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

    <button id="start-crawl" class="glass glass-hover" style="background: var(--primary); border: none; padding: 1rem 2rem; color: white; font-weight: 600; cursor: pointer; border-radius: 12px;">
        Start Multi-Source Crawling
    </button>

    <div id="crawl-status" style="margin-top: 2rem; display: none;">
        <div style="height: 4px; background: rgba(0,0,0,0.1); border-radius: 10px; overflow: hidden; margin-bottom: 1rem;">
            <div id="crawl-progress" style="height: 100%; width: 0%; background: linear-gradient(to right, #0ea5e9, #8b5cf6); transition: width 0.3s;"></div>
        </div>
        <p id="crawl-log" style="font-family: monospace; font-size: 0.8rem; color: #22c55e;"></p>
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
        btn.disabled = true;
        btn.innerText = 'Crawling in Progress...';
        status.style.display = 'block';
        spiderIcon.classList.add('fa-spin');
        
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
                spiderIcon.classList.remove('fa-spin');
                return;
            }
            
            const perc = ((step + 1) / logs.length) * 100;
            progress.style.width = `${perc}%`;
            log.innerText = `> ${logs[step]}`;
            step++;
        }, 1500);
    });
});
</script>
@endsection
