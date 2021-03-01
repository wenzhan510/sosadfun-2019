@if($user->no_logging)
<span class="badge bianyuan-tag badge-tag">封禁中</span>
@endif
<b>ID：</b>{{$user->id}} &nbsp;
<b>昵称：</b><a href="{{route('user.show', $user->id)}}">{{$user->name}}</a>&nbsp;
<b>email：</b>{{$user->email}}&nbsp;
<b>注册时间：</b>{{$user->created_at->setTimezone('Asia/Shanghai')}}&nbsp;
<b>当前邮箱验证于：</b>{{$user->info->email_verified_at ? $user->info->email_verified_at->setTimezone('Asia/Shanghai'):'无'}}&nbsp;
<b>注册IP：</b>{!! StringProcess::ip_link($user->info->creation_ip??'') !!}&nbsp;
@if($user->info->invitor_id>0)
<b>邀请人：</b><a href="{{route('user.show', $user->info->invitor_id)}}">UID{{ $user->info->invitor_id }}</a>&nbsp;
@else
<b>邀请码：</b>{{$user->info->invitation_token??'无'}}&nbsp;
@endif
@if($user->info->is_forbidden)
<a href="{{route('admin.resetuser_form', $user->id)}}" class="btn btn-xs btn-danger sosad-button-control">重置该用户</a>
@endif
