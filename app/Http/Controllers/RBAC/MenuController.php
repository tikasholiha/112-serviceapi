<?php

namespace App\Http\Controllers\RBAC;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $menus = Menu::with(['childrens.childrens.childrens.childrens'])->where('parent_id', null)->get();

            return $this->success_json("Successfully get menus", $menus);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get menus", $th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'url' => 'required|min:3',
            'icon' => 'required|min:3',
            'ord' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to create menus", $validator->errors(), 400);
        }

        try {
            if (!$request->parent_id) {
                $newMenus = Menu::create([
                    'name' => $request->name,
                    'url' => $request->url,
                    'icon' => $request->icon,
                    'ord' => $request->ord,
                    'created_by' => $request->user()->id
                ]);
            } else {
                $newMenus = Menu::create([
                    'name' => $request->name,
                    'url' => $request->url,
                    'icon' => $request->icon,
                    'ord' => $request->ord,
                    'parent_id' => $request->parent_id,
                    'created_by' => $request->user()->id
                ]);
            }

            if ($newMenus) {
                return $this->success_json('Successfully create new menu', $newMenus);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to create menus", $th->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menus)
    {
        return $this->success_json('Successfully create new menu', $menus);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $find = Menu::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Menus not Found! ", $find, 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'url' => 'required|min:3',
            'icon' => 'required|min:3',
            'ord' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to update menus", $validator->errors(), 400);
        }

        try {
            if (!$request->parent_id) {
                $updatedMenu = $find->update([
                    'name' => $request->name,
                    'url' => $request->url,
                    'icon' => $request->icon,
                    'ord' => $request->ord,
                    'updated_by' => $request->user()->id
                ]);
            } else {
                $updatedMenu = $find->update([
                    'name' => $request->name,
                    'url' => $request->url,
                    'icon' => $request->icon,
                    'ord' => $request->ord,
                    'parent_id' => $request->parent_id,
                    'updated_by' => $request->user()->id
                ]);
            }

            if ($updatedMenu) {
                return $this->success_json('Successfully update menu', $updatedMenu);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to update menus", $th->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $find = Menu::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Menus not Found! ", $find, 404);
        }

        try {
            $delete = $find->delete();

            if ($delete) {
                return $this->success_json('Successfully delete menu', $delete);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to delete menu", $th->getMessage(), 500);
        }
    }
}
