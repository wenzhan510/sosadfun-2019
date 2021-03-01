<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}的账户修改验证邮件</title>
</head>
<body>
    <h1>uid.{{$user->id}}&nbsp;「{{ $user->name }}」你好。</h1>
    <h2>你刚刚发起了邮箱更改请求，请首先验证本邮箱有效。</h2>
    <p>将原邮箱：{{$record->old_email}}</p>
    <p>更改为下述邮箱地址：{{$record->new_email}}</p>
    <p>请复制下列链接，从浏览器空白页面中打开，完成邮箱更改的确认。</p>
    <a href="{{ route('update_email_by_token', $record->token) }}">
        {{ route('update_email_by_token', $record->token) }}
    </a>
    <p>链接中含个人信息，请注意个人信息保护。</p>
    <p>如果这不是你的操作，请无视本邮件。</p>
    <br>
    <p>（部分浏览器和邮件客户端可能无法打开上述链接，并显示“地址不安全”，“地址被禁止访问”等信息。遇到这种情况，请复制链接，粘贴到其他浏览器中打开。）</p>
    <p>（邮件来自自动发件信箱，请勿直接回复本邮件。）</p>
</body>
</html>
