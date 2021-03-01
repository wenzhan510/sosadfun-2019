<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}的废文网密码修改提醒</title>
</head>
<body>
    <h1>uid.{{$user->id}}&nbsp;「{{ $user->name }}」你好，你刚刚发起了密码修改请求！</h1>
    <p>以下是你的废文网密码重置邮件链接</p>
    <a href="{{url(route('password.reset', $token, false))}}">{{url(route('password.reset', $token, false))}}</a>
    <p>（部分浏览器和邮件客户端可能无法打开上述链接，并显示“地址不安全”，“地址被禁止访问”等信息。遇到这种情况，请复制链接，粘贴到其他浏览器中打开。）</p>
    <p>（邮件来自自动发件信箱，请勿直接回复本邮件。）</p>
</body>
</html>
