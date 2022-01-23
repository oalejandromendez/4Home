<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\NoveltyRequest;
use App\Models\Admin\Novelty;
use App\Models\Scheduling\Reserve;
use App\Models\Scheduling\ReserveDay;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\Admin\NoveltyResource;

class NoveltiesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('permission:ACCEDER_NOVEDADES');
        $this->middleware('permission:VER_NOVEDADES', ['only' => ['index']]);
        $this->middleware('permission:CREAR_NOVEDADES', ['only' => ['store']]);
        $this->middleware('permission:MODIFICAR_NOVEDADES', ['only' => ['update']]);
        $this->middleware('permission:ELIMINAR_NOVEDADES', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return new NoveltyResource(Novelty::with('professional')->get());
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'NoveltiesController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(NoveltyRequest $request)
    {

        DB::beginTransaction();
        try {
            $novelty = new Novelty();
            $novelty->fill($request->all());
            $novelty->save();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'NoveltiesController:store', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(NoveltyRequest $request, $id)
    {

        $novelty = Novelty::find($id);

        if (!$novelty instanceof Novelty) {
            return response()->json(['message' => 'La novedad no se encuentra en la base de datos'], 404);
        }

        try {
            DB::beginTransaction();
            $novelty->fill($request->all());
            $novelty->update();
            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'NoveltiesController:update', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            return response()->json(['message' => 'El id de la novedad debe ser un campo numerico'], 400);
        }

        $novedad = Novelty::find($id);

        if (!$novedad instanceof Novelty) {
            return response()->json(['message' => 'La novedad no se encuentra en la base de datos'], 404);
        }

        DB::beginTransaction();
        try {
            $novedad->delete();

            DB::commit();
            return response()->json(200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(sprintf('%s:%s', 'NoveltiesController:destroy', $e->getMessage()));
            return response()->json(['message' => 'Error'], 500);
        }
    }

    public function scheduleAffected(Request $request)
    {
        try {

            $idProfessionals = [];
            if (!empty($request->get('professionals'))) {
                $idProfessionals = array_column($request->get('professionals'), 'id');
            }

            $sporadic = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day')
                ->whereHas('reserve_day', function (Builder $query) use ($request) {
                    $query->whereBetween('date', [$request->get('init') . " 00:00:00", $request->get('end') . " 23:59:59"]);
                });

            $status = $request->get('status');
            $sporadic->where('type', 1);
            if (!empty($status)) {
                $sporadic->where('status', $status);
            } else {
                $sporadic->where('status', '!=', 1)
                    ->where('status', '!=', 9);
            }

            if (count($idProfessionals) > 0) {
                $sporadic = $sporadic->whereIn('reserve.professional', $idProfessionals);
            }


            $sporadic = $sporadic->get();

            $monthly = Reserve::with('user.customer_address', 'customer_address', 'service.working_day', 'reserve_day');

            $monthly->whereRaw('((initial_service_date <= \'' . $request->get('init') . '\' AND ADDDATE(initial_service_date, INTERVAL 1 month) >= \'' . $request->get('init') . '\')');
            $monthly->orWhereRaw('(initial_service_date <= \'' . $request->get('end') . '\' AND ADDDATE(initial_service_date, INTERVAL 1 month) >= \'' . $request->get('end') . '\'))');

            if (count($idProfessionals) > 0) {
                $monthly = $monthly->whereIn('reserve.professional', $idProfessionals);
                $status = $request->get('status');
                $monthly->where('reserve.type', 2);
                if (!empty($status)) {
                    $monthly->where('reserve.status', $status);
                } else {
                    $monthly->where('reserve.status', '!=', 1)
                        ->where('reserve.status', '!=', 9);
                }
            }
            DB::enableQueryLog();
            $monthly = $monthly->get();
            $query = DB::getQueryLog();

            $schedule = [
                'sporadic' => $sporadic,
                'monthly' => $monthly,
                'query' => $query,
            ];

            return response()->json($schedule, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'NoveltiesController:scheduleAffected', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
