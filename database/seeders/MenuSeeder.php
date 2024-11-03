<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set Parent
        DB::table('ms_menus')->insert([
            [
                'name'  => 'Dashboard',
                'icon'  => 'Category',
                'url'   => '#',
                'ord'   => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'Call Reports',
                'icon'  => 'Headphones',
                'url'   => '/call-reports',
                'ord'   => 2,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'Test Calls',
                'icon'  => 'CallReceived',
                'url'   => '/test-calls',
                'ord'   => 3,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'Emergency Reports',
                'icon'  => 'Brodcast',
                'url'   => '/emergency-reports',
                'ord'   => 4,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'Master Data',
                'icon'  => 'Layer',
                'url'   => '#',
                'ord'   => 5,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'Settings',
                'icon'  => 'Setting2',
                'url'   => '#',
                'ord'   => 6,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);

        // Set Children
        DB::table('ms_menus')->insert([
            // Children Dashboard ID 1
            [
                'name'  => 'Calls',
                'icon'  => 'Chart21',
                'url'   => '/dashboard/calls',
                'ord'   => 1,
                'parent_id' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'Emergency',
                'icon'  => 'Chart2',
                'url'   => '/dashboard/emergency',
                'ord'   => 2,
                'parent_id' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],

            // Children Master Data ID 4
            [
                'name'  => 'Employees',
                'icon'  => 'UserOctagon',
                'url'   => '/emergency-reports',
                'ord'   => 1,
                'parent_id' => 5,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'Religions',
                'icon'  => 'Arrow',
                'url'   => '/master-data/religions',
                'ord'   => 2,
                'parent_id'   => 5,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'Status',
                'icon'  => 'Book1',
                'url'   => '/master-data/status',
                'ord'   => 3,
                'parent_id'   => 5,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'District',
                'icon'  => 'Buildings',
                'url'   => '/master-data/districts',
                'ord'   => 4,
                'parent_id'   => 5,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],

            // Children Setting ID 5
            [
                'name'  => 'Menu',
                'icon'  => 'Category2',
                'url'   => '/settings/menus',
                'ord'   => 1,
                'parent_id'   => 6,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'Role',
                'icon'  => 'Lock1',
                'url'   => '/settings/roles',
                'ord'   => 2,
                'parent_id'   => 6,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'Users',
                'icon'  => 'People',
                'url'   => '/settings/users',
                'ord'   => 3,
                'parent_id'   => 6,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);

        // Set Sub Children
        DB::table('ms_menus')->insert([
            [
                'name'  => 'List',
                'icon'  => 'UserTag',
                'url'   => '/master-data/employees',
                'ord'   => 1,
                'parent_id' => 9,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name'  => 'KPI',
                'icon'  => 'Activity',
                'url'   => '/master-data/employees/kpi',
                'ord'   => 2,
                'parent_id' => 9,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ],
        ]);
    }
}
