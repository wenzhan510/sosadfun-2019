@extends('layouts.default')
@section('title', '批量提交已通过的申请信息')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1>批量提交已通过的申请信息</h1>
                <h5>参加特殊活动的，具有特殊才艺如绘画写文特长的，或者赞助超过30元的</h5>
            </div>
            <div class="panel-body">
                @include('shared.errors')
                <form method="POST" action="{{ route('registration_application.upload') }}" name="registration_application_upload">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="body"><h4>正文：</h4></label>
                        <textarea id="mainbody" name="body" rows="14" class="form-control" placeholder="def@qq.com&#10;abcde@qq.com&#10;(可以在excel中整理后复制粘贴整行过来。)">{{ old('body') }}</textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-lg btn-primary sosad-button">批量提交</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
