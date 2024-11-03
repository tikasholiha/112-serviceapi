<?php

namespace App\Http\Controllers\Emergency;

use App\Http\Controllers\Controller;
use App\Models\Emergency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmergencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = Emergency::groupBy('period', 'year')
                ->selectRaw('
                    period,
                    year,
                    SUM(kecelakaan) as kecelakaan,
                    SUM(kebakaran) as kebakaran,
                    SUM(ambulan_gratis) as ambulan_gratis,
                    SUM(pln) as pln,
                    SUM(mobil_jenazah) as mobil_jenazah,
                    SUM(penanganan_hewan) as penanganan_hewan,
                    SUM(keamanan) as keamanan,
                    SUM(kriminal) as kriminal,
                    SUM(bencana_alam) as bencana_alam,
                    SUM(kdrt) as kdrt,
                    SUM(gelandangan_tanpa_identitas) as gelandangan_tanpa_identitas,
                    SUM(pipa_pdam_bocor) as pipa_pdam_bocor,
                    SUM(odgj) as odgj,
                    SUM(percobaan_bunuh_diri) as percobaan_bunuh_diri,
                    SUM(oli_tumpah) as oli_tumpah,
                    SUM(kabel_menjuntai) as kabel_menjuntai,
                    SUM(mobil_derek) as mobil_derek,
                    SUM(tiang_rubuh) as tiang_rubuh,
                    SUM(terkunci_dirumah) as terkunci_dirumah,
                    SUM(reklame_rubuh) as reklame_rubuh,
                    SUM(orang_tenggelam) as orang_tenggelam
                ')
                ->get();

            return $this->success_json("Successfully get Emergency", $data);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get Emergency", $th->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'period'                => 'required',
            'period_date'           => 'required',
            'year'                  => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to create Emergency data", $validator->errors(), 400);
        }

        if (!$request->detail) {
            return $this->error_json("Failed to create emergency reports", "Need Detail Data", 400);
        }
        $collectionEmergencies = collect([]);

        foreach ($request->detail as $data) {
            $find = Emergency::where([
                ['period', '=', $request->period],
                ['year', '=', $request->year],
                ['district_id', '=', $data['district_id']],
            ])->first();

            if ($find) {
                return $this->error_json("Emergency Period is exist", $find, 422);
            }

            $collectionEmergencies->push([
                'period' => $request->period,
                'period_date' => $request->period_date,
                'year' => $request->year,
                'district_id' => $data['district_id'],
                'kecelakaan' => $data['kecelakaan'],
                'kebakaran' => $data['kebakaran'],
                'ambulan_gratis' => $data['ambulan_gratis'],
                'pln' => $data['pln'],
                'mobil_jenazah' => $data['mobil_jenazah'],
                'penanganan_hewan' => $data['penanganan_hewan'],
                'keamanan' => $data['keamanan'],
                'kriminal' => $data['kriminal'],
                'bencana_alam' => $data['bencana_alam'],
                'kdrt' => $data['kdrt'],
                'gelandangan_tanpa_identitas' => $data['gelandangan_tanpa_identitas'],
                'pipa_pdam_bocor' => $data['pipa_pdam_bocor'],
                'odgj' => $data['odgj'],
                'percobaan_bunuh_diri' => $data['percobaan_bunuh_diri'],
                'oli_tumpah' => $data['oli_tumpah'],
                'kabel_menjuntai' => $data['kabel_menjuntai'],
                'mobil_derek' => $data['mobil_derek'],
                'tiang_rubuh' => $data['tiang_rubuh'],
                'terkunci_dirumah' => $data['terkunci_dirumah'],
                'reklame_rubuh' => $data['reklame_rubuh'],
                'orang_tenggelam' => $data['orang_tenggelam'],
                'created_by' => Auth::user()->id
            ]);
        }

        try {
            $create = Emergency::insert($collectionEmergencies->toArray());

            if ($create) {
                return $this->success_json("Successfully create emergency data", $create);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to create emergency data", $th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $data = Emergency::with('district')
                ->where('id', $id)
                ->first();

            return $this->success_json("Successfully get Emergency", $data);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get Emergency", $th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show_by_period($month_period, $year)
    {
        try {
            $data = Emergency::with('district')
                ->where([
                    ['period', '=', $month_period],
                    ['year', '=', $year],
                ])
                ->get();

            return $this->success_json("Successfully get Emergency", $data);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to get Emergency", $th->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $find = Emergency::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Emergency not found!", $find, 404);
        }

        $validator = Validator::make($request->all(), [
            'period'                => 'required',
            'year'                  => 'required|numeric',
            'district_id'           => 'required|numeric',
            'kecelakaan'            => 'required|numeric',
            'kebakaran'             => 'required|numeric',
            'ambulan_gratis'        => 'required|numeric',
            'pln'                   => 'required|numeric',
            'mobil_jenazah'         => 'required|numeric',
            'penanganan_hewan'      => 'required|numeric',
            'keamanan'              => 'required|numeric',
            'kriminal'              => 'required|numeric',
            'bencana_alam'          => 'required|numeric',
            'kdrt'                  => 'required|numeric',
            'gelandangan_tanpa_identitas' => 'required|numeric',
            'pipa_pdam_bocor' => 'required|numeric',
            'odgj' => 'required|numeric',
            'percobaan_bunuh_diri' => 'required|numeric',
            'oli_tumpah' => 'required|numeric',
            'kabel_menjuntai' => 'required|numeric',
            'mobil_derek' => 'required|numeric',
            'tiang_rubuh' => 'required|numeric',
            'terkunci_dirumah' => 'required|numeric',
            'reklame_rubuh' => 'required|numeric',
            'orang_tenggelam' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->error_json("Failed to create Emergency data", $validator->errors(), 400);
        }

        try {
            $update = $find->update([
                'period'                      => $request->period,
                'year'                        => $request->year,
                'district_id'                 => $request->district_id,
                'kecelakaan'                  => $request->kecelakaan,
                'kebakaran'                   => $request->kebakaran,
                'ambulan_gratis'              => $request->ambulan_gratis,
                'pln'                         => $request->pln,
                'mobil_jenazah'               => $request->mobil_jenazah,
                'penanganan_hewan'            => $request->penanganan_hewan,
                'keamanan'                    => $request->keamanan,
                'kriminal'                    => $request->kriminal,
                'bencana_alam'                => $request->bencana_alam,
                'kdrt'                        => $request->kdrt,
                'gelandangan_tanpa_identitas' => $request->gelandangan_tanpa_identitas,
                'pipa_pdam_bocor'             => $request->pipa_pdam_bocor,
                'odgj'                        => $request->odgj,
                'percobaan_bunuh_diri'        => $request->percobaan_bunuh_diri,
                'oli_tumpah'                  => $request->oli_tumpah,
                'kabel_menjuntai'             => $request->kabel_menjuntai,
                'mobil_derek'                 => $request->mobil_derek,
                'tiang_rubuh'                 => $request->tiang_rubuh,
                'terkunci_dirumah'            => $request->terkunci_dirumah,
                'reklame_rubuh'               => $request->reklame_rubuh,
                'orang_tenggelam'             => $request->orang_tenggelam,
                'updated_by' => Auth::user()->id
            ]);

            if ($update) {
                return $this->success_json("Successfully update emergency data", $update);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to update emergency data", $th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $find = Emergency::where('id', $id)->first();

        if (!$find) {
            return $this->error_json("Emergency not found!", $find, 404);
        }

        try {
            $delete = $find->delete();

            if ($delete) {
                return $this->success_json("Successfully delete emergency", $delete);
            }
        } catch (\Throwable $th) {
            return $this->error_json("Failed to delete emergency", $th->getMessage(), 500);
        }
    }

    /**
     * export the specified resource from storage.
     */
    public function export_data()
    {
        try {
            $data = Emergency::with('district')
                ->orderBy('year')
                ->orderByRaw('FIELD(period, "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December")')
                ->get();

            return $this->success_json("Successfully export data", $data);
        } catch (\Throwable $th) {
            return $this->error_json("Failed to export data", $th->getMessage(), 500);
        }
    }
}
