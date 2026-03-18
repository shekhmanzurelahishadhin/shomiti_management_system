<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;

class MembersSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['name'=>'মোঃ রাকিব',            'phone'=>'01711111111','father_name'=>'মোঃ আলী','gender'=>'male','marital_status'=>'married',  'monthly_deposit'=>1000, 'share_count'=>1,'join_date'=>'2025-12-01','present_district'=>'ঢাকা','present_upazila'=>'তেজগাঁও'],
            ['name'=>'মোঃ জিয়াউর রহমান',    'phone'=>'01722222222','father_name'=>'মোঃ করিম','gender'=>'male','marital_status'=>'married', 'monthly_deposit'=>1000, 'share_count'=>1,'join_date'=>'2025-12-01','present_district'=>'ঢাকা','present_upazila'=>'তেজগাঁও'],
            ['name'=>'মামুন হোসেন',           'phone'=>'01733333333','father_name'=>'মোঃ হোসেন','gender'=>'male','marital_status'=>'married','monthly_deposit'=>1000, 'share_count'=>2,'join_date'=>'2025-12-01','present_district'=>'ঢাকা','present_upazila'=>'তেজগাঁও'],
            ['name'=>'মোঃ মোজাম্মেল হক',     'phone'=>'01744444444','father_name'=>'মোঃ হক','gender'=>'male','marital_status'=>'married',   'monthly_deposit'=>1000, 'share_count'=>1,'join_date'=>'2025-12-01','present_district'=>'ঢাকা','present_upazila'=>'তেজগাঁও'],
            ['name'=>'মোঃ রাকিবুল হাসান রাষি','phone'=>'01755555555','father_name'=>'মোঃ হাসান','gender'=>'male','marital_status'=>'married','monthly_deposit'=>1000,'share_count'=>1,'join_date'=>'2025-12-01','present_district'=>'ঢাকা','present_upazila'=>'তেজগাঁও'],
        ];

        foreach ($members as $data) {
            Member::firstOrCreate(
                ['phone' => $data['phone']],
                array_merge($data, [
                    'member_id'  => Member::generateMemberId(),
                    'entry_fee'  => 100,
                    'status'     => 'active',
                ])
            );
        }
    }
}
