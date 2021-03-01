<?php
namespace App\Sosadfun\Traits;

use Cache;
use Carbon;

trait DonationObjectTraits{
    public function RecentDonations($page)
    {
        return Cache::remember('recent_donations-P'.$page, 20, function () use($page){
            return \App\Models\DonationRecord::with('author')
            ->where('donated_at','>',Carbon::now()->subMonth(1))
            ->orderBy('donation_amount','desc')
            ->orderBy('donated_at','desc')
            ->paginate(config('preference.records_per_part'))
            ->appends(['page'=>$page]);
        });
    }

    public function AllDonations($page)
    {
        return Cache::remember('all_donations-P'.$page, 20, function () use($page){
            return \App\Models\DonationRecord::with('author')
            ->orderBy('donation_amount','desc')
            ->orderBy('donated_at','desc')
            ->paginate(config('preference.records_per_page'))
            ->appends(['page'=>$page]);
        });
    }

    public function processDonationRecord($line)
    {
        $array = explode(',',$line);
        if(empty($array)||count($array)<>3){
            return ['warning' => 'warning', 'data' => '监测到输入格式错误:'.$line];
        }
        $email = trim($array[0]);
        $amount = trim($array[1]);
        $time = trim($array[2]);
        //
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            return ['warning' => 'warning', 'data' => '监测到输入格式错误（邮箱错误）:'.$line];
        }
        if(!preg_match('/^\$[0-9]+/', $amount)){
            return ['warning' => 'warning', 'data' => '监测到输入格式错误（金额错误）:'.$line];
        }
        $amount = (int)preg_replace('/\$/','',$amount);
        try {
            $real_time = Carbon::parse($time);
        } catch (\Exception $e) {
            return ['warning' => 'warning', 'data' => '监测到输入格式错误（日期错误）:'.$line];
        }
        $data=[];
        $data['donation_email']=$email;
        $data['donation_amount']=$amount;
        $data['donated_at'] = $real_time;
        $data['donation_kind'] = 'patreon';
        $data['is_claimed'] = false;
        return ['success'=>'success', 'data' => $data];

    }
    public function storeDonationRecord($data)
    {
        $record = \App\Models\DonationRecord::where('donation_kind', 'patreon')
        ->where('donation_email', $data['donation_email'])
        ->orderBy('donation_amount','desc')
        ->first();
        if($record){
            if($record->donation_amount < $data['donation_amount']){
                $record->update([
                    'donation_amount' => $data['donation_amount'],
                    'donated_at' =>  $data['donated_at'],
                    'is_claimed' => false,
                ]);
            }
        }else{
            $record = \App\Models\DonationRecord::create($data);
        }
        return $record;
    }
}
