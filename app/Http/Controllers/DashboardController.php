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
}
