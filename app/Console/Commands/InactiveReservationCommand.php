<?php

namespace App\Console\Commands;

use App\Models\Scheduling\Reserve;
use Carbon\Carbon;
use Illuminate\Console\Command;

class InactiveReservationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '4home:inactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inactivar las reservas que ya sobrepasaron el limite de pagos';

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
     * @return int
     */
    public function handle()
    {
        $reserves = Reserve::where('status', 2)->get();
        foreach($reserves as $reserve) {
            if( (new Carbon($reserve->scheduling_date))->addDay(1) < Carbon::now()) {
                $reserve->status = 3;
                $reserve->update();
            }
        }
    }
}
