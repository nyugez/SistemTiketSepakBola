<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use App\Models\Stadium;
use App\Models\TicketCategory;
use Illuminate\Database\Seeder;

class FootballMatchSeeder extends Seeder
{
    public function run(): void
    {
        $gbk    = Stadium::where('name', 'Gelora Bung Karno')->first();
        $gbt    = Stadium::where('name', 'Gelora Bung Tomo')->first();
        $manahan = Stadium::where('name', 'Stadion Manahan')->first();

        $matches = [
            [
                'home_team'  => 'Persija Jakarta',
                'away_team'  => 'Persib Bandung',
                'stadium_id' => $gbk->id,
                'match_date' => now()->addDays(7)->setTime(19, 0),
                'status'     => 'scheduled',
                'categories' => [
                    ['name' => 'Tribun Ekonomi', 'price' => 75000,  'quota' => 5000],
                    ['name' => 'Tribun Barat',   'price' => 150000, 'quota' => 2000],
                    ['name' => 'VIP',            'price' => 350000, 'quota' => 500],
                    ['name' => 'VVIP',           'price' => 750000, 'quota' => 100],
                ],
            ],
            [
                'home_team'  => 'Persebaya Surabaya',
                'away_team'  => 'Arema FC',
                'stadium_id' => $gbt->id,
                'match_date' => now()->addDays(14)->setTime(15, 30),
                'status'     => 'scheduled',
                'categories' => [
                    ['name' => 'Tribun Ekonomi', 'price' => 50000,  'quota' => 8000],
                    ['name' => 'Tribun Utama',   'price' => 125000, 'quota' => 3000],
                    ['name' => 'VIP',            'price' => 300000, 'quota' => 300],
                ],
            ],
            [
                'home_team'  => 'Persis Solo',
                'away_team'  => 'PSS Sleman',
                'stadium_id' => $manahan->id,
                'match_date' => now()->addDays(21)->setTime(19, 30),
                'status'     => 'scheduled',
                'categories' => [
                    ['name' => 'Tribun Ekonomi', 'price' => 50000,  'quota' => 5000],
                    ['name' => 'Tribun Utama',   'price' => 100000, 'quota' => 1500],
                    ['name' => 'VIP',            'price' => 250000, 'quota' => 200],
                ],
            ],
        ];

        foreach ($matches as $matchData) {
            $categories = $matchData['categories'];
            unset($matchData['categories']);

            $match = FootballMatch::create($matchData);

            foreach ($categories as $cat) {
                TicketCategory::create(array_merge($cat, ['football_match_id' => $match->id]));
            }
        }
    }
}
