<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfessionalRequest;
use App\Http\Resources\Admin\ProfessionalResource;
use App\Models\Admin\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProfessionalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_PROFESIONALES');
        $this->middleware('permission:VER_PROFESIONALES', ['only' => ['index']]);
        $this->middleware('permission:CREAR_PROFESIONALES', ['only' => ['store', 'validateIdentification', 'validateEmail']]);
        $this->middleware('permission:MODIFICAR_PROFESIONALES', ['only' => ['update', 'validateIdentification', 'validateEmail']]);
        $this->middleware('permission:ELIMINAR_PROFESIONALES', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return new ProfessionalResource(Professional::all());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ProfessionalController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProfessionalRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $professional = new Professional();
            $professional->fill($request->all());
            $professional->status = 1;
            $professional->save();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ProfessionalController:store', $e->getMessage()));
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
    public function update(ProfessionalRequest $request, $id)
    {
        $validated = $request->validated();

        $professional = Professional::find($id);

        if (!$professional instanceof Professional) {
            return response()->json(['message' => 'El profesional no se encuentra en la base de datos'], 404);
        }

        try {
            DB::beginTransaction();
            $professional->fill($request->all());
            $professional->update();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ProfessionalController:update', $e->getMessage()));
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
            return response()->json(['message' => 'El id del profesional debe ser un campo numerico'], 400);
        }

        $professional = Professional::find($id);

        if (!$professional instanceof Professional) {
            return response()->json(['message' => 'El profesional no se encuentra en la base de datos'], 404);
        }

        DB::beginTransaction();
        try {
            $professional->delete();

            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'ProfessionalController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function validateIdentification($identification)
    {
        try {

            $professional = Professional::where('identification', $identification)->first();
            if(isset($professional)) {
                return response()->json(new ProfessionalResource($professional));
            } else {
                return response()->json(null);
            }
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ProfessionalController:validateIdentification', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function validateEmail(Request $request)
    {
        try {
            if($request->get('email')) {

                $professional = Professional::where('email',Str::upper($request->get('email')))->first();
                if(isset($professional)) {
                    return response()->json(new ProfessionalResource($professional));
                } else {
                    return response()->json(null);
                }
            }
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ProfessionalController:validateEmail', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
