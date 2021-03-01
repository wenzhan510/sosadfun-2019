<b>记录ID：</b>{{$record->id}}&nbsp;|&nbsp;<b>记录时间：</b>{{$record->created_at->setTimezone('Asia/Shanghai')}}&nbsp;|&nbsp;<b>同时间活跃的 Session 数量：</b>{{$record->session_count}}&nbsp;|&nbsp;<b>IP计数：</b>{{$record->ip_count}}&nbsp;|&nbsp;<b>IP段计数：</b>{{$record->ip_band_count}}&nbsp;|&nbsp;<b>设备计数：</b>{{$record->device_count}}&nbsp;|&nbsp;<b>移动设备计数：</b>{{$record->mobile_count}}
@if($record->session_data)
<div class="font-6 font-weight-400">
    <?php $data = json_decode($record->session_data) ?>
    <div id="full{{$record->id}}" class="hidden">
        <div>
            @foreach($data as $session_id => $session_data)
            @if(property_exists($session_data, 'request_count'))
            <div>
                @if(!strpos($session_data->user_agent ,'Windows')&&!strpos($session_data->user_agent ,'Macintosh'))
                <span class="badge newchapter-badge badge-tag}}">移动端</span>
                @endif
                {!! StringProcess::ip_link($session_data->ip) !!}:&nbsp;
                <span>{{$session_data->user_agent}}</span>
                <span class="grayout">[{{ Carbon::parse($session_data->time)->setTimezone('Asia/Shanghai')}}]</span>
                <span class="warning-tag">#{{ $session_data->request_count }}</span>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    <div id="abbreviated{{$record->id}}">
        @foreach($data as $session_id => $session_data)
        @if(property_exists($session_data, 'request_count') && $session_data->request_count>10)
        <div>
            @if(!strpos($session_data->user_agent ,'Windows')&&!strpos($session_data->user_agent ,'Macintosh'))
            <span class="badge newchapter-badge badge-tag}}">移动端</span>
            @endif
            {!! StringProcess::ip_link($session_data->ip) !!}:&nbsp;
            <span>{{$session_data->user_agent}}</span>
            <span class="grayout">[{{ Carbon::parse($session_data->time)->setTimezone('Asia/Shanghai')}}]</span>
            <span class="warning-tag">#{{ $session_data->request_count }}</span>
        </div>
        @endif
        @endforeach
    </div>
</div>
@endif
<div>
    @if(!$user->no_logging)
    <a class="btn btn-xs btn-danger admin-control-button" href="{{route('admin.forbid_shared_account', ['user'=>$user->id])}}">封禁共享账户</a>&nbsp;&nbsp;&nbsp;&nbsp;
    @endif
    <a class="btn btn-xs btn-danger sosad-button-control" href="{{route('admin.reset_password', ['user'=>$user->id])}}">重置密码</a>
    &nbsp;&nbsp;&nbsp;&nbsp;<a type="button" name="button" id="expand{{$record->id}}" onclick="expanditem('{{$record->id}}')" class="font-5">>>展开更多记录</a>
</div>
