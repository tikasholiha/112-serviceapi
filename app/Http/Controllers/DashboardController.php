<?php

namespace App\Http\Controllers;

use App\Models\Call;
use App\Models\Emergency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function call_reports(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'month_period' => 'required',
                'year' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->error_json("Failed to create Emergency data", $validator->errors(), 400);
            }

            $totalByMonth = [
                'disconnect_call' => 0,
                'prank_call' => 0,
                'education_call' => 0,
                'emergency_call' => 0,
                'abandoned' => 0,
            ];

            $statByMonth = Call::with('detail')
                ->where([
                    ['month_period', '=', $request->month_period],
                    ['year', '=', $request->year],
                ])
                ->first();

            if ($statByMonth) {
                foreach ($statByMonth->detail as $data) {
                    $totalByMonth['disconnect_call'] += $data->disconnect_call;
                    $totalByMonth['prank_call'] += $data->prank_call;
                    $totalByMonth['education_call'] += $data->education_call;
                    $totalByMonth['emergency_call'] += $data->emergency_call;
                    $totalByMonth['abandoned'] += $data->abandoned;
                }
            }

            $statByYear = Call::with('detail')
                ->where('year', $request->year)
                ->orderByRaw("FIELD(month_period, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')")
                ->get();

            $totalByYear = [
                'disconnect_call' => 0,
                'prank_call' => 0,
                'education_call' => 0,
                'emergency_call' => 0,
                'abandoned' => 0,
            ];

            foreach ($statByYear as $month) {
                if ($month->detail) {
                    foreach ($month->detail as $detail) {
                        $totalByYear['disconnect_call'] += $detail->disconnect_call;
                        $totalByYear['prank_call'] += $detail->prank_call;
                        $totalByYear['education_call'] += $detail->education_call;
                        $totalByYear['emergency_call'] += $detail->emergency_call;
                        $totalByYear['abandoned'] += $detail->abandoned;
                    }
                }
            }

            $grafik_month = collect([]);
            $bar_grafik_month = collect([]);
            $grafik_year = collect([]);
            $bar_grafik_year = collect([]);

            if ($statByMonth) {
                $months = [
                    "January" => "01",
                    "February" => "02",
                    "March" => "03",
                    "April" => "04",
                    "May" => "05",
                    "June" => "06",
                    "July" => "07",
                    "August" => "08",
                    "September" => "09",
                    "October" => "10",
                    "November" => "11",
                    "December" => "12",
                ];

                $monthNumber = $months[$request->month_period];
                foreach ($statByMonth->detail as $detail) {
                    $date = sprintf('2024-%s-%s', $monthNumber, str_pad($detail->day, 2, '0', STR_PAD_LEFT));
                    $total = $detail->disconnect_call + $detail->prank_call + $detail->education_call + $detail->emergency_call + $detail->abandoned;

                    $grafik_month->push([
                        'x' => $date,
                        'y' => $total
                    ]);
                }
            }

            foreach ($totalByMonth as $key => $value) {
                $name = explode('_', $key);

                if (count($name) > 1) {
                    $bar_grafik_month->push([
                        'x' => ucfirst($name[0]) . " " . ucfirst($name[1]),
                        'y' => $value
                    ]);
                } else {
                    $bar_grafik_month->push([
                        'x' => ucfirst($name[0]),
                        'y' => $value
                    ]);
                }
            }

            // return $statByYear;
            foreach ($statByYear as $month) {
                foreach ($month->detail as $detail) {
                    $date = $month->month_period . " " . $detail->day;
                    $total = $detail->disconnect_call + $detail->prank_call + $detail->education_call + $detail->emergency_call + $detail->abandoned;

                    $grafik_year->push([
                        'x' => $date,
                        'y' => $total
                    ]);
                }
            }

            foreach ($totalByYear as $key => $value) {
                $name = explode('_', $key);

                if (count($name) > 1) {
                    $bar_grafik_year->push([
                        'x' => ucfirst($name[0]) . " " . ucfirst($name[1]),
                        'y' => $value
                    ]);
                } else {
                    $bar_grafik_year->push([
                        'x' => ucfirst($name[0]),
                        'y' => $value
                    ]);
                }
            }

            // Group by year and month_period, and calculate totals
            $formattedResult = $statByYear->map(function ($call) {
                $totals = $call->detail->reduce(function ($carry, $detail) {
                    return [
                        'total_disconnect_call' => $carry['total_disconnect_call'] + $detail->disconnect_call,
                        'total_prank_call' => $carry['total_prank_call'] + $detail->prank_call,
                        'total_education_call' => $carry['total_education_call'] + $detail->education_call,
                        'total_emergency_call' => $carry['total_emergency_call'] + $detail->emergency_call,
                        'total_abandoned' => $carry['total_abandoned'] + $detail->abandoned,
                    ];
                }, [
                    'total_disconnect_call' => 0,
                    'total_prank_call' => 0,
                    'total_education_call' => 0,
                    'total_emergency_call' => 0,
                    'total_abandoned' => 0,
                ]);

                return [
                    'year' => $call->year,
                    'month_period' => $call->month_period,
                    'total_disconnect_call' => $totals['total_disconnect_call'],
                    'total_prank_call' => $totals['total_prank_call'],
                    'total_education_call' => $totals['total_education_call'],
                    'total_emergency_call' => $totals['total_emergency_call'],
                    'total_abandoned' => $totals['total_abandoned'],
                ];
            });

            $result = collect([
                'by_month' => $totalByMonth,
                'by_year' => $totalByYear,
                'grafik_month' => $grafik_month,
                'bar_grafik_month' => $bar_grafik_month,
                'grafik_year' => $grafik_year,
                'bar_grafik_year' => $bar_grafik_year,
                'total' => $formattedResult
            ]);

            return $this->success_json('Successfully get dashboard', $result);
        } catch (\Throwable $th) {
            return $this->error_json("Report not Found!", $th->getMessage(), 400);
        }
    }

    public function emergency_reports(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'month_period' => 'required',
                'year' => 'required',
                'district_id' => 'required',
            ]);

            if ($validator->fails()) {
                return $this->error_json("Failed to create Emergency data", $validator->errors(), 400);
            }

            $statByMonth = Emergency::select(
                'kecelakaan',
                'kebakaran',
                'ambulan_gratis',
                'pln',
                'mobil_jenazah',
                'penanganan_hewan',
                'keamanan',
                'kriminal',
                'bencana_alam',
                'kdrt',
                'gelandangan_tanpa_identitas',
                'pipa_pdam_bocor',
                'odgj',
                'percobaan_bunuh_diri',
                'oli_tumpah',
                'kabel_menjuntai',
                'mobil_derek',
                'tiang_rubuh',
                'terkunci_dirumah',
                'reklame_rubuh',
                'orang_tenggelam',
            )
                ->whereMonth('period_date', '>=', $request->from)
                ->whereMonth('period_date', '<=', $request->to)
                ->where([
                    ['year', '=', $request->year],
                    ['district_id', '=', $request->district_id],
                ])->get();

            $total_by_month = [
                'kecelakaan' => 0,
                'kebakaran' => 0,
                'ambulan_gratis' => 0,
                'pln' => 0,
                'mobil_jenazah' => 0,
                'penanganan_hewan' => 0,
                'keamanan' => 0,
                'kriminal' => 0,
                'bencana_alam' => 0,
                'kdrt' => 0,
                'gelandangan_tanpa_identitas' => 0,
                'pipa_pdam_bocor' => 0,
                'odgj' => 0,
                'percobaan_bunuh_diri' => 0,
                'oli_tumpah' => 0,
                'kabel_menjuntai' => 0,
                'mobil_derek' => 0,
                'tiang_rubuh' => 0,
                'terkunci_dirumah' => 0,
                'reklame_rubuh' => 0,
                'orang_tenggelam' => 0,
            ];

            foreach ($statByMonth as $item) {
                foreach ($total_by_month as $key => $value) {
                    $total_by_month[$key] += $item[$key];
                }
            }

            $statByYear = Emergency::with('district')
                ->where(
                    'year',
                    $request->year
                )
                ->orderByRaw("FIELD(period, 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December')")
                ->get();

            // Mengelompokkan data berdasarkan district
            $groupedEmergencies = $statByYear->groupBy('district.name');

            // Menyiapkan variabel data chart
            $data_chart = [
                'series' => collect([]),
                'label' => collect([]),
            ];

            // Menghitung total untuk setiap district
            foreach ($groupedEmergencies as $district => $records) {
                $total = $records->reduce(function ($carry, $item) {
                    return $carry + $item->kecelakaan
                        + $item->kebakaran
                        + $item->ambulan_gratis
                        + $item->pln
                        + $item->mobil_jenazah
                        + $item->penanganan_hewan
                        + $item->keamanan
                        + $item->kriminal
                        + $item->bencana_alam
                        + $item->kdrt
                        + $item->gelandangan_tanpa_identitas
                        + $item->pipa_pdam_bocor
                        + $item->odgj
                        + $item->percobaan_bunuh_diri
                        + $item->oli_tumpah
                        + $item->kabel_menjuntai
                        + $item->mobil_derek
                        + $item->tiang_rubuh
                        + $item->terkunci_dirumah
                        + $item->reklame_rubuh
                        + $item->orang_tenggelam;
                }, 0);

                // Menambahkan hasil perhitungan ke variabel chart
                $data_chart['series']->push($total);
                $data_chart['label']->push($district);
            }

            $response = collect([
                'by_month' => $total_by_month,
                'by_year'  => $data_chart
            ]);

            return $this->success_json('Successfully get dashboard', $response);
        } catch (\Throwable $th) {
            return $this->error_json("Report not Found!", $th->getMessage(), 400);
        }
    }
}
