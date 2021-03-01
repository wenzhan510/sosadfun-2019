<b>邮箱修改记录ID：</b>{{$record->id}}&nbsp;<b>修改时间：</b>{{$record->created_at->setTimezone('Asia/Shanghai')}}&nbsp;<b>修改IP：</b>{!! StringProcess::ip_link($record->ip_address) !!}&nbsp;
<b>修改token：</b>{{$record->token}}&nbsp;
<b>旧邮箱：</b>{{$record->old_email}}&nbsp;(验证于:{{$record->old_email_verified_at? $record->old_email_verified_at->setTimezone('Asia/Shanghai'):'暂未验证'}}）<b>新邮箱：</b>{{$record->new_email}}(邮箱确认修改于{{$record->email_changed_at? $record->email_changed_at->setTimezone('Asia/Shanghai'):'无'}})<br>
@if($record->admin_revoked_at)
<code>管理员于{{$record->admin_revoked_at->setTimezone('Asia/Shanghai')}}恢复至原邮箱</code><br>
@endif
@if(!$record->admin_revoked_at&&$user->email === $record->new_email)
<a class="btn btn-md btn-danger sosad-button-control" href="{{route('admin.convert_to_old_email', ['user'=>$user->id,'record'=>$record->id])}}">改回旧邮箱{{$record->old_email}}</a>
@endif
