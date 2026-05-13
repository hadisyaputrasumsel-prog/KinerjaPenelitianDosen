<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Seed Lecturers
        $lecturersJson = \Illuminate\Support\Facades\File::get(database_path('data/lecturers.json'));
        $lecturers = json_decode($lecturersJson, true);
        foreach ($lecturers as $l) {
            \Illuminate\Support\Facades\DB::table('lecturers')->insert($l);
        }

        // Seed Research
        $researchJson = \Illuminate\Support\Facades\File::get(database_path('data/research.json'));
        $research = json_decode($researchJson, true);
        foreach ($research as $r) {
            \Illuminate\Support\Facades\DB::table('research')->insert($r);
        }

        // Seed Publications
        $publicationsJson = \Illuminate\Support\Facades\File::get(database_path('data/publications.json'));
        $publications = json_decode($publicationsJson, true);
        foreach ($publications as $p) {
            \Illuminate\Support\Facades\DB::table('publications')->insert($p);
        }
    }
}
