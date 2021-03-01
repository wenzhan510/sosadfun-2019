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

class UpdateModelValueChange implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model_type, $model_id, $attribute_name, $value;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct($model_type, $model_id, $attribute_name, $value)
    {
        $this->model_type = $model_type;
        $this->model_id = $model_id;
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
        return ['single', $this->model_type.':'.$this->model_id];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $model_type=$this->model_type;
        $model_id=$this->model_id;
        $attribute_name=$this->attribute_name;
        $value=$this->value;

        $allowed_models = [
            'App\Models\User',
            'App\Models\Thread',
            'App\Models\Post',
            'App\Models\Quiz',
            'App\Models\QuizOption',
            'App\Models\UserInfo',
            'App\Models\PostInfo',
        ];

        $allowed_attributes = [
            'view_count',
            'redirect_count',
            'quiz_count',
            'correct_count',
            'select_count',
            'daily_clicks',
        ];

        if(in_array($model_type, $allowed_models)&&in_array($attribute_name, $allowed_attributes)){
            $model = $model_type::find($model_id);
            if($model){
                $model->increment($attribute_name, $value);
            }
        }

    }
}
