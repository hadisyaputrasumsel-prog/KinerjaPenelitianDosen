<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UIGM - Kinerja Penelitian Dosen</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #0f2e5a;
            --primary-glow: rgba(15, 46, 90, 0.2);
            --secondary: #d7ac7c;
            --bg-dark: #f0f4f8;
            --bg-card: rgba(255, 255, 255, 0.85);
            --text-main: #1f2937;
            --text-muted: #64748b;
            --border-glass: rgba(15, 46, 90, 0.15);
            --glass-blur: blur(12px);
        }
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            background-image: 
                radial-gradient(at 0% 0%, rgba(215, 172, 124, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(15, 46, 90, 0.1) 0px, transparent 50%);
            color: var(--text-main);
            min-height: 100vh;
        }
        .glass {
            background: var(--bg-card);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: 1px solid var(--border-glass);
            border-radius: 16px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.1);
        }
        #app { display: grid; grid-template-columns: 260px 1fr; min-height: 100vh; }
        aside { padding: 2rem; background: var(--primary); color: white; border-right: 1px solid var(--border-glass); }
        main { padding: 2rem; overflow-y: auto; }
        .logo { margin-bottom: 2rem; text-align: center; }
        .logo img { max-width: 150px; }
        nav ul { list-style: none; }
        nav li { margin-bottom: 0.5rem; }
        nav a { display: flex; align-items: center; padding: 0.75rem 1rem; color: rgba(255, 255, 255, 0.7); text-decoration: none; border-radius: 10px; transition: all 0.2s; }
        nav a.active, nav a:hover { background: var(--secondary); color: #0f2e5a; font-weight: 600; }
        nav a i { margin-right: 1rem; }
        .realtime-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; background: rgba(34, 197, 94, 0.1); color: #4ade80; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .dot { width: 8px; height: 8px; background: #22c55e; border-radius: 50%; animation: pulse 2s infinite; }
        @keyframes pulse { 0% { transform: scale(0.95); } 70% { transform: scale(1); } 100% { transform: scale(0.95); } }
    </style>
</head>
<body>
    <div id="app">
        <aside>
            <div class="logo">
                <img src="https://www.uigm.ac.id/wp-content/uploads/2025/06/logo-uigm_putih.png" alt="Logo UIGM">
            </div>
            <nav>
                <ul>
                    <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                    <li><a href="{{ route('lecturers') }}" class="{{ request()->routeIs('lecturers') ? 'active' : '' }}"><i class="fas fa-users"></i> Data Dosen</a></li>
                    <li><a href="#"><i class="fas fa-spider"></i> Crawl Engine</a></li>
                    <li><a href="#"><i class="fas fa-project-diagram"></i> Analytics</a></li>
                </ul>
            </nav>
        </aside>
        
        <main>
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h1>Dashboard Kinerja Penelitian</h1>
                    <p style="color: var(--text-muted); font-size: 0.9rem;">Monitoring & Pelaporan Kinerja Penelitian Dosen UIGM.</p>
                </div>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <div class="realtime-badge">
                        <div class="dot"></div>
                        REAL-TIME UPDATING
                    </div>
                </div>
            </header>

            @yield('content')
        </main>
    </div>
</body>
</html>
