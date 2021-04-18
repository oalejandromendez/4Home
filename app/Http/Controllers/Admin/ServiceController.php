<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Http\Resources\Admin\ServiceResource;
use App\Models\Admin\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_SERVICIOS');
        $this->middleware('permission:VER_SERVICIOS', ['only' => ['index']]);
        $this->middleware('permission:CREAR_SERVICIOS', ['only' => ['store']]);
        $this->middleware('permission:MODIFICAR_SERVICIOS', ['only' => ['update']]);
        $this->middleware('permission:ELIMINAR_SERVICIOS', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return new ServiceResource(Service::with('working_day')->get());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ServiceController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $service = new Service();
            $service->fill($request->all());
            $service->status = 1;
            $service->save();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ServiceController:store', $e->getMessage()));
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
    public function update(ServiceRequest $request, $id)
    {
        $validated = $request->validated();

        $service = Service::find($id);

        if (!$service instanceof Service) {
            return response()->json(['message' => 'El servicio no se encuentra en la base de datos'], 404);
        }

        try {
            DB::beginTransaction();
            $service->fill($request->all());
            $service->update();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ServiceController:update', $e->getMessage()));
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
            return response()->json(['message' => 'El id del servicio debe ser un campo numerico'], 400);
        }

        $service = Service::find($id);

        if (!$service instanceof Service) {
            return response()->json(['message' => 'El servicio no se encuentra en la base de datos'], 404);
        }

        DB::beginTransaction();
        try {
            $service->delete();

            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ServiceController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
