<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $district = District::get();

            return $this->success_json("Successfully get district", $district);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get district", $th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|min:3|unique:ms_districts'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to create district", $validator->errors(), 400);
        }

        try {
            $newDistrict = District::create([
                'name' => $request->name
            ]);

            if ($newDistrict) {
                return $this->success_json('Successfully create new district', $newDistrict);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to create district", $th->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(District $district)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $find = District::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("District not Found! ", $find, 404);
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to update district", $validator->errors(), 400);
        }

        try {
            $updatedDistrict = $find->update([
                'name' => $request->name
            ]);

            if ($updatedDistrict) {
                return $this->success_json('Successfully update district', $updatedDistrict);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to update district", $th->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $find = District::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("District not Found! ", $find, 404);
        }

        try {
            $delete = $find->delete();

            if ($delete) {
                return $this->success_json('Successfully delete district', $delete);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to delete district ", $th->getMessage(), 500);
        }
    }
}
