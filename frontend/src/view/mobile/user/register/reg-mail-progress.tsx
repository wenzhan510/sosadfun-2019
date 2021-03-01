import * as React from 'react';
import { Card } from '../../../components/common/card';
import { DB } from '../../../../config/db-type';

export function RegMailProgress (props:{
  email:string;
  registrationStatus:DB.RegistrationApplication;
  className?:string;
}) {
  const { has_quizzed, email_verified_at, submitted_at, is_passed, last_invited_at } = props.registrationStatus.attributes;
  return (
    <Card className="reg">
    <p className="sub-title">
      申请档案编号{props.registrationStatus.id}  |  {props.email}
    </p>
    <div id="progress-detail">
      <p>- { has_quizzed ? '已' : '未' }答题</p>
      <p>- 邮箱{ email_verified_at ? '已' : '未' }确认</p>
      <p>- { submitted_at ? `申请提交时间: ${ submitted_at }` : '申请未提交'}</p>
    </div>

    { is_passed && (
      <div id="application-result">
        <p>恭喜，你的注册申请已经通过审核。<br/>
        { last_invited_at ?
          `邀请邮件发送时间: ${last_invited_at}` :
          '当前排队人数众多，尚未来得及发送邮件。你的邀请已进入发送队列，请耐心等待服务器空闲时依序发送邮件。'}
        </p>
        { last_invited_at && <a>重新发送邀请邮件</a> }
      </div>
    )}
    </Card>);
}