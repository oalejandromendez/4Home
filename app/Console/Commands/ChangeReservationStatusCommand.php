<?php

namespace App\Console\Commands;

use App\Models\Scheduling\Reserve;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChangeReservationStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '4home:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cerrar las reservas que ya fueron cumplidas';

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
        try {

            DB::beginTransaction();

            $reserves = Reserve::with('reserve_day')->where('status', 6 )->get();

            foreach($reserves as $reserve) {
                if($reserve->type == 2) {
                    $dateValidation = (new Carbon($reserve->scheduling_date))->addMonth();
                    if($dateValidation < Carbon::now()) {
                        $update = Reserve::find($reserve->id);
                        $update->status = 9;
                        $update->update();
                    }
                }
                if($reserve->type == 1) {
                    $flag = true;
                    foreach($reserve->reserve_day as $day) {
                        $validate = new Carbon($day->date);
                        if($validate > Carbon::now()) {
                            $flag = false;
                        }
                    }
                    if($flag) {
                        $update = Reserve::find($reserve->id);
                        $update->status = 9;
                        $update->update();
                    }
                }
            }
            DB::commit();
            Log::error(sprintf('%s:%s', 'ChangeReservationStatusCommand', $reserves));
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ChangeReservationStatusCommand', $e->getMessage()));
        }
    }
}
