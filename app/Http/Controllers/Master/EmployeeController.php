<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $employees = Employee::with(['marital_status', 'religion'])->get();

            return $this->success_json('Successfully get employees', $employees);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get employees", $th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'religion_id'       => 'required|numeric',
            'marital_status_id' => 'required|numeric',
            'name'              => 'required|min:3',
            'education'         => 'required|min:3',
            'jasnita_number'    => 'required',
            'employment_status'    => 'required',
            'gender'            => 'required',
            'dob'               => 'required|date',
            'address'           => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to create employees", $validator->errors(), 400);
        }

        if ($request->hasFile('avatar')) {
            $filename = Str::random(32) . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();

            $avatarPath = $request->file('avatar')->storeAs('uploads/avatars', $filename, 'public');

            $avatarPath = $avatarPath;
        } else {
            $avatarPath = 'default.png';
        }

        try {
            $created = Employee::create([
                'marital_status_id' => (int)$request->marital_status_id,
                'religion_id'       => (int)$request->religion_id,
                'name'              => $request->name,
                'education'         => $request->education,
                'jasnita_number'    => $request->jasnita_number,
                'employment_status'    => $request->employment_status,
                'gender'            => $request->gender,
                'dob'               => $request->dob,
                'address'           => $request->address,
                'avatar'            => $avatarPath,
            ]);

            return $this->success_json("Successfully created new employees", $created);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to create employees", $th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $findEmployee = Employee::with(['marital_status', 'religion'])->where('id', $id)->first();

            if ($findEmployee) {
                return $this->success_json("Successfully find employee", $findEmployee);
            } else {
                return $this->error_json("Employee not found.", null, 404);
            }
        } catch (\Throwable $th) {
            $this->error_json("Failed to find employee", $th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $findEmployee = Employee::with(['marital_status', 'religion'])->where('id', $id)->first();

            if (!$findEmployee) {
                return $this->error_json("Employee not found.", null, 404);
            }
            $validator = Validator::make($request->all(), [
                'religion_id'       => 'required|numeric',
                'marital_status_id' => 'required|numeric',
                'name'              => 'required|min:3',
                'jasnita_number'    => 'required',
                'employment_status'    => 'required',
                'gender'            => 'required',
                'dob'               => 'required|date',
                'address'           => 'required',
                'avatar'            => 'image'
            ]);

            if ($validator->fails()) {
                return $this->error_json("Failed to update employees", $validator->errors(), 400);
            }

            if ($request->hasFile('avatar')) {
                if ($findEmployee->avatar) {
                    if (Storage::exists('public/' . $findEmployee->avatar)) {
                        Storage::delete('public/' . $findEmployee->avatar);
                    }
                }

                // Generate a unique filename with a more readable format
                $filename = Str::random(32) . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();

                // Store the file in the public/avatars directory
                $avatarPath = $request->file('avatar')->storeAs('uploads/avatars', $filename, 'public');

                // Access the file path using asset() to ensure correct URL generation
                $avatarPath = $avatarPath;
            } else {
                $avatarPath = null;
            }

            try {
                if ($request->hasFile('avatar')) {
                    $created = $findEmployee->update([
                        'marital_status_id' => (int)$request->marital_status_id,
                        'religion_id'       => (int)$request->religion_id,
                        'name'              => $request->name,
                        'jasnita_number'    => $request->jasnita_number,
                        'employment_status'    => $request->employment_status,
                        'gender'            => $request->gender,
                        'dob'               => $request->dob,
                        'address'           => $request->address,
                        'avatar'            => $avatarPath
                    ]);
                } else {
                    $created = $findEmployee->update([
                        'marital_status_id' => (int)$request->marital_status_id,
                        'religion_id'       => (int)$request->religion_id,
                        'name'              => $request->name,
                        'jasnita_number'    => $request->jasnita_number,
                        'employment_status'    => $request->employment_status,
                        'gender'            => $request->gender,
                        'dob'               => $request->dob,
                        'address'           => $request->address,
                    ]);
                }

                return $this->success_json("Successfully updated employee", $created);
            } catch (\Throwable $th) {
                return $this->error_json("Failed to update employees", $th->getMessage(), 500);
            }
        } catch (\Throwable $th) {
            $this->error_json("Failed to find employee", $th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $find = Employee::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Employee not Found! ", $find, 404);
        }

        try {
            if (Storage::exists('public/' . $find->avatar)) {
                Storage::delete('public/' . $find->avatar);
            }

            $delete = $find->delete();

            if ($delete) {
                return $this->success_json('Successfully delete employee', $delete);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to delete employee", $th->getMessage(), 500);
        }
    }
}
