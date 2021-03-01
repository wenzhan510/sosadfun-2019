@extends('layouts.default')
@section('title', '新建邀请码')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>新建邀请码</h4>
            </div>
            <div class="panel-body">
                @include('shared.errors')

                <form method="POST" action="{{ route('invitation_token.store') }}" name="store_invitation_token">
                    {{ csrf_field() }}
                    <div class="">
                        <label for="token">填token名称：</label>
                        <h6>（请统一以“SOSAD_”开头，可含字母及下划线，如“SOSAD_invitation_tokens”）</h6>
                        <input type="text" name="token" class="form-control" value="">
                    </div>
                    <div class="">
                        <label for="expireAt">设置可用时间：</label>
                        <label><input type="text" style="width: 80px" name="eligible-days" value="0">天</label>
                        <label><input type="text" style="width: 80px" name="eligible-hours" value="0">小时</label>
                    </div>
                    <div class="">
                        <label for="invitation_times">设置可用次数：</label>
                        <label><input type="text" style="width: 80px" name="invitation_times" value="0">次</label>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-lg btn-danger sosad-button">新建邀请码</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
