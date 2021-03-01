<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>废文网注册邀请</title>
</head>
<body>
    <h1>{{ $application->email }}&nbsp;你好，感谢你提交注册废文网的请求！</h1>
    <h4>经过审核，我们在此特别邀请你注册废文网</h4>
    <p>
        友情提醒，收到本邮件【并非】注册成功，你仍需点击下面的链接进行注册：
{{--        {{ route('register.by_invitation_email.submit_token', $application->token) }}--}}
    </p>
    <p>链接中含个人信息，请注意个人信息保护。</p>
    <p>
        如果这不是你本人的操作，请忽略此邮件。
    </p>
    <p>
        如果链接无法打开，请尝试清理浏览器缓存，或复制链接到其他浏览器内打开完成注册。
    </p>
</body>
</html>
