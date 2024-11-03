<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ms_employees')->insert([
            'marital_status_id' => 1,
            'religion_id'       => 1,
            'name'              => 'Tika Soliha',
            'education'         => 'Strata 1',
            'jasnita_number'    => 789111,
            'employment_status'    => 'Tenaga Ahli',
            'gender'            => 'Female',
            'dob'               => '2001-04-27',
            'address'           => 'Tangerang',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
