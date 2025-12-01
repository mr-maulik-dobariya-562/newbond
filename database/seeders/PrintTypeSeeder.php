<?php

namespace Database\Seeders;

use App\Models\PartyType;
use App\Models\PrintType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrintTypeSeeder extends Seeder
{

    private $permissions = [
        'W/P',
        'O/F',
        'O/I',
        'T/I',
        'L/P',
        'E/P',
    ];

    private $partys = [
        'Dealer',
        'Retailer',
        'Retailer Bulk',
        'Karigar',
        'Corporate',
        'Export INR',
        'Export USD',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->permissions as $permission) {
            PrintType::create(['name' => $permission, 'short_name' => $permission, 'created_by' => 1]);
        }

        foreach ($this->partys as $type) {
            PartyType::create(['name' => $type, 'item_discount' => '0', 'created_by' => 1]);
        }
    }
}
