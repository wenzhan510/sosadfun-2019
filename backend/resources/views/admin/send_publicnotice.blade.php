@extends('layouts.default')
@section('title', '发送公共通知')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>发送公共通知</h4></div>
            <div class="panel-body">
                @include('shared.errors')
                <form method="POST" action="{{ route('admin.sendpublicnotice') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="body">消息正文：</label>
                        <textarea name="body" data-provide="markdown" id="messagetouser" rows="12" class="form-control" placeholder="消息">{{ old('body') }}</textarea>
                        <button type="button" onclick="retrievecache('messagetouser')" class="sosad-button-control addon-button">{{__('pageview.data_recovery')}}</button>
                    </div>
                    <button type="submit" class="btn btn-lg btn-primary">发布</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
