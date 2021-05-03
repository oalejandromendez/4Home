<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduling\CustomerRequest;
use App\Http\Resources\Scheduling\CustomerTypeResource;
use App\Models\Scheduling\CustomerAddress;
use App\Models\Scheduling\CustomerType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class SignUpController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function customerType()
    {
        try {
            return new CustomerTypeResource(CustomerType::all());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'CustomerTypeController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
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

    public function validateEmail(Request $request)
    {
        try {
            if($request->get('email')) {
                return response()->json(User::where('email', Str::upper($request->get('email')))->first());
            }
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'UserController:email', $e->getMessage()));
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
}
