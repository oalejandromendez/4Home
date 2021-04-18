<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Log;

class PermissionController extends Controller
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
    public function index()
    {
        try {
            return Permission::all();
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'PermissionController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
