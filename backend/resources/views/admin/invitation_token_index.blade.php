@extends('layouts.default')
@section('title', '查看邀请码列表')
@section('content')
<div class="container-fluid">
    <div class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>全部邀请码列表</h4>
                <div class="panel-body">
                    @foreach ($invitation_tokens as $invitation_token)
                    <div class="row h5">
                        <div class="{{ $invitation_token->invite_until<Carbon::now()||$invitation_token->invitation_times<=0 ?'grayout':''}}">
                            <span>邀请码内容：{{ $invitation_token->token }}</span>
                            <span>邀请人：
                                <a href="{{ route('user.show', $invitation_token->user_id) }}">{{ $invitation_token->user->name }}</a>
                            </span>
                            <span>
                                已邀请：{{ $invitation_token->invited }}；剩余次数：{{ $invitation_token->invitation_times }}
                            </span>
                            <span>
                                创建时间：{{ Carbon::parse($invitation_token->created_at)->setTimezone('Asia/Shanghai') }}
                            </span>
                            <span>
                                失效时间：{{ Carbon::parse($invitation_token->invite_until)->setTimezone('Asia/Shanghai') }}
                            </span>
                        </div>
                    </div>
                    <hr>
                    @endforeach
                </div>
                {{ $invitation_tokens->links() }}
                <div class="text-center">
                    <a href="{{ route('invitation_token.create') }}" class="btn btn-success sosad-button">新建邀请码</a>
                </div>
            </div>
        </div>
    </div>
</div>
@stop
