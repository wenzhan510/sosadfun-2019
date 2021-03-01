<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon;
use CacheUser;
use App\Models\Reward;

class StoreReward extends FormRequest
{
    protected $reward_types = array('salt', 'fish', 'ham');
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'rewardable_type' => 'required|string|max:20',
            'rewardable_id' => 'required|numeric',
            'reward_type' => 'required|string|max:10',
            'reward_value' => 'required|numeric|between:1,100',
        ];
    }

    public function validateDateAndAttribite($rewardable_type, $rewardable_id, $reward_data){
        return
        $rewards = Reward::on('mysql::write')
        ->where('rewardable_type', $rewardable_type)
        ->where('rewardable_id', $rewardable_id)
        ->where('user_id', $reward_data['user_id'])
        ->where('created_at','>',Carbon::parse($reward_data['created_at'])->subDay(1)->toDateTimeString())
        ->get()
        ->isEmpty();
    }

    public function validateBalance($reward_data,$info){
        return $info->{$reward_data['reward_type']} >= $reward_data['reward_value'];
    }

    public function generateReward($rewarded_model){
        if(!in_array($this->reward_type, $this->reward_types)){abort(422,'不存在这种虚拟物');}

        $reward_data = $this->only('reward_type','reward_value');
        $user = auth('api')->user();
        $info = $user->info;
        $reward_data['user_id'] = $user->id;
        $reward_data['created_at'] = Carbon::now()->toDateTimeString();
        $reward_data['receiver_id'] = $rewarded_model->user_id;

        if(!$this->validateDateAndAttribite($this->rewardable_type, $this->rewardable_id, $reward_data)){
            abort(410,'今天已经打赏过该奖励'); //今天已经打赏过该奖励
        }

        if(!$this->validateBalance($reward_data,$info)){
            abort(412, '奖励余额不充足'); //奖励余额是否充足
        }

        // 打赏和被打赏用户之间转账
        $this->info_transaction($info, $rewarded_model->author->info, $reward_data['reward_type'], (int)$reward_data['reward_value']);

        // 被打赏的item，进行分值改善
        $this->model_update($rewarded_model,$reward_data['reward_type'], (int)$reward_data['reward_value']);

        $reward = $rewarded_model->rewards()->create($reward_data);
        if($rewarded_model->user){
            $rewarded_model->user->remind('new_reward');
        }

        return $reward;
    }

    public function info_transaction($infoA, $infoB, $type, $value)
    {
        $half_value = $value - (int)($value / 2);
        $infoB->type_value_change($type, $half_value);
        $infoA->type_value_change($type, -$value);
        // TODO record value change and reason
    }

    public function model_update($model, $type, $value)
    {
        $model->type_value_change($type, $value);
    }
}
