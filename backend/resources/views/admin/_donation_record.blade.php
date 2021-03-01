<span class="badge bianyuan-tag badge-tag">{{$record->donation_kind}}</span>
<span class="badge newchapter-badge badge-tag {{$record->is_claimed? '':'hidden'}}">已关联</span>
<span class="">{{$record->donation_email}}</span>
@if($user)
<a href="{{ route('user.show', $user->id) }}" class="admin-see-through">{{ $user->name }}</a>
@endif
<span>${{$record->donation_amount}}&nbsp;</span>
<span>{{$record->donated_at? $record->donated_at->setTimeZone('Asia/Shanghai'):''}}</span>
