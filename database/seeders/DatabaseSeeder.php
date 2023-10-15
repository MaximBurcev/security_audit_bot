<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Audit;
use App\Models\AuditReport;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(5)->create();
        Utility::factory(5)->create();
        Project::factory(5)->create();
        Report::factory(5)->create();
        Audit::factory(5)->create();
        AuditReport::factory(5)->create();
    }
}
