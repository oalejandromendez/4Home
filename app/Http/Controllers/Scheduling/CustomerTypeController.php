<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduling\CustomerTypeRequest;
use App\Http\Resources\Scheduling\CustomerTypeResource;
use App\Models\Scheduling\CustomerType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_TIPO_CLIENTE');
        $this->middleware('permission:VER_TIPO_CLIENTE', ['only' => ['index']]);
        $this->middleware('permission:CREAR_TIPO_CLIENTE', ['only' => ['store']]);
        $this->middleware('permission:MODIFICAR_TIPO_CLIENTE', ['only' => ['update']]);
        $this->middleware('permission:ELIMINAR_TIPO_CLIENTE', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return new CustomerTypeResource(CustomerType::all());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'CustomerTypeController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerTypeRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $customerType = new CustomerType();
            $customerType->fill($request->all());
            $customerType->status = 1;
            $customerType->save();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'CustomerTypeController:store', $e->getMessage()));
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
    public function update(CustomerTypeRequest $request, $id)
    {
        $validated = $request->validated();

        $customerType = CustomerType::find($id);

        if (!$customerType instanceof CustomerType) {
            return response()->json(['message' => 'El tipo de cliente no se encuentra en la base de datos'], 404);
        }

        try {
            DB::beginTransaction();
            $customerType->fill($request->all());
            $customerType->update();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'CustomerTypeController:update', $e->getMessage()));
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
            return response()->json(['message' => 'El id del tipo de cliente debe ser un campo numerico'], 400);
        }

        $customerType = CustomerType::find($id);

        if (!$customerType instanceof CustomerType) {
            return response()->json(['message' => 'El tipo de cliente no se encuentra en la base de datos'], 404);
        }

        DB::beginTransaction();
        try {
            $customerType->delete();

            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'CustomerTypeController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }
}
