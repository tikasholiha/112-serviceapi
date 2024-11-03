<?php

namespace App\Http\Controllers;

use App\Models\TestCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestCallController extends Controller
{
    // GET: /api/test-calls - Retrieve all test calls
    public function index(Request $request)
    {
        try {
            $query = TestCall::query();

            // Check for query parameters and apply conditions accordingly
            $query->when($request->has('from'), function ($q) use ($request) {
                return $q->where('call_date', '>=', $request->query('from'));
            });

            $query->when($request->has('to'), function ($q) use ($request) {
                return $q->where('call_date', '<=', $request->query('to'));
            });

            $testCalls = $query->orderBy('call_date', 'ASC')->get();

            return $this->success_json("Successfully get test call", $testCalls);
        } catch (\Exception $e) {
            return $this->error_json("Failed to get test call", $e->getMessage(), 500);
        }
    }

    // POST: /api/test-calls - Store a new test call
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'call_date' => 'required|date',
            'location' => 'required|string|max:255',
            'latitude' => 'required|string|max:255',
            'longitude' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'duration' => 'required|integer|min:0',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return
                $this->error_json("Failed to get test call", $validator->errors(), 400);
        }

        try {
            $testCall = TestCall::create($request->all());

            return $this->success_json("Successfully created", $testCall);
        } catch (\Exception $e) {
            return $this->error_json("Failed to create test call.", $e->getMessage(), 500);
        }
    }

    // GET: /api/test-calls/{id} - Retrieve a specific test call
    public function show($id)
    {
        try {
            $testCall = TestCall::findOrFail($id);
            return $this->success_json("Successfully find test call", $testCall);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return
                $this->error_json("Test call not found.", $e->getMessage(), 404);
        } catch (\Exception $e) {
            return
                $this->error_json("Failed to retrieve test call.", $e->getMessage(), 500);
        }
    }

    // PUT/PATCH: /api/test-calls/{id} - Update a specific test call
    public function update(Request $request, $id)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'call_date' => 'required|date',
            'location' => 'required|string|max:255',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'phone_number' => 'required|string|max:15',
            'duration' => 'required|integer|min:0',
            'status' => 'required|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to update test call", $validator->errors(), 400);
        }

        try {
            $testCall = TestCall::findOrFail($id);
            $testCall->update($request->all());

            return $this->success_json("Successfully update", $testCall);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return
                $this->error_json("Test call not found.", $e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error_json("Failed to update test call.", $e->getMessage(), 500);
        }
    }

    // DELETE: /api/test-calls/{id} - Delete a specific test call
    public function destroy($id)
    {
        try {
            $testCall = TestCall::findOrFail($id);
            $testCall->delete();

            return $this->success_json("Successfully deleted test call", $testCall);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error_json("Test call not found.", $e->getMessage(), 404);
        } catch (\Exception $e) {
            return $this->error_json("Failed to delete test call.", $e->getMessage(), 500);
        }
    }

    /**
     * export the specified resource from storage.
     */
    public function export_data()
    {
        try {
            $data = TestCall::orderBy('call_date', 'desc')
                ->get();

            return $this->success_json("Successfully export data", $data);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to export data", $th->getMessage(), 500);
        }
    }
}
