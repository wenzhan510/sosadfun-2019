@extends('layouts.default')
@section('title', '搜索用户')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <a href="{{ route('admin.index') }}">{{ __('pageview.admin') }}</a>
        /
        <a href="{{route('admin.searchrecordsform')}}">搜索记录</a>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>搜索用户</h4>
            </div>
            <div class="panel-body">
                @include('admin._search_record_form')
            </div>
        </div>
    </div>
</div>
@stop
