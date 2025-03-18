<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    private $roleService;
    function __construct(){

        // Add middleware for route protection
        $this->middleware('auth');
        $this->middleware('can:view users')->only(['index', 'show']);
        $this->middleware('can:create users')->only(['store']);
        $this->middleware('can:edit users')->only(['update']);
        $this->middleware('can:delete users')->only(['destroy']);
        $this->roleService = new RoleService();
    }


    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        // Fetch all roles with their associated permissions
        $roles = Role::with('permissions')->get();

        // Return the formatted roles in the response
        return ResponseHelper::success('Roles are fetched.', $this->roleService->formattedData($roles));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed!', $validator->errors());
        }

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        return ResponseHelper::success('Role created successfully', $this->roleService->formattedSingleData($role));
    }

    /**
     * Display the specified role.
     */
    public function show($id)
    {
        $role = Role::with('permissions')->find($id);
        if(!$role) return ResponseHelper::error('Role not found!', []);
        return ResponseHelper::success('Role data', $this->roleService->formattedSingleData($role));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if(!$role) return ResponseHelper::error('Role not found!', []);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name,' . $id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation faild!', $validator->errors());
        }

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }
        return ResponseHelper::success('Role updated successfully', $this->roleService->formattedSingleData($role));
    }

    /**
     * Remove the specified role.
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if(!$role) return ResponseHelper::error('Role not found!', []);
        $roleCopyData=clone $role;
        $role->delete();
        return ResponseHelper::success('Role deleted successfully', $this->roleService->formattedSingleData($roleCopyData));
    }
}
