<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunPythonScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-python-score';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
   public function handle()
{
    $this->info('Running Python score calculation...');
    $output = shell_exec('python C:\xampp\htdocs\Recess-work\app\recommendation_engine\calculate_score.py 2>&1');
    $this->info($output);
}
}