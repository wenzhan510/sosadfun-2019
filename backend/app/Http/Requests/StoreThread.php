<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Thread;
use Carbon;
use DB;
use StringProcess;
use App\Sosadfun\Traits\GenerateThreadDataTraits;


class StoreThread extends FormRequest
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
            'channel_id' => 'numeric',
            'title' => 'required|string|max:30',
            'brief' => 'required|string|max:50',
            'body' => 'required|string|min:10|max:20000',
            'majia' => 'nullable|string|max:10',
            'tags' => 'nullable',
        ];
    }
    public function generateThread($channel)
    {
        $thread_data = $this->generateThreadData($channel);

        $tongren_data = $this->generateTongrenData($channel);

        $thread = Thread::create($thread_data);

        if($this->tags){$thread->tags()->syncWithoutDetaching($thread->tags_validate((array)$this->tags));}

        if($tongren_data){$thread->tongren_data_sync($form->all());}

        return $thread;
    }

    public function updateThread(Thread $thread)
    {
        $thread_data = $this->generateUpdateThreadData($thread);

        $tongren_data = $this->generateTongrenData($thread->channel());

        $thread->update($thread_data);

        if($thread->channel()->type!='book'){
            $thread->drop_none_tongren_tags();
            if($this->tags){$thread->tags()->syncWithoutDetaching($thread->tags_validate((array)$this->tags));}
        }

        return $thread;
    }
}
