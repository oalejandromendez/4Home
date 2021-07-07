<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Finance\PromocodesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Gabievi\Promocodes\Facades\Promocodes;

class PromocodesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return Promocodes::all();
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'PromocodesController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PromocodesRequest $request)
    {
        $validated = $request->validated();
        try {

            if($request->get('disposable') == 0) {
                Promocodes::create(
                    $request->get('amount'),
                    $request->get('reward'),
                    [],
                    $request->get('expires'),
                    $request->get('quantity'),
                    false
                );
            }

            if($request->get('disposable') == 1) {
                Promocodes::createDisposable(
                    $request->get('amount'),
                    $request->get('reward'),
                    [],
                    $request->get('expires'),
                    $request->get('quantity')
                );
            }
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'PromocodesController:store', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request)
    {
        try {
            Promocodes::disable($request->get('code'));
            return response()->json(200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'PromocodesController:disable', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
