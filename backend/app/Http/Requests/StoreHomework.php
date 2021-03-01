<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon;
use DB;
use StringProcess;
use Auth;
use App\Models\Homework;

class StoreHomework extends FormRequest
{
    /**
    * Determine if the user is authorized to make this request.
    *
    * @return bool
    */
    public function authorize()
    {
        return true;
    }

    /**
    * Get the validation rules that apply to the request.
    *
    * @return array
    */
    public function rules()
    {
        return [
            'title' => 'nullable|string|max:25',
            'topic' => 'nullable|string|max:25',
            'homework_level' => 'required|numeric|min:0|max:5',
            'worker_registration_limit' => 'required|numeric|min:0|max:50',
            'critic_registration_limit' => 'required|numeric|min:0|max:50',
            'registration_start_at' => 'required|date',
            'submission_end_at' => 'required|date',
            'critique_end_at' => 'required|date',
            'registration_thread_id' => 'nullable|numeric',
            'profile_thread_id' => 'nullable|numeric',
            'summary_thread_id' => 'nullable|numeric',
        ];
    }

    public function generateHomeworkData(){
        $registration_start_at = Carbon::createFromFormat('Y-m-d\TH:i', $this->registration_start_at, 'Asia/Shanghai')->setTimezone('UTC');
        $submission_end_at = Carbon::createFromFormat('Y-m-d\TH:i', $this->submission_end_at, 'Asia/Shanghai')->setTimezone('UTC');
        $critique_end_at = Carbon::createFromFormat('Y-m-d\TH:i', $this->critique_end_at, 'Asia/Shanghai')->setTimezone('UTC');
        $homework_data = [
            'title' => $this->title,
            'topic' => $this->topic,
            'level' => $this->homework_level,
            'worker_registration_limit' => $this->worker_registration_limit,
            'critic_registration_limit' => $this->critic_registration_limit,
            'registration_start_at' => $registration_start_at->toDateTimeString(),
            'submission_end_at' => $submission_end_at->toDateTimeString(),
            'critique_end_at' => $critique_end_at->toDateTimeString(),
            'is_active' => $this->is_active ? true:false,
            'allow_watch' => $this->allow_watch ? true:false,
            'registration_on' => $this->registration_on ? true:false,
            'registration_thread_id' => $this->registration_thread_id ?? 0,
            'profile_thread_id' => $this->profile_thread_id ?? 0,
            'summary_thread_id' => $this->summary_thread_id ?? 0,
        ];
        return $homework_data;
    }

    public function generateHomeworkThreadData($homework){
        $thread_data = [
            'title' => $homework->title.'报名入口',
            'channel_id' => config('constants.commentary_channel_id'),
            'user_id' => auth('api')->id(),
            'brief' => '作业报名',
            'responded_at' => Carbon::now(),
            'creation_ip' => request()->ip(),
            'body'=>$this->requirement."\n作业活动详情：[url]".route('homework.show',$homework).'[/url]',
        ];
        return $thread_data;
    }

    public function storeHomeworkandThread()
    {
        $homework_data = $this->generateHomeworkData();
        $homework = Homework::create($homework_data);
        $thread_data = $this->generateHomeworkThreadData($homework);
        $thread = \App\Models\Thread::create($thread_data);
        $homework->update(['registration_thread_id'=>$thread->id]);
        return $thread;
    }

    public function updateHomework($homework)
    {
        $data = $this->generateHomeworkData();
        $homework->update($data);
        return $homework;
    }

}
