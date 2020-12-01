<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class TestModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Module::factory()->count(50)->create();
    }
}
