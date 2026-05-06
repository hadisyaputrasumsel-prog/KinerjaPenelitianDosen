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
}
