<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduling\CustomerRequest;
use App\Models\Scheduling\CustomerAddress;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_CLIENTES');
        $this->middleware('permission:VER_CLIENTES', ['only' => ['index']]);
        $this->middleware('permission:CREAR_CLIENTES', ['only' => ['store','validateIdentification', 'validateEmail']]);
        $this->middleware('permission:MODIFICAR_CLIENTES', ['only' => ['update' ,'validateIdentification', 'validateEmail']]);
        $this->middleware('permission:ELIMINAR_CLIENTES', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $users = User::with('customer_address')->whereHas('roles', function (Builder $query) {
                $query->where('name', 'CLIENTE');
            })->where('id', '!=', Auth::id())->get();
            return response()->json($users, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'CustomerController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
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

            foreach($request->get('addresses') as $address) {
                $newAddress = new CustomerAddress();
                $newAddress->user = $user->id;
                $newAddress->address = $address;
                $newAddress->save();
            }

            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'CustomerController:store', $e->getMessage()));
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
    public function update(CustomerRequest $request, $id)
    {
        $validated = $request->validated();

        $user =  User::with('customer_address')->find($id);

        if (!$user instanceof User) {
            return response()->json(['message' => 'El cliente no se encuentra en la base de datos'], 404);
        }
        try {

            DB::beginTransaction();

            $user->customer_address()->delete();

            $user->fill($request->except('password'));
            if ($request->get('password')) {
                $user->password = Hash::make($request->get('password'));
            }

            $user->status = $request->get('status');
            $user->reset_password = 0;
            $user->update();

            $roles = $request->input('roles') ? $request->input('roles') : [];
            $user->syncRoles($roles);

            foreach($request->get('addresses') as $address) {
                $newAddress = new CustomerAddress();
                $newAddress->user = $user->id;
                $newAddress->address = $address;
                $newAddress->save();
            }

            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'CustomerController:update', $e->getMessage()));
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
            return response()->json(['message' => 'El id del cliente debe ser un campo numerico'], 400);
        }
        $user = User::find($id);
        if (!$user instanceof User) {
            return response()->json(['message' => 'El cliente no se encuentra en la base de datos'], 404);
        }
        DB::beginTransaction();
        try {
            $user->delete();
            DB::commit();
            return response()->json( 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'CustomerController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function validateIdentification($identification)
    {
        try {
            $user = User::where('identification', $identification)->first();
            if(isset($user)) {
                return response()->json($user);
            } else {
                return response()->json(null);
            }
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'CustomerController:validateIdentification', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function findCustomer(Request $request)
    {
        try {

            $user = User::with('customer_address')
            ->whereHas('roles', function (Builder $query) {
                $query->where('name', 'CLIENTE');
            })
            ->whereHas('type_document', function (Builder $query) use($request) {
                $query->where('id', $request->get('type_document'));
            })
            ->where('identification', $request->get('identification'))->first();

            return response()->json($user, 200);

        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'CustomerController:findCustomer', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
