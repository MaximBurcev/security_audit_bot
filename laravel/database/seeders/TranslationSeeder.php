<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Translation $model)
    {
        $model::truncate();

        // @todo Works correctly, but I don't like that way
        //$model::factory(50)->create();
    }
}
