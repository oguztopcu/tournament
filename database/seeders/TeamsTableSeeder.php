<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeamsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = [
            [
                'name' => 'Beşiktaş'
            ],
            [
                'name' => 'Fenerbahçe'
            ],
            [
                'name' => 'Trabzonspor'
            ],
            [
                'name' => 'Galatasaray'
            ]
        ];

        foreach ($teams as $team) {
            if (Team::query()->where('name', '=', $team['name'])->exists()) {
                continue;
            }

            Team::query()->create($team);
        }
    }
}
