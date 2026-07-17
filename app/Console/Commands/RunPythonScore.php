<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schedule;

class RunPythonScore extends Command
{
    protected $signature = 'python:score'; // match your scheduler
    protected $description = 'Run python script to calculate user engagement scores';

    public function handle()
    {
        $this->info('Running Python score calculation...');
        $path = base_path('app/recommendation_engine/calculate_score.py');
        $output = shell_exec("python \"{$path}\" 2>&1");
        $this->info($output);
        $this->info('Done');
    }
}