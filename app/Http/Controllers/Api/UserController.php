<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api','role:super-admin']);
    }

    // List all admin users
    public function index() {
        $users = User::role('admin')->get();
        return response()->json($users);
    }

    
public function store(Request $request)
{
    try {
        $currentUser = JWTAuth::parseToken()->authenticate();
    } catch (JWTException $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Invalid or expired token'
        ], 401);
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
        'role' => 'required|string',
        'status' => 'required|in:Active,Inactive,Suspended',
    ]);

    $data = $request->only(['name','email','password','status']);
    $data['password'] = Hash::make($data['password']);

    $user = User::create($data);
    $user->assignRole($request->role);

    return response()->json([
        'status'=>'success',
        'user'=>$user
    ],201);
}


    // Show single user
    public function show($id)
    {
        $user = User::role('admin')->findOrFail($id);
        return response()->json($user);
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = User::role('admin')->findOrFail($id);

        // Validation in controller
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'required|in:Active,Inactive,Suspended',
            'phone' => 'nullable|string|max:30',
        ]);

        $data = $request->only(['name','email','status','phone']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json([
            'status'=>'success',
            'user'=>$user
        ]);
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::role('admin')->findOrFail($id);
        $user->delete();

        return response()->json([
            'status'=>'success',
            'message'=>'User deleted'
        ]);
    }

    // Change status only
    public function changeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Active,Inactive,Suspended'
        ]);

        $user = User::role('admin')->findOrFail($id);
        $user->status = $request->status;
        $user->save();

        return response()->json([
            'status'=>'success',
            'message'=>'User status updated',
            'user'=>$user
        ]);
    }
}
