<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->integer('user_id')->primary();
            $table->boolean('has_intro')->default(false);//是否有额外的个人介绍
            $table->string('brief_intro', 50)->nullable();//短个人介绍

            $table->unsignedInteger('salt')->default(0);//盐粒数目
            $table->unsignedInteger('fish')->default(0);//咸鱼数目
            $table->unsignedInteger('ham')->default(0);//火腿数目
            $table->unsignedInteger('exp')->default(0);//经验值=盐度
            $table->unsignedInteger('upvote_count')->default(0);//被赞次数
            $table->unsignedInteger('view_count')->default(0);//被赞次数
            $table->unsignedInteger('follower_count')->default(0);//
            $table->unsignedInteger('following_count')->default(0);//

            $table->dateTime('no_posting_until')->nullable();//禁言至
            $table->dateTime('no_logging_until')->nullable();// 禁止登陆至
            $table->dateTime('no_homework_until')->nullable();//最后一次登陆时间

            $table->string('activation_token', 50)->nullable()->index();//激活token
            $table->string('invitation_token', 50)->nullable()->index();//注册邀请码
            $table->unsignedInteger('invitor_id')->default(0)->index();//邀请人ID
            $table->unsignedInteger('token_limit')->default(0);//邀请码限额
            $table->unsignedInteger('invitee_count')->default(0);//邀请人额度
            $table->unsignedInteger('donation_level')->default(0);//捐赠额度
            $table->unsignedInteger('no_ads_reward_limit')->default(0);//免广告福利额度
            $table->unsignedInteger('qiandao_reward_limit')->default(0);//补签福利额度

            $table->unsignedInteger('qiandao_continued')->default(0);//连续签到次数
            $table->unsignedInteger('qiandao_all')->default(0);//最高连续签到次数
            $table->unsignedInteger('qiandao_max')->default(0);//最高连续签到次数
            $table->unsignedInteger('qiandao_last')->default(0);//最高连续签到次数
            $table->dateTime('qiandao_at')->nullable()->index();//最后一次签到时间

            $table->unsignedInteger('message_limit')->default(0);//可以给陌生人发送的私信限额
            $table->unsignedInteger('list_limit')->default(0);//可以给陌生人发送的私信限额
            $table->boolean('no_stranger_msg')->default(false);//是否拒绝接受陌生人的私信
            $table->boolean('no_reward_reminders')->default(false);//是否不再接受关于被打赏的提醒
            $table->boolean('no_upvote_reminders')->default(false);//是否不再接受关于被点赞的提醒
            $table->boolean('no_message_reminders')->default(false);//是否不再接受私信提醒
            $table->boolean('no_reply_reminders')->default(false);//是否不再接受关于被回复的提醒

            $table->unsignedBigInteger('total_clicks')->default(0);//全部点击次数
            $table->unsignedInteger('daily_clicks')->default(0);//今日点击次数

            $table->unsignedInteger('unread_reminders')->default(0);//全部未读提醒
            $table->unsignedInteger('unread_updates')->default(0);//全部未读收藏更新
            $table->unsignedInteger('message_reminders')->default(0);//私信提醒
            $table->unsignedInteger('reply_reminders')->default(0);//回复提醒
            $table->unsignedInteger('upvote_reminders')->default(0);//赞提醒
            $table->unsignedInteger('reward_reminders')->default(0);//打赏提醒
            $table->unsignedInteger('administration_reminders')->default(0);//管理提醒
            $table->unsignedInteger('default_collection_updates')->default(0);//管理提醒

            $table->unsignedInteger('default_list_id')->default(0);//默认清单id
            $table->unsignedInteger('default_box_id')->default(0);//默认问题箱id
            $table->unsignedInteger('default_collection_group_id')->default(0);//默认收藏页id

            $table->string('creation_ip', 45)->index();//创建ip
            $table->dateTime('email_verified_at')->nullable()->index();//邮箱验证时间

            // $table->unsignedInteger('total_book_characters')->default(0);//全部发文字数
            // $table->unsignedInteger('total_comment_characters')->default(0);//全部评论字数

            $table->unsignedInteger('collection_total_count')->default(0);//收藏总数
            $table->unsignedInteger('collection_recommendation_count')->default(0);//编推收藏总数
            $table->unsignedInteger('collection_bianyuan_count')->default(0);//边缘收藏总数
            $table->unsignedInteger('collection_none_bianyuan_count')->default(0);//非边缘收藏总数

            $table->boolean('is_forbidden')->default(false);//是否恶意注册封禁账户

            $table->string('lang', 5)->nullable();//语言偏好
            $table->string('majia', 10)->nullable();//最近使用过的马甲
            $table->boolean('use_indentation')->default(true);//最近使用过的段首缩进设置
            $table->unsignedInteger('public_notice_id')->default(0);//已读最新系统消息

            $table->string('quiz_questions')->nullable();//上一次quiz的题目

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_infos');
    }
}
