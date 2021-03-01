@extends('layouts.default')
@section('title', '搜索用户结果')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <a href="{{ route('admin.index') }}">{{ __('pageview.admin') }}</a>
        /
        <a href="{{route('admin.searchrecordsform')}}">搜索记录</a>
        <div class="panel panel-default">
            <div class="panel-body">
                @include('admin._search_record_form')
            </div>
        </div>

        @if(count($black_list_emails)>0)
        <h1>含「{{$name}}」的黑名单列表</h1>
        <a href="{{route('admin.manageblacklistform')}}" class="btn btn-lg btn-danger sosad-button pull-right">>>修改黑名单</a>
        <div class="panel panel-default">
            <div class="panel-body">
                @foreach($black_list_emails as $black_list_email)
                {{$black_list_email->email}},&nbsp;
                @endforeach
            </div>
        </div>
        {{$black_list_emails->links()}}
        @endif

        @if(count($users)>0)
        <h1>当前用户的用户ID/用户名/邮箱含有「{{$name}}」</h1>
        <div class="grayout font-6">
            <span class="admin-symbol">IP</span>:&nbsp;<span>仪器信息</span>&nbsp;<span class="grayout">[记录时间]</span>&nbsp;<span class="warning-tag">#请求次数</span>
            &nbsp;&nbsp;&nbsp;其中 IP 计数与 Session 计数只统计请求次数在10次以上的 Session ，而 IP段 和设备计数会统计全部的 Session
        </div>
        @foreach($users as $user)
        <div class="panel panel-default">
            <div class="panel-heading">
                @include('admin._user_info')
            </div>
            @foreach($user->emailmodifications as $record)
            <div class="panel-body">
                @include('admin._email_modification_record')
            </div>
            @endforeach
            @foreach($user->passwordresets as $record)
            <div class="panel-body">
                @include('admin._password_reset_record')
            </div>
            @endforeach
            @foreach($user->usersessions as $record)
            <div class="panel-body">
                @include('admin._session_record')
            </div>
            @endforeach
            @foreach($user->registrationapplications as $application)
            <?php $application->setAttribute('owner', $user); ?>
            <div class="panel-body">
                @include('admin._application_record')
            </div>
            @endforeach
            @foreach($user->donations as $record)
            <div class="panel-body">
                @include('admin._donation_record')
            </div>
            @endforeach
        </div>
        @endforeach
        {{$users->links()}}
        @endif

        @if(count($email_modification_records)>0)
        <h1>邮箱修改记录中含有「{{$name}}」</h1>
        @foreach($email_modification_records as $record)
        <?php $user = $record->user; ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                @include('admin._user_info')
            </div>
            <div class="panel-body">
                @include('admin._email_modification_record')
            </div>
        </div>
        @endforeach
        {{$email_modification_records->links()}}
        @endif

        @if(count($password_reset_records)>0)
        <h1>密码修改记录中含有「{{$name}}」</h1>
        @foreach($password_reset_records as $record)
        <?php $user = $record->user; ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                @include('admin._user_info')
            </div>
            <div class="panel-body">
                @include('admin._password_reset_record')
            </div>
        </div>
        @endforeach
        {{$password_reset_records->links()}}
        @endif

        @if(count($donation_records)>0)
        <h1>捐赠记录中含有「{{$name}}」</h1>
        @foreach($donation_records as $record)
        <?php $user = $record->user; ?>
        <div class="panel panel-default">
            @if($user)
            <div class="panel-heading">
                @include('admin._user_info')
            </div>
            @endif
            <div class="panel-body">
                @include('admin._donation_record')
            </div>
        </div>
        @endforeach
        {{$donation_records->links()}}
        @endif

        @if(count($application_records)>0)
        <h1>申请注册记录中含有「{{$name}}」</h1>
        @foreach($application_records as $application)
        <?php $user = $application->user; ?>
        <div class="panel panel-default">
            @if($user)
            <div class="panel-heading">
                @include('admin._user_info')
            </div>
            @endif
            <div class="panel-body">
                @include('admin._application_record')
            </div>
        </div>
        @endforeach
        {{$application_records->links()}}
        @endif

        @if(count($quotes)>0)
        <h1>题头中含有「{{$name}}」</h1>
        @foreach($quotes as $quote)
        <div class="panel panel-default">
            @include('quotes._quotes_review')
        </div>
        @endforeach
        {{$quotes->links()}}
        @endif
    </div>
</div>
@stop
