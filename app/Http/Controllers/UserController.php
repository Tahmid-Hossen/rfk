<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ResponseHelper;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    private $userService;

    public function __construct()
    {
        // Add middleware for route protection
        $this->middleware('auth');
        $this->middleware('can:view users')->only(['index', 'show']);
        $this->middleware('can:create users')->only(['store']);
        $this->middleware('can:edit users')->only(['update']);
        $this->middleware('can:delete users')->only(['destroy']);

        $this->userService = new UserService();
    }

    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $users = User::with(['roles.permissions'])->get();

        return ResponseHelper::success('Users retrieved successfully', $this->userService->getFormattedData($users));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name' // Ensure the role exists in the roles table
        ]);

        // If validation fails, return the first error message
        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed!', $validator->errors());
        }

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign the role to the user (ensure the role exists)
        $user->assignRole($request->role);

        // Return a success response with the newly created user
        return ResponseHelper::success('User created successfully', $this->userService->getFormattedSingleData($user));
    }


    /**
     * Display the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = User::find($id);
        if(!$user) return ResponseHelper::error('User not found!', []);

        return ResponseHelper::success('User details retrieved successfully', $this->userService->getFormattedSingleData($user));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if(!$user) return ResponseHelper::error('User not found!', []);

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|exists:roles,name' // Ensure the role exists in the roles table
        ]);

        if ($validator->fails()) {
            return ResponseHelper::error('Validation failed!',$validator->errors());
        }

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        // Check if the role field is provided and reassign the role
        if ($request->has('role')) {
            $user->syncRoles($request->role); // Sync the new role
        }

        $user->save();

        return ResponseHelper::success('User updated successfully', $this->userService->getFormattedSingleData($user));
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {

        $user = User::find($id);
        if(!$user) return ResponseHelper::error('User not found!', []);
        $copyData=$this->userService->getFormattedSingleData($user);
        $user->delete();

        return ResponseHelper::success('User deleted successfully', $copyData);
    }
}
