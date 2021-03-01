@extends('layouts.default')
@section('title', '审核界面')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading"><h1>待办审核</h1></div>
        </div>
        <div class="panel panel-default">
            <div class="panel-body">

                <h4>题头（Quote）审核</h4>
                <ul>
                    <li><a href="{{route('admin.review.quote_index')}}">题头审核</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

@stop
