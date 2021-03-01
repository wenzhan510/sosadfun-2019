<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use DB;
use Carbon;
use Log;

class SaveSessionData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $session_data, $user_id, $created_at;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($session_data, $user_id, $created_at)
    {
        $this->session_data = $session_data;
        $this->user_id = $user_id;
        $this->created_at = $created_at;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['single', 'user-session:'.$this->user_id];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_id = $this->user_id;
        $session_data = $this->session_data;
        $created_at = $this->created_at;


        $record = \App\Models\HistoricalUserSession::where('user_id',$user_id)->where('created_at', '>', $created_at->subDay(1))->first();
        if(!$record){
            $new_record = \App\Models\HistoricalUserSession::create([
                'user_id' => $user_id,
                'session_count' => $session_data['session_count'],
                'ip_count' => $session_data['ip_count'],
                'ip_band_count' => $session_data['ip_band_count'],
                'device_count' => $session_data['device_count'],
                'mobile_count' => $session_data['mobile_count'],
                'session_data' => json_encode($session_data['data']),
            ]);
        }
        if($record&&($record->mobile_count<$session_data['mobile_count']||$record->session_count<$session_data['session_count']||$record->ip_band_count<$session_data['ip_band_count'])){
            $record->update([
                'session_count' => $session_data['session_count'],
                'ip_count' => $session_data['ip_count'],
                'ip_band_count' => $session_data['ip_band_count'],
                'device_count' => $session_data['device_count'],
                'mobile_count' => $session_data['mobile_count'],
                'session_data' => json_encode($session_data['data']),
                'created_at' => $created_at->toDateTimeString(),
            ]);
        }
    }
}
