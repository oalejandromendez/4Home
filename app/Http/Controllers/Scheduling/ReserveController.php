<?php

namespace App\Http\Controllers\Scheduling;

use App\Http\Controllers\Controller;
use App\Models\Scheduling\Reserve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReserveController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_RESERVAS');
        $this->middleware('permission:VER_RESERVAS', ['only' => ['index']]);
        $this->middleware('permission:CREAR_RESERVAS', ['only' => ['store']]);
        $this->middleware('permission:MODIFICAR_RESERVAS', ['only' => ['update']]);
        $this->middleware('permission:ELIMINAR_RESERVAS', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function findByCustomer($id)
    {
        try {
            $reservations = Reserve::where('user', $id)->get();
            return response()->json($reservations, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:findByCustomer', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
