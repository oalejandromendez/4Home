<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentTypeController extends Controller
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
            return response()->json(DocumentType::all(), 200);
        } catch (\Exception $e) {
            Log::error(sprintf('%s:%s', 'DocumentTypeController:index', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
