<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StatusRequest;
use App\Models\Admin\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatusController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_ESTADOS');
        $this->middleware('permission:VER_ESTADOS', ['only' => ['index']]);
        $this->middleware('permission:CREAR_ESTADOS', ['only' => ['store']]);
        $this->middleware('permission:MODIFICAR_ESTADOS', ['only' => ['update']]);
        $this->middleware('permission:ELIMINAR_ESTADOS', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return Status::all();
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'StatusController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StatusRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $status = new Status();
            $status->fill($request->all());
            $status->status = 1;
            $status->save();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'StatusController:store', $e->getMessage()));
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
    public function update(StatusRequest $request, $id)
    {
        $validated = $request->validated();

        $status = Status::find($id);

        if (!$status instanceof Status) {
            return response()->json(['message' => 'El estado no se encuentra en la base de datos'], 404);
        }

        try {
            DB::beginTransaction();
            $status->fill($request->all());
            $status->update();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'StatusController:update', $e->getMessage()));
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
            return response()->json(['message' => 'El id del estado debe ser un campo numerico'], 400);
        }

        $status = Status::find($id);

        if (!$status instanceof Status) {
            return response()->json(['message' => 'El estado no se encuentra en la base de datos'], 404);
        }

        $professional = $status->professional;

        if(count($professional)) {
            return response()->json(['message' => 'El estado tiene profesionales asociados'], 500);
        }

        DB::beginTransaction();
        try {
            $status->delete();

            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'StatusController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
