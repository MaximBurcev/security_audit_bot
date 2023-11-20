<?php

namespace App\Console\Commands;

use App\Enums\EntityEnum;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\Utility;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class WarmupCacheCommand extends Command
{

    const ZERO_TTL = 0;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cache:warmup 
                            {entity? : What entity warm up}
                            {ttl? : Set Time to live}
                            {--clear : Clear all cache}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache warm up';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $entity = $this->argument('entity');
        $ttl = $this->argument('ttl');
        $clear = $this->option('clear');


        if (!$entity) {
            $question = "What entity cache warm up?";
            if ($clear) {
                $question = "What entity cache clear up?";
            }
            $entity = $this->anticipate($question, array_column(EntityEnum::cases(), 'value'),
                EntityEnum::ALL->value);
        }

        if (!$ttl && !$clear) {
            $ttl = $this->ask('Set ttl', config('cache.ttl'));
        }

        if ($clear) {
            $ttl = self::ZERO_TTL;
        }

        if ($entity == EntityEnum::ALL->value) {
            foreach (array_column(EntityEnum::cases(), 'value') as $entity) {
                Cache::put($entity . 'Count', Audit::all()->count(), $ttl);
            }

            if ($ttl == self::ZERO_TTL) {
                $this->info('Clear all cache completed!');
            } else {
                $this->info('Warm up all cache completed!');
            }

        } else {
            Cache::put($entity . 'Count', Audit::all()->count(), $ttl);

            if ($ttl == self::ZERO_TTL) {
                $this->info("Clear $entity cache completed!");
            } else {
                $this->info("Warm up $entity cache completed!");
            }

        }


    }
}
