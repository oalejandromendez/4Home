<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_USUARIOS');
        $this->middleware('permission:VER_USUARIOS', ['only' => ['index']]);
        $this->middleware('permission:CREAR_USUARIOS', ['only' => ['store', 'validateEmail']]);
        $this->middleware('permission:MODIFICAR_USUARIOS', ['only' => ['update', 'validateEmail']]);
        $this->middleware('permission:ELIMINAR_USUARIOS', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::with('roles')->whereHas('roles', function (Builder $query) {
                $query->where('name', '<>' ,'CLIENTE');
            })->where('id', '!=', Auth::id())->get();
            return response()->json($users, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'UserController:index', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
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
        try {
            $user = User::with('roles', 'customer_address')->where('id', $id)->first();
            return response()->json($user, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'UserController:show', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $user = new User();
            $user->fill($request->all());
            $user->password = Hash::make($request->get('password'));
            $user->status = 1;
            $user->reset_password = 0;
            $user->save();
            $roles = $request->input('roles') ? $request->input('roles') : [];
            $user->assignRole($roles);
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'UserController:store', $e->getMessage()));
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
    public function update(UserRequest $request, $id)
    {
        $validated = $request->validated();
        $user = User::find($id);
        if (!$user instanceof User) {
            return response()->json(['message' => 'El usuario no se encuentra en la base de datos'], 404);
        }
        try {
            DB::beginTransaction();
            $user->fill($request->except('password'));
            if ($request->get('password')) {
                $user->password = Hash::make($request->get('password'));
            }

            $user->status = $request->get('status');
            $user->reset_password = 0;
            $user->update();

            $roles = $request->input('roles') ? $request->input('roles') : [];
            $user->syncRoles($roles);

            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'UserController:update', $e->getMessage()));
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
            return response()->json(['message' => 'El id del usuario debe ser un campo numerico'], 400);
        }
        $user = User::find($id);
        if (!$user instanceof User) {
            return response()->json(['message' => 'El usuario no se encuentra en la base de datos'], 404);
        }
        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'UserController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function validateEmail(Request $request)
    {
        try {
            if($request->get('email')) {
                return response()->json(User::where('email',Str::upper($request->get('email')))->first());
            }
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'UserController:email', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function permissions()
    {
        try {
            return response()->json(request()->user()->getAllPermissions());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'UserController:permissions', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function changePassword(Request $request)
    {
        if(!is_null($request->get('password')) && !is_null($request->get('id')) ) {
            $user = User::find($request->get('id'));
            if (!$user instanceof User) {
                return response()->json(['message' => 'El usuario no se encuentra en la base de datos'], 404);
            }
            try {
                DB::beginTransaction();
                if ($request->get('password')) {
                    $user->password = Hash::make($request->get('password'));
                }
                $user->reset_password = 0;
                $user->update();

                DB::commit();
                return response()->json(200);
            } catch (\Exception $e) {
                DB::rollback();
                Log::error(sprintf('%s:%s', 'UserController:changePassword', $e->getMessage()));
                return response()->json(['message' => $e->getMessage()], 500);
            }
        } else {
            return response()->json(['Error'], 500);
        }
    }
}
