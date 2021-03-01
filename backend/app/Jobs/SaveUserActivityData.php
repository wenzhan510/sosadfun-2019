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

class SaveUserActivityData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $new_data;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($new_data)
    {
        $this->new_data = $new_data;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['batch', 'user-activity:batch'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $new_data = $this->new_data;

        $inserts = collect($new_data)->unique('user_id')->map(function ($user_activity) {
            return $user_activity;
        })->toArray();

        DB::table('today_user_activities')->insert($inserts);

        DB::table('podcast')->insert(['data'=> count($inserts), 'type' => 'inserted_today_user_activities', 'created_at' => Carbon::now()]);

        Log::info('succesfully saved batch user activity data');
    }
}
