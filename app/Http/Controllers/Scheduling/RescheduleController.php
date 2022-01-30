<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduling\ReserveRequest;
use App\Models\Scheduling\Reserve;
use App\Models\Scheduling\ReserveDay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RescheduleController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReserveRequest $request)
    {

        $reserve =  Reserve::with('reserve_day')->find($request->get('id'));

        if (!$reserve instanceof Reserve) {
            return response()->json(['message' => 'La reserva no se encuentra en la base de datos'], 404);
        }

        try {

            DB::beginTransaction();

            $reserve->reserve_day()->delete();
            $reserve->status = 9;
            $reserve->professional = null;
            $reserve->supervisor = null;
            $reserve->update();

            foreach($request->get('days') as $day) {

                if($reserve->type == 1) {
                    $newDay = new ReserveDay();
                    $newDay->date = $day['date'];
                    $day = (new Carbon($day['date']))->dayOfWeek;
                    if($day == 0) {
                        $day = 6;
                    } else {
                        $day--;
                    }
                    $newDay->day = $day;
                    $newDay->reserve = $reserve->id;
                    $newDay->save();
                }

                if($reserve->type == 2) {
                    $newDay = new ReserveDay();
                    $newDay->day = $day['day'];
                    $newDay->reserve = $reserve->id;
                    $newDay->save();
                }
            }
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'RescheduleController:store', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
