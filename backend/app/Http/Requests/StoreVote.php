<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Vote;



class StoreVote extends FormRequest
{
    protected $attitudes = array('upvote','downvote','funnyvote','foldvote');
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
            'votable_type' => 'required|string|max:20',
            'votable_id' => 'required|numeric',
            'attitude_type' => 'required|string|max:10',
        ];
    }

    public function validateAttitude($votable_type, $votable_id, $attitude_type, $user_id){
        $votes = Vote::on('mysql::write')
        ->where('votable_type', $votable_type)
        ->where('votable_id', $votable_id)
        ->where('user_id',$user_id)
        ->get();
        $check_attitude=in_array($attitude_type, array('upvote','downvote')) ? array('upvote','downvote'):array($attitude_type);
        return $votes->whereIn('attitude_type', $check_attitude)->isEmpty();
    }

    public function generateVote($voted_model){

        if(!in_array($this->attitude_type, $this->attitudes)){abort(422);}

        $vote_data = $this->only('attitude_type');
        $vote_data['user_id'] = auth()->id();
        $vote_data['receiver_id'] = $voted_model->user_id;

        if(!$this->validateAttitude($this->votable_type, $this->votable_id, $vote_data['attitude_type'], $vote_data['user_id'])){
            abort(411,'和已有投票冲突（如重复投票，也可能是已经赞还要踩）'); //和已有投票冲突（可能是重复投票，也可能是已经赞还要踩）
        }

        // 被评票的item，统计评票数量
        $this->model_update($voted_model,$vote_data['attitude_type'].'_count',1);

        $vote = $voted_model->votes()->create($vote_data);

        if($vote_data['attitude_type']==='upvote'&&$voted_model->user){
            $voted_model->user->remind('new_upvote');
        }

        return $vote;

    }

    public function model_update($model, $type, $value)
    {
        $model->type_value_change($type, $value);
    }

}
