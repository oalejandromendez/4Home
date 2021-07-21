<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        try {
            $activities = Activity::with('causer')
            ->whereBetween('created_at', [ $request->get('init')." 00:00:00", $request->get('end')." 23:59:59" ])
            ->get();
            return response()->json($activities, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'ActivityLogController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
