<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'somity_name'     => 'নবদিগন্ত সমবায় সমিতি',
            'somity_name_en'  => 'Nabadiganta Somobai Somiti',
            'somity_address'  => '৪১৭-৪১৮/এ, তেজগাঁও শিল্প এলাকা, ঢাকা-১২০৮',
            'somity_address_en'=> '417-418/A, Tejgaon I/A, Dhaka-1208',
            'somity_phone'    => '+8801722-784150, +8801636-466341',
            'somity_email'    => 'nabadigantaltd@gmail.com',
            'due_date_start'  => '5',
            'due_date_end'    => '15',
            'late_fee'        => '50',
            'entry_fee'       => '100',
            'share_value'     => '1000',
            'max_shares'      => '2',
            'max_members'     => '30',
            'suspend_after_months' => '3',
            'currency'        => '৳',
            'tagline'         => 'একসাথে দিগন্তে',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
