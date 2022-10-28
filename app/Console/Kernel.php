<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('broadcast')->everyMinute();
        $schedule->command('supplement')->everyMinute();
        $schedule->command('error_report')->everyMinute();
        $schedule->command('debt_report')->everyFiveMinutes();
        $schedule->command('channel:refresh')->everyMinute();
        $schedule->command('bot:refresh')->everyMinute();
        $schedule->command('chat:refresh')->dailyAt('21:10');
        $schedule->command('redis:refresh')->dailyAt('23:20');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
