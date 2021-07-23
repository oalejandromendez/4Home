<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceTypeRequest;
use App\Http\Resources\Admin\ServiceTypeResource;
use App\Models\Admin\ServiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceTypeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:VER_TIPO_SERVICIO', ['only' => ['index']]);
        $this->middleware('permission:CREAR_TIPO_SERVICIO', ['only' => ['store']]);
        $this->middleware('permission:MODIFICAR_TIPO_SERVICIO', ['only' => ['update']]);
        $this->middleware('permission:ELIMINAR_TIPO_SERVICIO', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return new ServiceTypeResource(ServiceType::all());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ServiceTypeController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceTypeRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $serviceType = new ServiceType();
            $serviceType->fill($request->all());
            $serviceType->status = 1;
            $serviceType->save();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ServiceTypeController:store', $e->getMessage()));
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
    public function update(ServiceTypeRequest $request, $id)
    {
        $validated = $request->validated();

        $serviceType = ServiceType::find($id);

        if (!$serviceType instanceof ServiceType) {
            return response()->json(['message' => 'El tipo de servicio no se encuentra en la base de datos'], 404);
        }

        try {
            DB::beginTransaction();
            $serviceType->fill($request->all());
            $serviceType->update();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ServiceTypeController:update', $e->getMessage()));
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
            return response()->json(['message' => 'El id del tipo de servicio debe ser un campo numerico'], 400);
        }

        $serviceType = ServiceType::find($id);

        if (!$serviceType instanceof ServiceType) {
            return response()->json(['message' => 'El tio de servicio no se encuentra en la base de datos'], 404);
        }

        $working = $serviceType->working_day;

        if(count($working)) {
            return response()->json(['message' => 'El tipo de servicio tiene jornadas asociadas'], 500);
        }

        DB::beginTransaction();
        try {
            $serviceType->delete();

            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ServiceTypeController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
