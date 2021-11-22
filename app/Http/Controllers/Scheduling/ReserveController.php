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
use Illuminate\Database\Eloquent\Builder;

class ReserveController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_RESERVAS');
        $this->middleware('permission:VER_RESERVAS', ['only' => ['index']]);
        $this->middleware('permission:CREAR_RESERVAS', ['only' => ['store']]);
        $this->middleware('permission:MODIFICAR_RESERVAS', ['only' => ['update']]);
        $this->middleware('permission:ELIMINAR_RESERVAS', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $reservations = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day')->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReserveRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {

            $reserve = new Reserve();
            $reserve->user = $request->get('user');
            $reserve->customer_address = $request->get('customer_address');
            $reserve->service = $request->get('service');
            $reserve->type = $request->get('type');
            $reserve->status = 1;
            $reserve->reference = Carbon::now()->timestamp . $reserve->id;
            $reserve->save();

            foreach ($request->get('days') as $day) {

                if ($reserve->type == 1) {
                    $newDay = new ReserveDay();
                    $newDay->date = $day['date'];
                    $day = (new Carbon($day['date']))->dayOfWeek;
                    if ($day == 0) {
                        $day = 6;
                    } else {
                        $day--;
                    }
                    $newDay->day = $day;
                    $newDay->reserve = $reserve->id;
                    $newDay->save();
                }

                if ($reserve->type == 2) {
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
            Log::error(sprintf('%s:%s', 'ReserveController:store', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(ReserveRequest $request, $id)
    {
        $validated = $request->validated();

        $reserve = Reserve::with('reserve_day')->find($id);

        if (!$reserve instanceof Reserve) {
            return response()->json(['message' => 'La reserva no se encuentra en la base de datos'], 404);
        }
        try {

            DB::beginTransaction();

            $reserve->reserve_day()->delete();

            $reserve->user = $request->get('user');
            $reserve->customer_address = $request->get('customer_address');
            $reserve->service = $request->get('service');
            $reserve->type = $request->get('type');

            $reserve->update();

            foreach ($request->get('days') as $day) {

                if ($reserve->type == 1) {
                    $newDay = new ReserveDay();
                    $newDay->date = $day['date'];
                    $day = (new Carbon($day['date']))->dayOfWeek;
                    if ($day == 0) {
                        $day = 6;
                    } else {
                        $day--;
                    }
                    $newDay->day = $day;
                    $newDay->reserve = $reserve->id;
                    $newDay->save();
                }

                if ($reserve->type == 2) {
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
            Log::error(sprintf('%s:%s', 'ReserveController:update', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['message' => 'El id de la reserva debe ser un campo numerico'], 400);
        }

        $reserve = Reserve::find($id);

        if (!$reserve instanceof Reserve) {
            return response()->json(['message' => 'La reserva no se encuentra en la base de datos'], 404);
        }

        DB::beginTransaction();
        try {
            $reserve->delete();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ReserveController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function findByCustomer($id)
    {
        try {
            $reservations = Reserve::with('user', 'customer_address', 'service.working_day', 'reserve_day', 'professional', 'supervisor')->where('user', $id)->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:findByCustomer', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function findByReference($reference)
    {
        try {
            $reservation = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day', 'professional', 'supervisor')->where('reference', $reference)->first();

            return response()->json($reservation, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:findByReference', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function findScheduleByCustomer($id)
    {
        try {
            $reservations = Reserve::with('user', 'customer_address', 'service.working_day', 'reserve_day', 'professional', 'supervisor', 'payment')
                ->where('user', $id)
                ->where('status', 4)
                ->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:findByCustomer', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function filterByStatus($status)
    {
        try {
            $reservations = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day')
                ->where('status', $status)
                ->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:filterByStatus', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reportSchedule(Request $request)
    {
        try {

            $idCustomers = [];
            if (!empty($request->get('customers'))) {
                $idCustomers = array_column($request->get('customers'), 'id');
            }

            $sporadic = ReserveDay::with('reserve.professional', 'reserve.user', 'reserve.customer_address', 'reserve')
                ->whereHas('reserve', function (Builder $query) use ($request) {
                    $query->where('status', '!=', 1)
                        ->where('type', 1);
                })->whereBetween('date', [$request->get('init') . " 00:00:00", $request->get('end') . " 23:59:59"]);

            if (count($idCustomers) > 0) {
                $sporadic = $sporadic->whereHas('reserve', function (Builder $query) use ($idCustomers, $request) {
                    $query->whereIn('reserve.user', $idCustomers);
                });
            }

            $sporadic = $sporadic->get();

            $init = (new Carbon($request->get('init') . " 00:00:00"))->subMonth();
            $monthly = ReserveDay::with('reserve.professional', 'reserve.user', 'reserve.customer_address', 'reserve')
                ->whereHas('reserve', function (Builder $query) use ($request, $init) {
                    $query->where('status', '!=', 1)
                        ->where('type', 2)
                        ->whereBetween('scheduling_date', [$init->toDateString() . " 00:00:00", $request->get('end') . " 23:59:59"]);
                });

            if (count($idCustomers) > 0) {
                $monthly = $monthly->whereHas('reserve', function (Builder $query) use ($idCustomers, $request) {
                    $query->whereIn('reserve.user', $idCustomers);
                });
            }


            $monthly = $monthly->get();

            $schedule = [
                'sporadic' => $sporadic,
                'monthly' => $monthly
            ];

            return response()->json($schedule, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:reportSchedule', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reportExpiration()
    {
        try {
            $reservations = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day', 'professional')
                ->where('status', 3)
                ->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:reportExpiration', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reportHistory(Request $request)
    {
        try {
            if (is_null($request->get('user'))) {
                $reservations = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day', 'professional')
                    ->where('status', 10)
                    ->whereBetween('scheduling_date', [$request->get('init') . " 00:00:00", $request->get('end') . " 23:59:59"])
                    ->get();
            } else {
                $reservations = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day', 'professional')
                    ->whereHas('user', function (Builder $query) use ($request) {
                        $query->where('id', $request->get('user'));
                    })
                    ->where('status', 10)
                    ->whereBetween('scheduling_date', [$request->get('init') . " 00:00:00", $request->get('end') . " 23:59:59"])
                    ->get();
            }
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:reportHistory', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reportPendingPayments()
    {
        try {
            $reservations = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day', 'professional')
                ->where('status', 2)
                ->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:reportPendingPayments', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function reportProfessional(Request $request)
    {
        try {
            $reservations = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day', 'professional')
                ->whereHas('professional', function (Builder $query) use ($request) {
                    $query->where('id', $request->get('professional'));
                })
                ->whereBetween('scheduling_date', [$request->get('init') . " 00:00:00", $request->get('end') . " 23:59:59"])
                ->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:reportProfessional', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
