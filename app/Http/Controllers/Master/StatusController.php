<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $status = Status::get();

            return $this->success_json("Successfully get status", $status);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get status", $th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|min:3|unique:ms_marital_status'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to create status", $validator->errors(), 400);
        }

        try {
            $newStatus = Status::create([
                'name' => $request->name
            ]);

            if ($newStatus) {
                return $this->success_json('Successfully create new status', $newStatus);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to create status", $th->getMessage(), 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Status $status)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $find = Status::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Status not Found! ", $find, 404);
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => 'required|min:3'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Faield to update status", $validator->errors(), 400);
        }

        try {
            $updatedStatus = $find->update([
                'name' => $request->name
            ]);

            if ($updatedStatus) {
                return $this->success_json('Successfully update status', $updatedStatus);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Faield to update status", $th->getMessage(), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $find = Status::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Status not Found! ", $find, 404);
        }

        try {
            $delete = $find->delete();

            if ($delete) {
                return $this->success_json('Successfully delete status', $delete);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to delete status ", $th->getMessage(), 500);
        }
    }
}
