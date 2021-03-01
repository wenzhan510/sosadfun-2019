@extends('layouts.default')
@section('title', '黑名单管理')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <a href="{{ route('admin.index') }}">{{ __('pageview.admin') }}</a>
        /
        <a href="{{route('admin.searchrecordsform')}}">搜索记录</a>
        /
        黑名单管理
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>邮箱黑名单管理</h4>
            </div>
            <div class="panel-body">
                @include('shared.errors')
                <form method="POST" action="{{ route('admin.manageblacklist_submit') }}" name="manageblacklist_submit">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="name">在这里输入邮箱或IP「全名」：</label>
                        <input type="text" name="name" class="form-control" value="">
                        <label><input type="radio" name="name_type" value="email">email</label>&nbsp;&nbsp;
                        <label><input type="radio" name="name_type" value="ip_address">IP地址</label>&nbsp;&nbsp;
                    </div>
                    <input type="submit" name="remove_from_blacklist" value="从黑名单解除" class="btn btn-md btn-success cancel-button">
                    <input type="submit" name="add_to_blacklist" value="添加到黑名单" class="btn btn-md btn-danger cancel-button">
                </form>
            </div>
        </div>
    </div>
</div>
@stop
