<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$lecturers = DB::table('lecturers')->pluck('id')->toArray();

foreach ($lecturers as $lId) {
    // Generate Research
    $researchCount = rand(1, 10);
    for ($i = 0; $i < $researchCount; $i++) {
        DB::table('research')->insert([
            'lecturerId' => $lId,
            'title' => 'Penelitian Dummy ' . rand(100, 999),
            'source' => ['Perguruan Tinggi', 'Lembaga Dalam Negeri', 'Mandiri'][rand(0, 2)],
            'year' => rand(2024, 2026)
        ]);
    }

    // Generate Pengabdian
    $pengabdianCount = rand(1, 5);
    // Let's store Pengabdian in publications with a specific source or in research. Wait, I added a "totalPengabdian" field in DashboardController but I need to make sure how it's calculated.
    // In DashboardController, totalResearch is calculated by summing scholar + scopus from the lecturers table!
    // And totalPengabdian is hardcoded to 0.
    
    // Update the lecturer table with scholar and scopus counts so the "Output Penelitian" becomes non-zero
    DB::table('lecturers')->where('id', $lId)->update([
        'scholar' => rand(10, 100),
        'scopus' => rand(0, 20),
        'pengabdian' => rand(0, 15),
        'hIndex' => rand(1, 15)
    ]);
}
echo "Dummy data generated!\n";
