<?php

namespace App\Console;

use App\Http\Controllers\HR\Process\ApprovalScript;
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
       Commands\HourlyWork::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    // protected function schedule(Schedule $schedule)
    // {
    //     $schedule->command('offline:sync')
    //              ->everyMinute();
    // }


    protected function schedule(Schedule $schedule)
    {

        /* $schedule->command('queue:work --stop-when-empty')
            ->everyMinute(); */

        // $schedule->call(function () {
        //     echo "Test 1";
        //     dd('gfmgadkmfoadsfdsofadso');
        // })->everyMinute();
        // ->cron('1 * * * *');

        $schedule->call(new ApprovalScript)->everyMinute();

        //$schedule->command('offline:sync')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
