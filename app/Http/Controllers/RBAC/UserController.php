<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::with(['employee', 'roles'])->get();
            return $this->success_json('Successfully get users', $users);
        } catch (\Throwable $th) {
            //throw $th;
            return $this->error_json("Failed to get users", $th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username'              => 'required|min:3|unique:ms_users',
            'password'              => 'required',
            'employee_id'           => 'required|numeric'
        ]);

        if (!$validator->fails()) {
            try {
                $created = User::create([
                    'username'              => $request->username,
                    'password'              => $request->password,
                    'employee_id'           => $request->employee_id
                ]);

                return $this->success_json("Successfully created new users", $created);
            } catch (\Throwable $th) {
                return $this->error_json("Failed to create users", $th->getMessage(), 500);
            }
        } else {
            return $this->error_json("Failed to create users", $validator->errors(), 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $findUser = User::where('id', $id)->first();

        if (!$findUser) {
            return $this->error_json("Failed to update, User not found", null, 404);
        }

        $validator = Validator::make($request->all(), [
            'username'              => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to create users", $validator->errors(), 400);
        }

        try {
            if ($request->password) {
                $updated = $findUser->update([
                    'username'              => $request->username,
                    'password'              => $request->password,
                ]);
            } else {
                $updated = $findUser->update([
                    'username'              => $request->username,
                ]);
            }

            return $this->success_json("Successfully update users", $updated);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to update users", $th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $find = User::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Failed to delete, User not found.", null, 404);
        }

        try {
            //code...
            $deleted  = $find->delete();

            return $this->success_json('Successfully delete user', $deleted);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to delete User", $th->getMessage(), 500);
        }
    }

    /**
     * assign user roles
     * need role_id and user_id
     */
    public function assign_roles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to assign user roles", $validator->errors(), 400);
        }

        $collectionRoles = collect([]);

        foreach ($request->role_ids as $role_id) {
            $collectionRoles->push([
                'user_id' => $request->user_id,
                'role_id' => $role_id,
            ]);
        }

        try {
            UserRole::where('user_id', $request->user_id)->delete();

            $create = UserRole::insert($collectionRoles->toArray());

            if ($create) {
                return $this->success_json("Successfully assign roles", $create);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to assign roles", $th->getMessage(), 500);
        }
    }

    /**
     * show user login with roles, menus and permissions
     */
    public function me(Request $request)
    {
        $user_login = $request->user();

        try {
            $find = User::with(['employee', 'roles.role.menus.menu.permissions'])->where('id', $user_login->id)->first();

            return $this->success_json("Successfully get user login", $find);
        } catch (\Throwable $th) {
            $this->error_json("Failed to get user login", $th->getMessage(), 500);
        }
    }
}
