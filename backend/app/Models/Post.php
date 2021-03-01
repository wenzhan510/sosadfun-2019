<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ConstantObjects;


class Post extends Model
{
    use SoftDeletes;
    use Traits\VoteTrait;
    use Traits\RewardTrait;
    use Traits\TypeValueChangeTrait;
    use Traits\DelayCountTrait;

    protected $guarded = [];
    protected $post_types = array('chapter', 'question', 'answer', 'post', 'comment', 'review', 'work', 'critique', 'volumn', 'essay', 'case'); // post的分类类别
    protected $count_types = ['upvote_count'];

    const UPDATED_AT = null;

    protected $hidden = [
        'creation_ip',
    ];

    protected $dates = ['deleted_at', 'edited_at', 'created_at', 'responded_at'];

    public function info()
    {
        return $this->hasOne(PostInfo::class, 'post_id');
    }

    public function simpleInfo()
    {
        return $this->hasOne(PostInfo::class, 'post_id')->select('post_id','order_by','previous_id','next_id','reviewee_id','reviewee_type','rating','redirect_count','author_attitude','previous_id','next_id','abstract', 'summary');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function reply_positions()
    {
        return $this->belongsTo(PostReplyPosition::class);
    }

    public function simpleThread()
    {
        return $this->belongsTo(Thread::class, 'thread_id')->select('id','channel_id','title','brief','is_anonymous');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id','name','title_id','level');
    }

    public function last_reply()
    {
        return $this->belongsTo(Post::class, 'last_reply_id')->select(['id','brief']);
    }

    public function replies()
    {
        return $this->hasMany(Post::class, 'reply_to_id');
    }

    public function answers()
    {
        return $this->hasMany(Post::class, 'reply_to_id')->where('type','=','answer');
    }

    public function question()
    {
        return $this->belongsTo(Post::class, 'reply_to_id')->where('type','=','question');
    }

    public function parent()
    {
        return $this->belongsTo(Post::class, 'reply_to_id');
    }

    public function tags()
    {
        return $this->belongsToMany('App\Models\Tag', 'tag_post', 'post_id', 'tag_id');
    }

    public function scopeBrief($query)
    {
        return $query->select('id', 'thread_id', 'user_id', 'title', 'type', 'brief', 'created_at',  'edited_at',  'is_anonymous', 'majia', 'is_bianyuan', 'upvote_count', 'char_count', 'reply_count', 'view_count');
    }

    public function scopeWithUser($query, $id) {
        return $query->where('posts.user_id', '=', $id);
    }

    public function scopeIsPublic($query, $isPublic='')//只看作者决定公开的
    {
        if($isPublic==='include_private'){
            return $query;
        }
        if($isPublic==='private_only'){
            $query->where('threads.is_public', false);
        }
        $query->where('threads.is_public', true);
    }

    public function scopeInPublicChannel($query, $inPublicChannel='')//只看公共channel内的
    {
        if($inPublicChannel==='include_none_public_channel'){
            return $query;
        }
        return $query->whereIn('threads.channel_id', ConstantObjects::public_channels());
    }

    public function scopeThreadOnly($query, $threadOnly)
    {
        if($threadOnly){
            return $query->where('posts.thread_id', $threadOnly);
        }
        return $query;
    }

    public function scopeUserOnly($query, $userOnly)
    {
        if($userOnly){
            return $query->where('posts.user_id', $userOnly)->where('posts.is_anonymous', false);
        }
        return $query;
    }

    public function scopeWithType($query, $withType=null)
    {
        if(in_array($withType, $this->post_types)){
            return $query->where('posts.type', $withType);
        }
        return $query;
    }

    public function scopeWithTypes($query, $withTypes=[])
    {
        if(!array_diff($withTypes, $this->post_types)){
            return $query->whereIn('posts.type', (array)$withTypes);
        }
        return $query;
    }

    public function scopeWithFolded($query, $withFolded='')
    {
        if($withFolded==='include_folded'){
            return $query;
        }
        if($withFolded==='folded_only'){
            return $query->where('posts.fold_state','>',0);
        }
        return $query->where('posts.fold_state','=',0);
    }

    public function scopeWithBianyuan($query, $withBianyuan='')
    {
        if($withBianyuan==='include_bianyuan'){
            return $query;
        }
        if($withBianyuan==='bianyuan_only'){
            return $query->where('posts.is_bianyuan', true);
        }
        return $query->where('posts.is_bianyuan', false);
    }

    public function scopeWithComment($query, $withComment)
    {
        if($withComment==='include_comment'){
            return $query;
        }
        return $query->where('is_comment',0);
    }

    public function scopeWithComponent($query, $withComponent)
    {
        if($withComponent==='component_only'){
            return $query->where('posts.type', '<>', 'post');
        }
        return $query;
    }


    public function scopeCaseType($query, $caseType)
    {
        if($caseType==='unsolved_only'){
            return $query->where('post_infos.summary', null);
        }
        if($caseType==='solved_only'){
            return $query->where('post_infos.summary', '<>', null);
        }
        if($caseType==='all'){
            return $query;
        }
        return $query;
    }

    public function scopeWithSummary($query, $withSummary)
    {
        if($withSummary){
            return $query->where('post_infos.summary', $withSummary);
        }
        return $query;
    }

    public function scopeWithReplyTo($query, $withReplyTo)
    {
        if($withReplyTo){
            return $query->where('posts.reply_to_id', $withReplyTo);
        }
        return $query;
    }

    public function scopeInComponent($query, $inComponent)
    {
        if($inComponent){
            return $query->where('posts.in_component_id', $inComponent);
        }
        return $query;
    }

    public function scopeOrdered($query, $ordered='')
    {
        switch ($ordered) {
            case 'latest_created'://最新
            return $query->orderBy('posts.created_at', 'desc');
            break;
            case 'most_replied'://按回应数量倒序
            return $query->orderBy('posts.reply_count', 'desc');
            break;
            case 'most_upvoted'://按赞数倒序
            return $query->orderBy('posts.upvote_count', 'desc');
            break;
            case 'random'://随机排序
            return $query->inRandomOrder();
            break;
            case 'latest_responded'://按最新被回应时间倒序
            return $query->orderBy('posts.responded_at', 'desc');
            break;
            default://默认按时间顺序排列，最早的在前面
            return $query->orderBy('posts.created_at', 'asc');
        }
    }

    public function scopeWithLength($query, $length)
    {
        if($length==='short'){ //短推
            return $query->where('posts.len',1);
        }
        if($length==='medium'){ //中推
            return $query->where('posts.len',2);
        }
        if($length==='long'){ //长推
            return $query->where('posts.len',3);
        }
        if($length==='no_limit'){ //无限制
            return $query;
        }
        if($length==='not_small'){
            return $query->where('posts.len','>',1);
        }
        return $query;
    }

    public function scopeReviewType($query, $reviewType)
    {
        if($reviewType==='sosad_only'){ //站内推荐
            return $query->where('post_infos.reviewee_id','>',0);
        }
        if($reviewType==='none_sosad_only'){ //非站内推荐
            return $query->where('post_infos.reviewee_id','=',0);
        }
        return $query;
    }

    public function scopeReferTo($query, $reviewee_type='', $reviewee_id=0)
    {
        if($reviewee_id>0&&$reviewee_type){
            return $query->where('post_infos.reviewee_type', $reviewee_type)->where('post_infos.reviewee_id', $reviewee_id);
        }else{
            return $query;
        }
    }

    public function scopeReviewAuthorAttitude($query, $withAuthorAttitude)
    {
        if($withAuthorAttitude==='approved_only'){
            return $query->where('post_infos.author_attitude', 1);
        }
        if($withAuthorAttitude==='none_approved_only'){
            return $query->where('post_infos.author_attitude', 2);
        }
        return $query;
    }

    public function scopeReviewMaxRating($query, $withMaxRating)
    {
        if($withMaxRating){
            return $query->where('post_infos.rating', '<=', $withMaxRating);
        }else{
            return $query;
        }
    }

    public function scopeReviewMinRating($query, $withMinRating)
    {
        if($withMinRating){
            return $query->where('post_infos.rating', '>=', $withMinRating);
        }else{
            return $query;
        }
    }

    public function favorite_replies()//这个post里面，回复它的post的comment中，最多赞的
    {
        return Post::where('reply_to_id', $this->id)
        ->with('author.title')
        ->where('fold_state', 0) // 必须没有被作者折叠
        ->orderBy('upvote_count', 'desc')
        ->take(5)
        ->get();
    }

    public function newest_replies() // 这个post里面，回复它的postcomment中，最新的5个，全部内容
    {
        return Post::with('author.title','last_reply')
        ->where('reply_to_id', $this->id)
        ->where('fold_state', 0) // 必须没有被作者折叠
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    }

    public function reward_check(){
        $msg = '';
        if(!in_array($this->type,['chapter','work'])){
            if($this->post_check('long_comment')){
                $this->user->reward('long_post');
                $msg = $msg.', 得到了长评奖励';
            }
            if($this->post_check('first_post')){
                $this->user->reward("first_post");
                $msg = $msg.', 得到了新章节率先回帖的奖励';
            }
            $this->user->reward("regular_post");
            return '恭喜，你成功回帖，缓存数分钟后会在讨论主题内展示'.$msg;
        }

        if(in_array($this->type,['chapter','work'])){
            if($this->post_check('standard_chapter')){
                $this->user->reward('standard_chapter');
                $msg = $msg.', 得到了标准正文奖励';
            }else{
                $this->user->reward('short_chapter');
                $msg = $msg.', 得到了短正文奖励';
            }
            return '恭喜，你成功发布正文，缓存数分钟后会在目录页展示'.$msg;
        }
    }

    public function retract_check($old_post){
        $msg = '';
        if(!in_array($this->type,['chapter','work'])){
            if($old_post->post_check('long_comment')&&!$this->post_check('long_comment')){
                $this->user->retract('reduce_long_to_short');
                $msg = ',长内容变短内容，扣除对应虚拟物。';
            }
            return '成功修改回帖'.$msg;
        }

        if(in_array($this->type,['chapter','work'])){
            if($old_post->post_check('standard_chapter')&&!$this->post_check('standard_chapter')){
                $this->user->retract('reduce_long_to_short');
                $msg = ',长章节变短章节，扣除对应虚拟物。';
            }
            return '成功修改章节'.$msg;
        }
    }

    public function post_check($requirement = '')
    {
        switch ($requirement) {
            case 'long_comment'://长评？ post_check('long_comment')
            if($this->char_count>config('constants.longcomment_length')){
                return true;
            }
            break;
            case 'standard_chapter'://章节更新需求 post_check('standard_chapter')
            if($this->char_count>config('constants.update_min')){
                return true;
            }
            case 'first_post'://是否最新回帖 post_check('first_post')
            if($this->parent&&$this->parent->type==="chapter"&&$this->parent->reply_count<=2){
                return true;
            }
            default:
            break;
        }
        return false;
    }

    public function latest_rewards()
    {
        return \App\Models\Reward::with('author')
        ->withType('post')
        ->withId($this->id)
        ->orderBy('created_at','desc')
        ->take(10)
        ->get();
    }

    public function latest_upvotes()
    {
        return \App\Models\Vote::with('author')
        ->withType('post')
        ->withId($this->id)
        ->withAttitude('upvote')
        ->orderBy('created_at','desc')
        ->take(10)
        ->get();
    }

    public function increment_reply_position($position=0)
    {
        if($position>0&&is_numeric($position)){
            $reply_position_record = PostReplyPosition::where('post_id',$this->id)->where('position',$position)->first();
            if(!$reply_position_record){
                PostReplyPosition::create([
                    'post_id' => $this->id,
                    'position' => $position,
                    'reply_count' => 1,
                ]);
            }else{
                $reply_position_record->increment('reply_count');
            }
        }
        return;

    }

}
