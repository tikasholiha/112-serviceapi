<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $roles = Permission::with(['menu'])->get();

            return $this->success_json("Successfully get permissions", $roles);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get permissions", $th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|min:3',
            'menu_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to create permissions", $validator->errors(), 400);
        }

        try {
            $newPermission = Permission::create([
                'name' => $request->name,
                'menu_id' => $request->menu_id,
                'created_by' => $request->user()->id
            ]);

            if ($newPermission) {
                return $this->success_json('Successfully create new permission', $newPermission);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to create permissions", $th->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $roles)
    {
        return $this->success_json('Successfully create new permission', $roles);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $find = Permission::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Permission not Found! ", $find, 404);
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'name'    => 'required|min:3',
            'menu_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to update permissions", $validator->errors(), 400);
        }

        try {
            $updatedRoles = $find->update([
                'name' => $request->name,
                'menu_id' => $request->menu_id,
                'updated_by' => $request->user()->id
            ]);

            if ($updatedRoles) {
                return $this->success_json('Successfully update permission', $updatedRoles);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to update permissions", $th->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $find = Permission::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Permission not Found! ", $find, 404);
        }

        try {
            $delete = $find->delete();

            if ($delete) {
                return $this->success_json('Successfully delete permission', $delete);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to delete role ", $th->getMessage(), 500);
        }
    }
}
