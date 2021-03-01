@include('shared.errors')
<form method="GET" action="{{ route('admin.searchrecords') }}" name="searchrecords">
    <div class="form-group">
        <label for="name">用户ID/用户名/IP/邮箱相似字段(为了搜索效率请使用尽量长的字段，如完整邮件地址，或完整用户名)：</label>
        <input type="text" name="name" class="form-control" value="">
        <label><input type="radio" name="name_type" value="user_id">搜指定用户uid</label>&nbsp;
        <label><input type="radio" name="name_type" value="username">搜索用户名</label>&nbsp;
        <label><input type="radio" name="name_type" value="email">搜索email</label>&nbsp;
        <label><input type="radio" name="name_type" value="ip_address">搜索IP地址</label>&nbsp;
        <label><input type="radio" name="name_type" value="latest_created_user">最新注册用户</label>&nbsp;
        <label><input type="radio" name="name_type" value="latest_invited_user">最新被邀请用户</label>&nbsp;
        <label><input type="radio" name="name_type" value="latest_email_modification">最新修改邮箱</label>&nbsp;
        <label><input type="radio" name="name_type" value="latest_password_reset">最新修改密码</label>&nbsp;
        <label><input type="radio" name="name_type" value="max_suspicious_sessions">全部可疑Session记录</label>&nbsp;
        <label><input type="radio" name="name_type" value="active_suspicious_sessions">今日可疑Session记录</label>&nbsp;
        <label><input type="radio" name="name_type" value="application_essay_like">搜索注册申请字段</label>&nbsp;
        <label><input type="radio" name="name_type" value="application_record_id">搜索注册申请ID</label>&nbsp;
        <label><input type="radio" name="name_type" value="is_forbidden">可回收账户</label>&nbsp;
        <label><input type="radio" name="name_type" value="quote_like">搜索题头字段</label>&nbsp;


    </div>
    <button type="submit" class="btn btn-lg btn-danger sosad-button">开始搜索</button>
</form>
