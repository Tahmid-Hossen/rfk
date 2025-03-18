<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controller;

class PermissionController extends Controller
{

    function __construct(){
        // Add middleware for route protection
        $this->middleware('auth');
        $this->middleware('can:view permissions')->only(['index', 'show']);
    }
    /**
     * Display a listing of roles with permissions.
     */
    public function index(Request $request)
    {
        $permissions = Permission::get()->pluck('name');
        return ResponseHelper::success('All Permissions', $permissions);

    }

}
