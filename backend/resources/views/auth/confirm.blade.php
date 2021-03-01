<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $user->name }}的邮箱确认链接</title>
</head>
<body>
    <h1>uid.{{$user->id}}&nbsp;{{ $user->name }}&nbsp;你好，感谢你在 废文网 进行注册！</h1>
    <p>
        请点击下面的链接完成邮箱验证：
        <a href="{{ route('confirm_email', $info->activation_token) }}">
            {{ route('confirm_email', $info->activation_token) }}
        </a>
    </p>
    <p>链接中含个人信息，请注意个人信息保护。</p>
    <p>
        如果这不是你本人的操作，请忽略此邮件。
    </p>
    <br>
    <p>（部分浏览器和邮件客户端可能无法打开上述链接，并显示“地址不安全”，“地址被禁止访问”等信息。遇到这种情况，请复制链接，粘贴到其他浏览器中打开。）</p>
    <p>（邮件来自自动发件信箱，请勿直接回复本邮件。）</p>
</body>
</html>
