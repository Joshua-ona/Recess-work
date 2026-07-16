protected function schedule(Schedule $schedule)
{
$schedule->command('python:score')->dailyAt('10:00');
}