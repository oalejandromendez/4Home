<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduling\ScheduleRequest;
use App\Mail\CustomerServiceSchedulingEmail;
use App\Models\Admin\Service;
use App\Models\Scheduling\Reserve;
use App\Models\Scheduling\ReserveDay;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $schedule = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day')
            ->where('status', 2)
            ->get();
            return response()->json($schedule, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ScheduleController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ScheduleRequest $request)
    {
        $validated = $request->validated();

        $reserve =  Reserve::with('reserve_day')->find($request->get('id'));

        if (!$reserve instanceof Reserve) {
            return response()->json(['message' => 'La reserva no se encuentra en la base de datos'], 404);
        }

        try {

            DB::beginTransaction();

            $reserve->reserve_day()->delete();
            $reserve->professional = $request->get('professional');
            $reserve->supervisor = $request->get('supervisor');
            $reserve->status = 2;
            $reserve->scheduling_date = new Carbon();
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

            $user = User::find($reserve->user);
            $service = Service::find($reserve->service);
            if(isset($user->email)) {
                $fullname = $user->name . " " . $user->lastname;
            $reference = $reserve->reference;
                $value = $service->price * $service->quantity;
                Mail::to($user->email)->send(new CustomerServiceSchedulingEmail($fullname, $reference, $value));
            }
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ScheduleController:store', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
