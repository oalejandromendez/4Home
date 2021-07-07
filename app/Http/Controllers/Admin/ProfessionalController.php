<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfessionalRequest;
use App\Http\Resources\Admin\ProfessionalResource;
use App\Models\Admin\Professional;
use App\Models\Scheduling\Reserve;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

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
            return new ProfessionalResource(Professional::with('position', 'reserve.reserve_day', 'reserve.service.working_day' ,'status')->get());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ProfessionalController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
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
            $professional = Professional::with('position', 'reserve.reserve_day', 'reserve.service.working_day' ,'status')->where('id', $id)->first();
            if(isset($professional)) {
                return response()->json(new ProfessionalResource($professional));
            } else {
                return response()->json(null);
            }
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ProfessionalController:show', $e->getMessage()));
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


    public function checkAvailability(Request $request) {

        try {

            if($request->get('type') == 1) {

                $dates = [];
                $days = [];

                foreach( $request->get('days') as $date) {
                    array_push($dates, (new Carbon($date['date']))->toDateString());
                    $day = (new Carbon($date['date']))->dayOfWeek;
                    if($day == 0) {
                        $day = 6;
                    } else {
                        $day--;
                    }
                    array_push($days, $day);
                }

                $professionals = Professional::whereHas('status', function (Builder $query) {
                    $query->where('openSchedule', 1);
                })->whereDoesntHave('reserve.reserve_day', function($query) use($dates, $days) {
                    $query->whereIn('date', $dates)
                    ->WhereIn('day', $days);
                })->get();

                return response()->json($professionals, 200);
            }

            if($request->get('type') == 2) {

                $days = [];

                foreach( $request->get('days') as $date) {
                    array_push($days, $date['day']);
                }

                $professionals = Professional::
                whereHas('status', function (Builder $query) {
                    $query->where('openSchedule', 1);
                })->whereDoesntHave('reserve.reserve_day', function($query) use($days) {
                    $query->whereIn('day', $days);
                })->get();

                return response()->json($professionals, 200);
            }

        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ReserveController:findByCustomer', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }

    }
}
