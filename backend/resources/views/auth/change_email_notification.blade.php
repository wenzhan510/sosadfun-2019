<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}的账户信息修改提醒</title>
</head>
<body>
    <h1>uid.{{$user->id}}&nbsp;「{{ $user->name }}」你好，你刚刚发起了邮箱更改请求！</h1>
    <p>请求具体要求为：</p>
    <div>
        <p>将原邮箱：{{$record->old_email}}</p>
        <p>更改为下述邮箱地址：{{$record->new_email}}</p>
        <p>请求id：{{$record->id}}</p>
        <p>请求发起时间：{{$record->created_at? $record->created_at->setTimeZone('Asia/shanghai'):''}}</p>
        <p>确认修改时间：{{Carbon::now()->setTimeZone('Asia/shanghai')}}</p>
        <p>本请求token：{{$record->token}}</p>
    </div>
    <br>
    <div>
        <p>如果这【不是】你本人的操作，请按照下述流程，通过废文网公共邮箱完成盗号申诉，申诉时请提供本邮件截图。</p>
        <p>具体申诉流程及格式，可直接搜索关键词“盗号”获取: <a href="https://sosad.fun/search?search=%E7%9B%97%E5%8F%B7">https://sosad.fun/search?search=%E7%9B%97%E5%8F%B7</a></p>
    </div>
    <p>（邮件来自自动发件信箱，请不要直接回复本邮件）</p>
</body>
</html>
