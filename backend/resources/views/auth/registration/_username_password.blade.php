<div class="form-group">
    <label for="name">用户名（笔名）：</label>
    <h6 class="grayout">（用户名注册后，暂时无法更改哦。）</h6>
    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
</div>

<div class="form-group">
    <label for="password">密码：</label>
    <input type="password" name="password" class="form-control" value="{{ old('password') }}">
    <h6>(密码至少10位，需包含至少一个大写字母，至少一个小写字母，至少一个数字，至少一个特殊字符。常用特殊字符：#?!@$%^&*-_)</h6>
</div>

<div class="form-group">
    <label for="password_confirmation">确认密码：</label>
    <input type="password" name="password_confirmation" class="form-control" value="{{ old('password_confirmation') }}">
</div>

<div class="panel panel-default text-center">
    <div class="panel-title">
        <h4>注册协议</h4>
    </div>
    <div >
        <p>丧病之家，你的精神墓园</p>
        <p>比欲哭无泪更加down，不抑郁不要钱</p>
        <p>本站<u><em><b>禁抄袭，禁人身攻击，禁人肉，禁恋童</b></em></u></p>
        <p>请<u><em><b>不要发布侵犯他人版权的文字</b></em></u></p>
        <p>请确保你已<u><em><b>年满<span class="warning-tag">十八</span>岁</b></em></u></p>
        <p>祝你玩得愉快</p>
        <br>
    </div>
    <div class="panel-footer text-center">
        <div class="text-center no-selection">
            <label for="promise">注册担保：</label>
            <h6 class="grayout">请手工输入下面这句红色的话：</h6>
            <h6 class="" style="color:#f44248"><em>{{ config('preference.register_promise') }}</em></h6>
            <textarea name="promise" rows="3" class="form-control">{{ old('promise') }}</textarea>
        </div>
    </div>
    <div class="panel-footer text-center h6">
        <div class="">
            <input type="checkbox" name="have_read_policy1" value=true>
            <span>我知道可以左上角【搜索】关键词获取使用帮助</span>&nbsp;<u><a href="{{'help'}}">帮助页面</a></u>
        </div>
        <div class="">
            <input type="checkbox" name="have_read_policy2" value=true>
            <span>我已阅读《版规》中约定的社区公约，同意遵守版规</span>&nbsp;<u><a href="{{ route('thread.show', 136) }}">版规详情</a></u>
        </div>
        <div class="">
            <input type="checkbox" name="have_read_policy3" value=true>
            <span>我保证自己<span class="warning-tag">年满十八周岁</span>，神智健全清醒，承诺为自己的言行负责。</span>
        </div>
    </div>
</div>

@if (env('NOCAPTCHA_SITEKEY'))
<div class="form-group row">
    <div class="col-md-6 offset-md-4">
        <div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>
    </div>
</div>
@endif
