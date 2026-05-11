<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
    public function index()
    {
        $jsonPath = database_path('data/lecturers.json');
        $lecturers = json_decode(File::get($jsonPath), true);

        // Calculate Stats
        $totalLecturers = count($lecturers);
        $totalResearch = array_sum(array_column($lecturers, 'scholar')) + array_sum(array_column($lecturers, 'scopus'));
        $avgSinta = round(array_sum(array_column($lecturers, 'sintaOverall')) / $totalLecturers);
        $avgSinta3Yr = round(array_sum(array_column($lecturers, 'sinta3Yr')) / $totalLecturers);
        
        $stats = [
            'totalLecturers' => $totalLecturers,
            'totalResearch' => $totalResearch,
            'avgSinta' => $avgSinta,
            'avgSinta3Yr' => $avgSinta3Yr,
            'productivityRatio' => round((count(array_filter($lecturers, fn($l) => $l['sinta3Yr'] >= 50)) / $totalLecturers) * 100),
            'productiveCount' => count(array_filter($lecturers, fn($l) => $l['sinta3Yr'] >= 50)),
            'lessActiveCount' => count(array_filter($lecturers, fn($l) => $l['sinta3Yr'] < 50)),
            'unggulCount' => count(array_filter($lecturers, fn($l) => $l['sinta3Yr'] > $avgSinta3Yr)),
            'baikCount' => count(array_filter($lecturers, fn($l) => $l['sinta3Yr'] > 0 && $l['sinta3Yr'] <= $avgSinta3Yr)),
            'perluCount' => count(array_filter($lecturers, fn($l) => $l['sinta3Yr'] == 0)),
        ];

        return view('dashboard', compact('lecturers', 'stats'));
    }

    public function lecturers()
    {
        $jsonPath = database_path('data/lecturers.json');
        $lecturers = json_decode(File::get($jsonPath), true);
        
        // Stats for filtering
        $avgSinta3Yr = round(array_sum(array_column($lecturers, 'sinta3Yr')) / count($lecturers));

        return view('lecturers', compact('lecturers', 'avgSinta3Yr'));
    }

    public function crawl()
    {
        return view('crawl');
    }

    public function analytics()
    {
        $jsonPath = database_path('data/lecturers.json');
        $lecturers = json_decode(File::get($jsonPath), true);
        return view('analytics', compact('lecturers'));
    }

    public function sintaProxy(\Illuminate\Http\Request $request)
    {
        $name = $request->query('name');
        if (!$name) {
            return response()->json(["error" => "No name provided"]);
        }

        $url = "https://sinta.kemdiktisaintek.go.id/authors?q=" . urlencode($name);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $headers = [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.9,id;q=0.8',
            'Connection: keep-alive',
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $html = curl_exec($ch);
        curl_close($ch);

        if (preg_match_all('/profile\/(\d+)/', $html, $matches)) {
            $id = $matches[1][0];
            return response()->json(["success" => true, "id" => $id]);
        } else {
            return response()->json(["success" => false, "message" => "ID not found or blocked by Cloudflare"]);
        }
    }

    public function accreditation()
    {
        $research = json_decode(file_get_contents(database_path('data/research.json')), true);
        $publications = json_decode(file_get_contents(database_path('data/publications.json')), true);

        // Define years
        $ts = 2026;
        $ts1 = 2025;
        $ts2 = 2024;

        // Table 3.b.2 Logic
        $t3b2_data = [
            'pt_mandiri' => ['ts2' => 0, 'ts1' => 0, 'ts' => 0],
            'nasional' => ['ts2' => 0, 'ts1' => 0, 'ts' => 0],
            'internasional' => ['ts2' => 0, 'ts1' => 0, 'ts' => 0],
        ];

        foreach ($research as $r) {
            $yearKey = $r['year'] == $ts ? 'ts' : ($r['year'] == $ts1 ? 'ts1' : ($r['year'] == $ts2 ? 'ts2' : null));
            if (!$yearKey) continue;

            if ($r['source'] == 'Perguruan Tinggi' || $r['source'] == 'Mandiri') {
                $t3b2_data['pt_mandiri'][$yearKey]++;
            } elseif ($r['source'] == 'Lembaga Dalam Negeri') {
                $t3b2_data['nasional'][$yearKey]++;
            } elseif ($r['source'] == 'Lembaga Luar Negeri') {
                $t3b2_data['internasional'][$yearKey]++;
            }
        }

        $table_3b2 = [
            [
                'no' => 1,
                'sumber' => "a) Perguruan tinggi\nb) Mandiri",
                'ts2' => $t3b2_data['pt_mandiri']['ts2'],
                'ts1' => $t3b2_data['pt_mandiri']['ts1'],
                'ts' => $t3b2_data['pt_mandiri']['ts'],
                'jumlah' => array_sum($t3b2_data['pt_mandiri'])
            ],
            [
                'no' => 2,
                'sumber' => 'Lembaga dalam negeri (diluar PT)',
                'ts2' => $t3b2_data['nasional']['ts2'],
                'ts1' => $t3b2_data['nasional']['ts1'],
                'ts' => $t3b2_data['nasional']['ts'],
                'jumlah' => array_sum($t3b2_data['nasional'])
            ],
            [
                'no' => 3,
                'sumber' => 'Lembaga luar negeri',
                'ts2' => $t3b2_data['internasional']['ts2'],
                'ts1' => $t3b2_data['internasional']['ts1'],
                'ts' => $t3b2_data['internasional']['ts'],
                'jumlah' => array_sum($t3b2_data['internasional'])
            ]
        ];

        // Table 3.b.4 Logic
        $t3b4_data = array_fill(1, 10, ['ts2' => 0, 'ts1' => 0, 'ts' => 0]);

        foreach ($publications as $p) {
            $yearKey = $p['year'] == $ts ? 'ts' : ($p['year'] == $ts1 ? 'ts1' : ($p['year'] == $ts2 ? 'ts2' : null));
            if (!$yearKey) continue;

            $source = $p['source'];
            if ($source == 'Scholar') {
                $t3b4_data[1][$yearKey]++; // Jurnal tidak terakreditasi
            } elseif (str_contains($source, 'SINTA')) {
                $t3b4_data[2][$yearKey]++; // Jurnal Nasional Terakreditasi
            } elseif ($source == 'Scopus Q3' || $source == 'Scopus Q4') {
                $t3b4_data[3][$yearKey]++; // Jurnal Internasional
            } elseif ($source == 'Scopus Q1' || $source == 'Scopus Q2') {
                $t3b4_data[4][$yearKey]++; // Jurnal Internasional Bereputasi
            }
        }

        $table_3b4_names = [
            1 => 'Jurnal penelitian tidak terakreditasi',
            2 => 'Jurnal penelitian nasional terakreditasi',
            3 => 'Jurnal penelitian internasional',
            4 => 'Jurnal penelitian internasional bereputasi',
            5 => 'Seminar wilayah/lokal/perguruan tinggi',
            6 => 'Seminar nasional',
            7 => 'Seminar internasional',
            8 => 'Pagelaran/pameran/presentasi dalam forum di tingkat wilayah',
            9 => 'Pagelaran/pameran/presentasi dalam forum di tingkat nasional',
            10 => 'Pagelaran/pameran/presentasi dalam forum di tingkat internasional',
        ];

        $table_3b4 = [];
        foreach ($table_3b4_names as $no => $name) {
            $table_3b4[] = [
                'no' => $no,
                'jenis' => $name,
                'ts2' => $t3b4_data[$no]['ts2'],
                'ts1' => $t3b4_data[$no]['ts1'],
                'ts' => $t3b4_data[$no]['ts'],
                'jumlah' => array_sum($t3b4_data[$no])
            ];
        }

        return view('accreditation', compact('table_3b2', 'table_3b4'));
    }

    public function crawlScholar(\Illuminate\Http\Request $request)
    {
        $name = $request->input('name');
        if (!$name) {
            return response()->json(["success" => false, "message" => "Name is required"]);
        }

        $url = "https://scholar.google.com/citations?view_op=search_authors&mauthors=" . urlencode($name);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        
        $html = curl_exec($ch);
        curl_close($ch);

        // Find Scholar ID
        if (preg_match('/user=([^&"]+)/', $html, $matches)) {
            $scholarId = $matches[1];
            
            // Now fetch profile
            $profileUrl = "https://scholar.google.com/citations?user=" . $scholarId;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $profileUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
            $profileHtml = curl_exec($ch);
            curl_close($ch);

            // Parse publications (simple regex for titles)
            if (preg_match_all('/class="gsc_a_at">([^<]+)<\/a>/', $profileHtml, $titleMatches)) {
                $titles = $titleMatches[1];
                return response()->json([
                    "success" => true, 
                    "scholarId" => $scholarId,
                    "publications_count" => count($titles),
                    "titles" => array_slice($titles, 0, 5) // Return top 5
                ]);
            }

            return response()->json(["success" => true, "scholarId" => $scholarId, "message" => "Found profile but failed to parse publications"]);
        }

        return response()->json(["success" => false, "message" => "Profile not found on Google Scholar"]);
    }
}
