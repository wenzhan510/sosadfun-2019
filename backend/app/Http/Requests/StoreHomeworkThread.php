<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon;
use DB;
use StringProcess;
use Auth;
use App\Models\Homework;
use App\Sosadfun\Traits\GenerateThreadDataTraits;


class StoreHomeworkThread extends FormRequest
{
    use GenerateThreadDataTraits;

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
            'title' => 'required|string|max:15',
            'brief' => 'required|string|max:30',
            'body' => 'nullable|string|max:20000',
            'majia' => 'nullable|string|max:10',
        ];
    }

    public function generateHomeworkThread($homework, $homework_registration)
    {
        $channel = collect(config('channel'))->keyby('id')->get(config('constants.homework_channel_id'));
        $thread_data = $this->generateThreadData($channel);
        $order_id = $homework->assign_order_id();
        $thread_data['title'] = $homework->title.'-'.$this->title.'-'.$order_id;
        $thread_data['is_public'] = true;
        $thread_data['no_reply'] = false;

        $thread = \App\Models\Thread::create($thread_data);

        $homework_registration->update([
            'title' => $this->title,
            'order_id' => $order_id,
            'thread_id' => $thread->id,
            'submitted_at' => Carbon::now(),
        ]);

        return $thread;
    }
}
