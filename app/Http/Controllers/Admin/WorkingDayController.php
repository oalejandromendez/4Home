<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WorkingDayRequest;
use App\Http\Resources\Admin\WorkingDayResource;
use App\Models\Admin\WorkingDay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkingDayController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_JORNADAS');
        $this->middleware('permission:VER_JORNADAS', ['only' => ['index']]);
        $this->middleware('permission:CREAR_JORNADAS', ['only' => ['store']]);
        $this->middleware('permission:MODIFICAR_JORNADAS', ['only' => ['update']]);
        $this->middleware('permission:ELIMINAR_JORNADAS', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return new WorkingDayResource(WorkingDay::all());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'WorkingDayController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(WorkingDayRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $workingDay = new WorkingDay();
            $workingDay->fill($request->all());
            $workingDay->status = 1;
            $workingDay->save();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'WorkingDayController:store', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(WorkingDayRequest $request, $id)
    {
        $validated = $request->validated();

        $workingDay = WorkingDay::find($id);

        if (!$workingDay instanceof WorkingDay) {
            return response()->json(['message' => 'La jornada no se encuentra en la base de datos'], 404);
        }

        try {
            DB::beginTransaction();
            $workingDay->fill($request->all());
            $workingDay->update();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'WorkingDayController:update', $e->getMessage()));
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
            return response()->json(['message' => 'El id de la jornada debe ser un campo numerico'], 400);
        }

        $workingDay = WorkingDay::find($id);

        if (!$workingDay instanceof WorkingDay) {
            return response()->json(['message' => 'La jornada no se encuentra en la base de datos'], 404);
        }

        DB::beginTransaction();
        try {
            $workingDay->delete();

            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'WorkingDayController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
