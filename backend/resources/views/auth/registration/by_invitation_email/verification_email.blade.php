<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>废文网注册申请邮箱确认</title>
</head>
<body>
    <h1>{{ $application->email }}&nbsp;你好，感谢你提交注册废文网的请求！</h1>
    <h4>为了确保你的邮箱可以接收邀请链接，请首先验证邮箱</h4>
{{--    <p>请打开注册页面，</p>--}}
{{--    <p>--}}
{{--        {{route('register.by_invitation_email.submit_email_form')}}--}}
{{--    </p>--}}
{{--    <p>于指定位置输入下列确认码（应为10个随机字母）：</p>--}}
    <p>请在注册页面的验证码输入框内输入下列确认码（应为10个随机字母）：</p>
    <h3>
        {{$application->email_token}}
    </h3>
    <br>
    <p>
        如果这不是你本人的操作，请忽略此邮件。
    </p>
    <p>（如页面并未显示输入“确认码”，请先输入自己的邮箱，如果尚未验证，将自动跳转至输入确认码页面）</p>
    <p>（邮件来自自动发件信箱，请勿直接回复本邮件。）</p>
</body>
</html>
