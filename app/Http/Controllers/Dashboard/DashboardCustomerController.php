<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Scheduling\Reserve;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardCustomerController extends Controller
{
    public function index($id)
    {
        try {

            $reserve = Reserve::with('reserve_day', 'service.working_day')->where('user', $id)->get();

            $pending = Reserve::where('status', 2)->where('user', $id)->get();
            $pending = $pending->count();

            $schedule = Reserve::where('status', 1)->where('user', $id)->get();
            $schedule = $schedule->count();

            $expiration = Reserve::where('status', 3)->where('user', $id)->get();
            $expiration = $expiration->count();

            $total = Reserve::where('status', 10)->where('user', $id)->get();
            $total = $total->count();


            $widgets = [
                'pending' => $pending,
                'schedule' => $schedule,
                'expiration' => $expiration,
                'total'  => $total,
            ];

            $dashboard = [
                'widgets' => $widgets,
                'schedule' => $reserve
            ];

            return response()->json($dashboard, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'DashboardCustomerController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
