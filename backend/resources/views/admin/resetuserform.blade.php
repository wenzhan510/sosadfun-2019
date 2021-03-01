@extends('layouts.default')
@section('title', '重置账户')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <a href="{{ route('admin.index') }}">{{ __('pageview.admin') }}</a>
        /
        <a href="{{route('admin.searchrecordsform')}}">搜索记录</a>
        /
        重置账户
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>重置账户管理</h4>
            </div>
            <div class="panel-body">
                @include('shared.errors')
                <form method="POST" action="{{ route('admin.resetuser_submit', $user->id) }}" name="resetuser_submit">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="name">用户名：</label>
                        <input type="text" name="name" class="form-control" value="{{$user->name}}">
                    </div>
                    <div class="form-group">
                        <label for="email">邮箱：</label>
                        <input type="text" name="email" class="form-control" value="{{$user->email}}">
                    </div>
                    <div class="form-group">
                        <label for="password">密码：</label>
                        <input type="text" name="password" class="form-control" value="password">
                    </div>
                    <div class="form-group">
                        <label for="level">用户等级：</label>
                        <input type="text" name="level" class="form-control" value="{{$user->level}}">
                    </div>
                    <div class="form-group">
                        <label for="quiz_level">答题等级：</label>
                        <input type="text" name="quiz_level" class="form-control" value="{{$user->quiz_level}}">
                    </div>

                    <div class="form-group">
                        <label><input type="text" style="width: 80px" name="salt" value="{{$user_info->salt}}">盐粒</label>，
                        <label><input type="text" style="width: 80px" name="fish" value="{{$user_info->fish}}">咸鱼</label>，
                        <label><input type="text" style="width: 80px" name="ham" value="{{$user_info->ham}}">火腿</label>，
                        <label><input type="text" style="width: 80px" name="token_limit" value="{{$user_info->token_limit}}">邀请额度</label>，
                        <label><input type="text" style="width: 80px" name="no_ads_reward_limit" value="{{$user_info->no_ads_reward_limit}}">免广告码</label>，
                        <label><input type="text" style="width: 80px" name="qiandao_reward_limit" value="{{$user_info->qiandao_reward_limit}}">补签卡</label>，
                    </div>

                    <div class="form-group">
                        <label><input type="text" style="width: 80px" name="qiandao_all" value="{{$user_info->qiandao_all}}">总签到天数</label>，
                        <label><input type="text" style="width: 80px" name="qiandao_max" value="{{$user_info->qiandao_max}}">最高连续签到天数</label>，
                        <label><input type="text" style="width: 80px" name="qiandao_continued" value="{{$user_info->qiandao_continued}}">当前连续签到天数</label>，
                        <label><input type="text" style="width: 80px" name="qiandao_last" value="{{$user_info->qiandao_last}}">上次连续签到天数</label>，
                    </div>

                    <button type="submit" class="btn btn-lg btn-danger sosad-button">重置这个账户</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
