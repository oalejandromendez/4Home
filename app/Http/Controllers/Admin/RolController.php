<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RolRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;


class RolController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_ROLES');
        $this->middleware('permission:VER_ROLES', ['only' => ['index']]);
        $this->middleware('permission:CREAR_ROLES', ['only' => ['store', 'validateName']]);
        $this->middleware('permission:MODIFICAR_ROLES', ['only' => ['update', 'validateName']]);
        $this->middleware('permission:ELIMINAR_ROLES', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return Role::with('permissions')->get();
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'RolController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RolRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $rol = Role::create($request->except('permissions'));
            $permissions = $request->input('permissions') ? $request->input('permissions') : [];
            $rol->givePermissionTo($permissions);

            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'RolController:store', $e->getMessage()));
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
    public function update(RolRequest $request, $id)
    {
        $validated = $request->validated();
        DB::beginTransaction();
        try {
            $rol = Role::findOrFail($id);
            $rol->update($request->except('permissions'));
            $permissions = $request->input('permissions') ? $request->input('permissions') : [];
            $rol->syncPermissions($permissions);
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'RolController:store', $e->getMessage()));
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
            return response()->json(['message' => 'El id del rol debe ser un campo numerico'], 400);
        }
        $rol = Role::findOrFail($id);
        DB::beginTransaction();
        try {
            $rol->delete();
            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'RolController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function validateName($name)
    {
        try {
            return response()->json(Role::where('name', Str::upper($name))->first());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'RolController:name', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
