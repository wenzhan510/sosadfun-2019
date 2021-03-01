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

class BatchUpdateModelValueChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model_type, $model_ids, $attribute_name, $value;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($model_type, $model_ids, $attribute_name, $value)
    {
        $this->model_type = $model_type;
        $this->model_ids = $model_ids;
        $this->attribute_name = $attribute_name;
        $this->value = $value;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return ['batch',$this->model_type.':batch'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $model_type=$this->model_type;
        $model_ids=$this->model_ids;
        $attribute_name=$this->attribute_name;
        $value=$this->value;

        $allowed_models = [
            'App\Models\User' => 'users',
            'App\Models\Thread' => 'threads',
            'App\Models\Post' => 'posts',
            'App\Models\Quiz' => 'quizzes',
            'App\Models\UserInfo' => 'user_infos',
            'App\Models\PostInfo' => 'post_infos',
        ];

        $allowed_keys = [
            'App\Models\User' => 'id',
            'App\Models\Thread' => 'id',
            'App\Models\Post' => 'id',
            'App\Models\Quiz' => 'id',
            'App\Models\UserInfo' => 'user_id',
            'App\Models\PostInfo' => 'post_id',
        ];

        $allowed_attributes = [
            'view_count',
            'redirect_count',
            'quiz_count',
            'correct_count',
            'daily_clicks',
        ];

        if(array_key_exists($model_type, $allowed_models)&&in_array($attribute_name, $allowed_attributes)&&is_numeric($value)){
            DB::table($allowed_models[$model_type])->whereIn($allowed_keys[$model_type], $model_ids)->update([$attribute_name => DB::raw($attribute_name.' + '.$value)]);
        }
    }
}
