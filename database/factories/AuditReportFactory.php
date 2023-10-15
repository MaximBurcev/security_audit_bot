<?php

namespace Database\Factories;

use App\Models\Audit;
use App\Models\AuditReport;
use App\Models\Report;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditReport>
 */
class AuditReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'audit_id' => Audit::all()->random(),
            'report_id' => Report::all()->random()
        ];
    }
}
