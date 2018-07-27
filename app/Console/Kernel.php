<?php

namespace App\Console;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use ManagesFrequencies;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
   protected $commands = [
         '\App\Console\Commands\CronJob',
    ];


    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {        
        // $schedule->call(function () {
        //     DB::table('current_user')->where('id',1)->update(['current_user_id' => rand(10,100)]);
        // })->everyMinute();
        
        $schedule->call('App\Http\Controllers\WooVismaController@CronJob')->everyMinute();
        
        // $schedule->command('CronJob:cronjob')->everyMinute();
    }


    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
 