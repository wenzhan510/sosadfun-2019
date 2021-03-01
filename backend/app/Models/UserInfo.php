<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon;
use ConstantObjects;

class UserInfo extends Model
{
    use Traits\TypeValueChangeTrait;
    use Traits\DelayCountTrait;

    protected $guarded = [];
    protected $connection= 'mysql::write';
    protected $primaryKey = 'user_id';
    const UPDATED_AT = null;
    protected $dates = ['no_posting_until', 'no_logging_until', 'login_at',  'email_verified_at'];
    protected $count_types = array('salt', 'fish', 'ham', 'upvote_count', 'follower_count', 'following_count');
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rewardData($salt=0, $fish=0, $ham=0)
    {
        $this->salt+=$salt;
        $this->fish+=$fish;
        $this->ham+=$ham;
    }

    public function retractData($salt=0, $fish=0, $ham=0)
    {
        $this->salt-=$salt;
        $this->fish-=$fish;
        $this->ham-=$ham;
    }

    public function reward($kind, $base = 0){
        switch ($kind):
            case "regular_status"://普通状态奖励（不再奖励）
            $this->rewardData(0,0,0);
            break;
            case "regular_post"://普通回帖奖励
            $this->rewardData(0,1,0);
            break;
            case "long_post":// 长评
            $this->rewardData(5,3,1);
            break;
            case "first_post"://抢到新章节首杀
            $this->rewardData(4,2,0);
            break;
            case "regular_thread"://普通主题奖励（不再奖励）
            $this->rewardData(0,0,0);
            break;
            case "regular_book"://普通书本奖励（不再奖励）
            $this->rewardData(0,0,0);
            break;
            case "short_chapter"://短小章节奖励
            $this->rewardData(5,1,0);
            break;
            case "standard_chapter"://标准章节奖励
            $this->rewardData(10,1,1);
            break;
            case "upvoted_by_many":
            $this->rewardData(5,1,1);
            break;
            case "book_downloaded_as_thread":
            $this->rewardData(5,1,0);
            break;
            case "book_downloaded_as_book":
            $this->rewardData(10,2,0);
            break;
            case "homework_excellent_worker":
            $this->rewardData(50,20,$base*20);
            break;
            case "homework_excellent_critic":
            $this->rewardData(25,10,$base*10);
            break;
            case "homework_regular_worker":
            $this->rewardData(25,10,$base*10);
            break;
            case "homework_regular_critic":
            $this->rewardData(10,5,$base*5);
            break;
            case "first_quiz":// 首次答题奖励
            $this->rewardData(5*$base,1*$base,0);
            break;
            case "more_quiz":// 重复答题奖励(不再奖励)
            $this->rewardData(0,0,0);
            break;
            default:
            echo "应该奖励什么呢？一个bug呀……";
        endswitch;
        $this->save();
    }

    public function retract($kind, $base = 0){
        switch ($kind):
            case "delete_status"://删除动态惩罚
            $this->retractData(1,0,0);
            break;
            case "delete_post"://删除回帖惩罚 ok
            $this->retractData(0,1,0);
            break;
            case "reduce_long_to_short"://将长内容修改为短内容
            $this->retractData(0,0,1);
            break;
            case "convert_chapter_to_post"://将章节转为回帖的惩罚 ok
            $this->retractData(0,0,1);
            break;
            case "convert_work_to_post"://将章节转为回帖的惩罚 ok
            $this->retractData(0,0,1);
            break;
            case "delete_book"://删除书籍惩罚
            $this->retractData(10,5,2);
            break;
            case "delete_thread"://删除主题惩罚
            $this->retractData(5,2,0);
            break;
            default:
            echo "应该奖励什么呢？一个bug呀……";
        endswitch;
        $this->save();
    }

    public function activate(){
        $user = $this->user;
        $user->activated = true;
        $this->activation_token = null;
        $this->email_verified_at = Carbon::now();
        $user->save();
        $this->save();
    }

    public function clear_column($column_name='')
    {
        switch ($column_name) {
            case 'unread_reminders':
            if($this->unread_reminders>0){
                $this->update(['unread_reminders'=>0]);
            }
            return true;
            break;

            case 'unread_updates':
            if($this->unread_updates>0){
                $this->update(['unread_updates'=>0]);
            }
            return true;
            break;

            case 'public_notice_id':
            if($this->public_notice_id<ConstantObjects::system_variable()->latest_public_notice_id){
                $this->update(['public_notice_id'=>ConstantObjects::system_variable()->latest_public_notice_id]);
            }
            return true;
            break;

            case 'default_collection_updates':
                if($this->default_collection_updates>0){
                    $this->update(['default_collection_updates'=>0]);
                }
            return true;
            case 'message_reminders':
                if($this->message_reminders>0){
                    $this->update(['message_reminders'=>0]);
                }
            return true;
            break;

            case 'upvote_reminders':
                if($this->upvote_reminders>0){
                    $this->update(['upvote_reminders'=>0]);
                }
            return true;
            break;

            case 'reply_reminders':
                if($this->reply_reminders>0){
                    $this->update(['reply_reminders'=>0]);
                }
            return true;
            break;

            case 'reward_reminders':
                if($this->reward_reminders>0){
                    $this->update(['reward_reminders'=>0]);
                }
            return true;
            break;

            case 'administration_reminders':
                if($this->administration_reminders>0){
                    $this->update(['administration_reminders'=>0]);
                }
            return true;
            break;

            default:
            return false;
        }
    }



}
