@extends('layouts.default')
@section('title', '审核题头')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <!-- 导航 -->
        <div class="">
            <a type="btn btn-lg btn-danger sosad-button" href="{{ route('home') }}"><span class="glyphicon glyphicon-home"></span><span>{{__('pageview.homepage')}}</span></a>
            /
            <a href="{{ route('admin.review.index') }}" class="admin-symbol">待办审核</a>
            /
            审核题头
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1>审核题头</h1>
                @include('quotes._review_standard')
                <div class="">
                    「<a href="{{route('user.show',$user->id)}}">{{$user->name}}</a>」已通过题头：{{$stats['pass_count']}}个，不通过题头{{$stats['unpass_count']}}个。
                </div>
            </div>
            <div class="panel-body text-center" id="quotes_for_review">
                {{ $quotes->links() }}
                    @include('admin.review._quotes_review')
                {{ $quotes->links() }}
            </div>
        </div>
    </div>
</div>
@stop
