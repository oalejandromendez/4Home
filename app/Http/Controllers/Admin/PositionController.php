<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PositionRequest;
use App\Models\Admin\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PositionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_CARGOS');
        $this->middleware('permission:VER_CARGOS', ['only' => ['index']]);
        $this->middleware('permission:CREAR_CARGOS', ['only' => ['store']]);
        $this->middleware('permission:MODIFICAR_CARGOS', ['only' => ['update']]);
        $this->middleware('permission:ELIMINAR_CARGOS', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return Position::all();
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'PositionController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PositionRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $position = new Position();
            $position->fill($request->all());
            $position->status = 1;
            $position->save();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'PositionController:store', $e->getMessage()));
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
    public function update(PositionRequest $request, $id)
    {
        $validated = $request->validated();

        $position = Position::find($id);

        if (!$position instanceof Position) {
            return response()->json(['message' => 'El cargo no se encuentra en la base de datos'], 404);
        }

        try {
            DB::beginTransaction();
            $position->fill($request->all());
            $position->update();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'PositionController:update', $e->getMessage()));
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
            return response()->json(['message' => 'El id del cargo debe ser un campo numerico'], 400);
        }

        $position = Position::find($id);

        if (!$position instanceof Position) {
            return response()->json(['message' => 'El cargo no se encuentra en la base de datos'], 404);
        }

        DB::beginTransaction();
        try {
            $position->delete();

            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'PositionController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
