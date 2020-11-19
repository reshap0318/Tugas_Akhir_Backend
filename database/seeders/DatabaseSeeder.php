<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User::factory()->count(4)->create();
        $this->call(UnitSeeder::class);
        $this->call(PeriodSeeder::class);
        $this->call(TopicSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(UserSeeder::class);
    }
}
