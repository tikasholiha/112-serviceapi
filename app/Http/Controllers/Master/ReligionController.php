<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Religion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReligionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $religion = Religion::get();

            return $this->success_json("Successfully get religion", $religion);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get religion", $th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|min:3|unique:ms_religions'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to create religion", $validator->errors(), 400);
        }

        try {
            $newReligion = Religion::create([
                'name' => $request->name
            ]);

            if ($newReligion) {
                return $this->success_json('Successfully create new religion', $newReligion);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to create religion", $th->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Religion $religion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $find = Religion::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Religion not Found! ", $find, 404);
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to update religion", $validator->errors(), 400);
        }

        try {
            $updatedReligion = $find->update([
                'name' => $request->name
            ]);

            if ($updatedReligion) {
                return $this->success_json('Successfully update religion', $updatedReligion);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to update religion", $th->getMessage(), 422);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $find = Religion::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Religion not Found! ", $find, 404);
        }

        try {
            $delete = $find->delete();

            if ($delete) {
                return $this->success_json('Successfully delete religion', $delete);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to delete religion ", $th->getMessage(), 500);
        }
    }
}
