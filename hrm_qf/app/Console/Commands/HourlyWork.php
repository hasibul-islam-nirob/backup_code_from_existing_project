<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\POS\SynchronizeController;
use Illuminate\Support\Facades\DB;
class HourlyWork extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offline:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hourly Synconization Complete.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //  $controller = new SynchronizeController;
        //  $data = $controller->synchronize_data();
        //  dd( $data );
        //  $this->info('Hourly Sync Update Successfull.');

        // DB::table('test')->insert(['name' => 'Testing456']);
         
        // App\Http\Controllers\POS\SynchronizeController->synchronize_data();
        //
    }
}
