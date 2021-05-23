<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduling\ReserveRequest;
use App\Models\Scheduling\Reserve;
use App\Models\Scheduling\ReserveDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $reservations = Reserve::with('user', 'customer_address', 'service.working_day', 'service_day')->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:index', $e->getMessage()));
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
            $reserve->save();

            foreach($request->get('days') as $day) {

                if($reserve->type == 1) {
                    $newDay = new ReserveDay();
                    $newDay->date = $day['date'];
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
            Log::error(sprintf('%s:%s', 'ReserveController:store', $e->getMessage()));
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
    public function update(ReserveRequest $request, $id)
    {
        $validated = $request->validated();

        $reserve =  Reserve::with('service_day')->find($id);

        if (!$reserve instanceof Reserve) {
            return response()->json(['message' => 'La reserva no se encuentra en la base de datos'], 404);
        }
        try {

            DB::beginTransaction();

            $reserve->service_day()->delete();

            $reserve->user = $request->get('user');
            $reserve->customer_address = $request->get('customer_address');
            $reserve->service = $request->get('service');
            $reserve->type = $request->get('type');

            $reserve->update();

            foreach($request->get('days') as $day) {

                if($reserve->type == 1) {
                    $newDay = new ReserveDay();
                    $newDay->date = $day['date'];
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
            Log::error(sprintf('%s:%s', 'ReserveController:update', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['message' => 'El id de la reserva debe ser un campo numerico'], 400);
        }

        $reserve =  Reserve::find($id);

        if (!$reserve instanceof Reserve) {
            return response()->json(['message' => 'La reserva no se encuentra en la base de datos'], 404);
        }

        DB::beginTransaction();
        try {
            $reserve->delete();
            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ReserveController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function findByCustomer($id)
    {
        try {
            $reservations = Reserve::with('user', 'customer_address', 'service.working_day', 'service_day')->where('user', $id)->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:findByCustomer', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
