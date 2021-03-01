@extends('layouts.default')
@section('title', '邀请注册')
@section('content')
<div class="container-fluid">
    <style media="screen">
    </style>
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading lead">
                <h1>邀请注册</h1>
                <h4 class="text-center">友情提醒，本页面含有IP访问频率限制，为了你的正常注册，注册时<code>请不要刷新或倒退</code>网页。</h4>
                @if(!$invitation_token->is_public)
                <h5>你使用了私人邀请码！你的邀请人是：<a href="{{route('user.show', $invitation_token->user_id)}}">UID:{{$invitation_token->user_id}}</a></h5>
                <h5 class="warning-tag">如果被邀请人严重违反版规，邀请人需负连带责任。</h5>
                @endif
            </div>
            <div class="panel-body">
                @include('shared.errors')
                <form method="POST" action="{{ route('register') }}">
                    {{ csrf_field() }}
                    <input type="text" name="registration_method" class="form-control hidden" value="via_invitation_token">

                    <div class="form-group">
                        <label for="invitation_token">邀请码：</label>
                        <input type="text" name="invitation_token" class="form-control hidden" value="{{ $invitation_token->token }}">
                        <input type="text" class="form-control" value="{{ $invitation_token->token }}" disabled>
                    </div>

                    <div class="form-group">
                        <label for="email">邮箱：</label>
                        <h6 class="grayout">（请输入你的可用邮箱，用于激活账户和未来找回密码。请勿使用qq邮箱。）</h6>
                        <input type="text" name="email" class="form-control" value="{{ old('email') }}">
                    </div>

                    <div class="form-group">
                        <label for="email_confirmation">确认邮箱：</label>
                        <input type="text" name="email_confirmation" class="form-control" value="{{ old('email_confirmation') }}">
                        <h6>友情提醒，请<span class="warning-tag">仔细检查邮箱</span>输入情况，确认邮箱无误。输入错误的邮箱将无法激活自己的账户，也无法找回自己的账户。<br>为了确保验证邮件正常送达，请务必使用个人<span class="warning-tag">目前常用、可用的</span>邮箱地址。</h6>
                    </div>


                    @include('auth.registration._username_password')

                    <div class="text-center">
                        <button type="submit" class="btn btn-md btn-danger sosad-button">一键注册</button>
                        <h6>本页面含有IP访问频率限制，友情提醒，为了你的正常注册，<code>请不要刷新或倒退</code>页面。</h6>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
