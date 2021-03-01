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
                <h4>友情提醒，本页面含有IP访问频率限制，为了你的正常注册，注册时<code>请不要刷新或倒退</code>网页。</h4>
            </div>
            <div class="panel-body">
                @include('shared.errors')
                <form method="POST" action="{{ route('register') }}">
                    {{ csrf_field() }}

                    <input type="text" name="registration_method" class="form-control hidden" value="via_invitation_email">

                    <input type="text" name="token" class="form-control hidden" value="{{$application->token}}">

                    <input type="text" name="email" class="form-control hidden" value="{{$application->email}}">

                    <input type="text" name="email_confirmation" class="form-control hidden" value="{{$application->email}}">

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
