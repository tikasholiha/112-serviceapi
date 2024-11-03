<?php

namespace App\Http\Controllers\Calls;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\CallDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $calls =
                DB::table('ms_calls')
                ->join('tr_call_details', 'ms_calls.id', '=', 'tr_call_details.call_id')
                ->select(
                    'ms_calls.id',
                    'ms_calls.year',
                    'ms_calls.month_period',
                    DB::raw('SUM(tr_call_details.disconnect_call) as total_disconnect_call'),
                    DB::raw('SUM(tr_call_details.prank_call) as total_prank_call'),
                    DB::raw('SUM(tr_call_details.education_call) as total_education_call'),
                    DB::raw('SUM(tr_call_details.emergency_call) as total_emergency_call'),
                    DB::raw('SUM(tr_call_details.abandoned) as total_abandoned'),
                    'ms_calls.created_at',
                    'ms_calls.updated_at'
                )
                ->groupBy('ms_calls.id', 'ms_calls.year', 'ms_calls.month_period', 'ms_calls.created_at', 'ms_calls.updated_at')
                ->get();

            return $this->success_json("Successfully get calls", $calls);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get calls", $th->getMessage(), 500);
            //throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $findByYearPeriod = Call::where([
            ['year', '=', $request->year],
            ['month_period', '=', $request->month_period]
        ])->first();

        if ($findByYearPeriod) {
            return $this->error_json("Period is exists", $findByYearPeriod, 422);
        }

        $validator = Validator::make($request->all(), [
            'year' => 'required|numeric',
            'month_period' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to create call reports", $validator->errors(), 400);
        }

        if (!$request->detail) {
            return $this->error_json("Failed to create call reports", "Need Detail Data", 400);
        }
        $createCall = "";


        try {
            $createCall = Call::create([
                'year' => $request->year,
                'month_period' => $request->month_period
            ]);

            if ($createCall) {
                $collectionCallDetail = collect([]);

                foreach ($request->detail as $call_detail) {
                    $collectionCallDetail->push([
                        'call_id' => $createCall->id,
                        'day' => $call_detail['day'],
                        'disconnect_call' => $call_detail['disconnect_call'],
                        'prank_call' => $call_detail['prank_call'],
                        'education_call' => $call_detail['education_call'],
                        'emergency_call' => $call_detail['emergency_call'],
                        'abandoned' => $call_detail['abandoned'],
                    ]);
                }

                $createCallDetail = CallDetail::insert($collectionCallDetail->toArray());

                if ($createCallDetail) {
                    return $this->success_json("Successfully create call reports", [
                        "call" => $createCall,
                        "detail" => $createCallDetail
                    ]);
                }
            }
        } catch (\Throwable $th) {
            Call::where('id', $createCall->id)->delete();

            return $this->error_json("Failed to create call report", $th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = Call::with('detail')->where('id', $id)->first();

        if (!$data) {
            return $this->error_json("Call report not found!", null, 404);
        }

        return $this->success_json("Successfully get call report", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $findCallReport = Call::where('id', $id)->first();

        if (!$findCallReport) {
            return $this->error_json("Period not found", $findCallReport, 404);
        }

        $validator = Validator::make($request->all(), [
            'year' => 'required|numeric',
            'month_period' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to create call reports", $validator->errors(), 400);
        }

        try {
            $update = $findCallReport->update([
                'year' => $request->year,
                'month_period' => $request->month_period
            ]);


            if ($update) {
                CallDetail::where('call_id', $findCallReport->id)->delete();

                $collectionCallDetail = collect([]);

                foreach ($request->detail as $call_detail) {
                    $collectionCallDetail->push([
                        'call_id' => $findCallReport->id,
                        'day' => $call_detail['day'],
                        'disconnect_call' => $call_detail['disconnect_call'],
                        'prank_call' => $call_detail['prank_call'],
                        'education_call' => $call_detail['education_call'],
                        'emergency_call' => $call_detail['emergency_call'],
                        'abandoned' => $call_detail['abandoned'],
                    ]);
                }

                $reCreateCallDetail = CallDetail::insert($collectionCallDetail->toArray());

                if ($reCreateCallDetail) {
                    return $this->success_json("Successfully update call report", [
                        "call" => $findCallReport,
                        "detail" => $reCreateCallDetail
                    ]);
                }
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to update call reports", $th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $findCallReport = Call::where('id', $id)->first();

        if (!$findCallReport) {
            return $this->error_json("Period not found", $findCallReport, 404);
        }

        try {
            $deleteDetail = CallDetail::where('call_id', $id)->delete();

            if ($deleteDetail) {
                $deleteCall = $findCallReport->delete();

                if ($deleteCall) {
                    return $this->success_json("Successfully delete call report", $deleteCall);
                }
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to delete call report", $th->getMessage(), 500);
        }
    }

    /**
     * export the specified resource from storage.
     */
    public function export_data()
    {
        try {
            $data = Call::with('detail')
                ->orderBy('year')
                ->orderByRaw('FIELD(month_period, "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December")')
                ->get();

            return $this->success_json("Successfully export data", $data);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to export data", $th->getMessage(), 500);
        }
    }
}
