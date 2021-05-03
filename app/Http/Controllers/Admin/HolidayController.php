<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HolidayRequest;
use App\Http\Resources\Admin\HolidayResource;
use App\Models\Admin\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HolidayController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_FESTIVOS');
        $this->middleware('permission:VER_FESTIVOS', ['only' => ['index']]);
        $this->middleware('permission:CREAR_FESTIVOS', ['only' => ['store']]);
        $this->middleware('permission:MODIFICAR_FESTIVOS', ['only' => ['update']]);
        $this->middleware('permission:ELIMINAR_FESTIVOS', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return new HolidayResource(Holiday::all());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'HolidayController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(HolidayRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $holiday = new Holiday();
            $holiday->fill($request->all());
            $holiday->save();
            DB::commit();
            return response()->json($holiday);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'HolidayController:store', $e->getMessage()));
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
    public function update(HolidayRequest $request, $id)
    {
        $validated = $request->validated();

        $holiday = Holiday::find($id);

        if (!$holiday instanceof Holiday) {
            return response()->json(['message' => 'El festivo no se encuentra en la base de datos'], 404);
        }

        try {
            DB::beginTransaction();
            $holiday->fill($request->all());
            $holiday->update();
            DB::commit();
            return response()->json($holiday);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'HolidayController:update', $e->getMessage()));
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
            return response()->json(['message' => 'El id del festivo debe ser un campo numerico'], 400);
        }

        $holiday = Holiday::find($id);

        if (!$holiday instanceof Holiday) {
            return response()->json(['message' => 'El festivo no se encuentra en la base de datos'], 404);
        }

        DB::beginTransaction();
        try {
            $holiday->delete();

            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'HolidayController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
