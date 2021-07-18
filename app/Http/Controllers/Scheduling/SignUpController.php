<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Http\Requests\Scheduling\CustomerRequest;
use App\Http\Resources\Scheduling\CustomerTypeResource;
use App\Mail\PasswordResetEmail;
use App\Models\Scheduling\CustomerAddress;
use App\Models\Scheduling\CustomerType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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
            Log::error(sprintf('%s:%s', 'SignUpController:customerType', $e->getMessage()));
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
            Log::error(sprintf('%s:%s', 'SignUpController:validateIdentification', $e->getMessage()));
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
            Log::error(sprintf('%s:%s', 'SignUpController:validateEmail', $e->getMessage()));
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
            Log::error(sprintf('%s:%s', 'SignUpController:store', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function resetpassword(Request $request)
    {
        try {
            $user = User::where('email', $request->get('email'))->first();
            if(isset($user)) {
                DB::beginTransaction();
                $fullname = $user->name . ' ' . $user->lastname;
                $password = $this->randomPassword();
                $user->password = Hash::make('0'.$password);
                $user->reset_password = 1;
                $user->update();
                Mail::to($request->get('email'))->send(new PasswordResetEmail($fullname, '0'.$password));
                DB::commit();
                return response()->json(200);
            } else {
                return response()->json([], 404);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'SignUpController:resetpassword', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function randomPassword($len = 8) {

        if($len < 8)
            $len = 8;

        $sets = array();
        $sets[] = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        $sets[] = '23456789';
        $sets[]  = '~!@#$%^&*(){}[],./?';

        $password = '';

        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
        }

        while(strlen($password) < $len) {
            $randomSet = $sets[array_rand($sets)];
            $password .= $randomSet[array_rand(str_split($randomSet))];
        }

        return str_shuffle($password);
    }
}
