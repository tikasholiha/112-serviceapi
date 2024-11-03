<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleMenu;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = Role::with(['menus.menu', 'permissions.permission'])->get();

            return $this->success_json("Successfully get roles", $roles);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get roles", $th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|unique:ms_roles',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to create roles", $validator->errors(), 400);
        }

        try {
            $newRoles = Role::create([
                'name' => $request->name,
                'description' => $request->description,
                'created_by' => $request->user()->id
            ]);

            if ($newRoles) {
                return $this->success_json('Successfully create new role', $newRoles);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to create roles", $th->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $roles)
    {
        return $this->success_json('Successfully create new role', $roles);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $find = Role::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Roles not Found! ", $find, 404);
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|min:3',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to update roles", $validator->errors(), 400);
        }

        try {
            $updatedRoles = $find->update([
                'name' => $request->name,
                'description' => $request->description,
                'updated_by' => $request->user()->id
            ]);

            if ($updatedRoles) {
                return $this->success_json('Successfully update role', $updatedRoles);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to update roles", $th->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $find = Role::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Roles not Found! ", $find, 404);
        }

        try {
            $delete = $find->delete();

            if ($delete) {
                return $this->success_json('Successfully delete role', $delete);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to delete role ", $th->getMessage(), 500);
        }
    }

    /**
     * assign role menus
     * need role_id and menu_id
     */
    public function assign_menus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to assign menus", $validator->errors(), 400);
        }

        $collectionMenu = collect([]);

        foreach ($request->menu_ids as $menu_id) {
            $collectionMenu->push([
                'role_id' => $request->role_id,
                'menu_id' => $menu_id,
            ]);
        }

        try {
            RoleMenu::where('role_id', $request->role_id)->delete();

            $create = RoleMenu::insert($collectionMenu->toArray());

            if ($create) {
                return $this->success_json("Successfully assign menus", $create);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to assign menus", $th->getMessage(), 500);
        }
    }

    /**
     * assign role permission
     * need role_id and permission_id
     */
    public function assign_permissions(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'role_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to assign permissions", $validator->errors(), 400);
        }

        $collectionPermissions = collect([]);

        foreach ($request->permission_ids as $permission_id) {
            $collectionPermissions->push([
                'role_id' => $request->role_id,
                'permission_id' => $permission_id,
            ]);
        }

        // return $collectionPermissions;
        try {
            RolePermission::where('role_id', $request->role_id)->delete();

            $create = RolePermission::insert($collectionPermissions->toArray());

            if ($create) {
                return $this->success_json("Successfully assign permissions", $create);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to assign permissions", $th->getMessage(), 500);
        }
    }
}
