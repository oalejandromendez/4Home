<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin\ServiceType;
use App\Models\Admin\WorkingDay;
use App\Models\Scheduling\CustomerType;
use App\Models\Scheduling\Reserve;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardAdminController extends Controller
{
    public function index()
    {
        try {

            $pending = Reserve::where('status', 2)->get();
            $pending = $pending->count();

            $schedule = Reserve::where('status', 1)->get();
            $schedule = $schedule->count();

            $expiration = Reserve::where('status', 3)->get();
            $expiration = $expiration->count();

            $reschedule = Reserve::where('status', 9)->get();
            $reschedule = $reschedule->count();

            $services = Reserve::where('status', 10)->get();
            $services = $services->count();

            $customers = User::with('customer_address')->whereHas('roles', function (Builder $query) {
                $query->where('name', 'CLIENTE');
            })->where('id', '!=', Auth::id())
            ->where('status', 1)
            ->get();

            $customers = $customers->count();

            $widgets = [
                'pending' => $pending,
                'schedule' => $schedule,
                'expiration' => $expiration,
                'reschedule' => $reschedule,
                'services'  => $services,
                'customers' => $customers
            ];

            $sporadic =  Reserve::where('type', 1)->get();
            $sporadic = $sporadic->count();

            $monthly =  Reserve::where('type', 2)->get();
            $monthly = $monthly->count();

            $services = ServiceType::with(['working_day.service' => function($q) {
                $q->withCount('reserve');
            }])->get();

            $working = WorkingDay::with(['service' => function($q) {
                $q->withCount('reserve');
            }])->get();


            $customers = CustomerType::withCount('user')->get();

            $dashboard = [
                'widgets' => $widgets,
                'sporadic' => $sporadic,
                'monthly' => $monthly,
                'services' => $services,
                'working' => $working,
                'customers' => $customers
            ];



            return response()->json($dashboard, 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'DashboardAdminController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
