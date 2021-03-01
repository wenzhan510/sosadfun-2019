import * as React from 'react';
import { Card } from '../../../components/common/card';
import { Accordion } from '../../../components/common/accordion';
import { APIResponse } from '../../../../core/api';
import { notice } from '../../../components/common/notice';

export function RegMail3 (props:{
  email:string;
  className?:string;
  regMailToken:string;
  resendEmail:(email:string) => Promise<APIResponse<'registerByInvitationEmailResendEmailVerification'>>;
  changeRegMailToken:(token:string) => () => void;
}) {
  const onClickResendButton = async () => {
    // TODO: rate limit
    try {
      const { email } = await props.resendEmail(props.email);
      notice.success(`已发送邮件到${email}`);
    } catch (e) {
      notice.requestError(e);
    }
  };

  return (
    <Card className="reg">
      {/* TODO: use h2 here, after h2 is defined in common.scss */}
      <p className="title">步骤三：验证注册邮箱</p>
      <p className="small-warning">你正在使用 {props.email} 进行注册，如果邮箱有误，请勿继续！</p>
      <p className="sub-title">你好！感谢你来到废文！为了确保你的邮箱正确无误，可以接收邀请链接，请先确认你的邮箱！</p>

      <div id="verify-token">
        <p>从邮箱收到的确认码（10位随机字母）</p>

        <div className="input-text">
          <input type="text"
            maxLength={10}
            value={props.regMailToken}
            onChange={(e) => props.changeRegMailToken(e.target.value)() }
            placeholder="请输入确认码"></input>
          <span
            className="small-warning"
            id="resend-token"
            onClick={onClickResendButton}>
              重新发送邮件确认码
          </span>
        </div>
        <p>为保证注册公平，避免机器恶意注册，页面含有防批量注册机制，五分钟只能提交一次确认码，请核实后再提交确认码，勿直接“返回”前页面重新提交。</p>
      </div>

      <Accordion title={'友情提醒'} arrow={true}>
        <div className="warning-content">
          <p>请【仔细】检查邮箱输入情况，确认邮箱无误。错误的邮箱将无法接收确认码，也无法接收注册邀请邮件。为了确保验证邮件正常送达，<span className="red">请务必使用个人目前常用、可用的邮箱地址。</span></p>
          <p>正常的邮件形式一般为abcdefg@163.com，123456789@qq.com，而不是www.abcdefg@163.com，www.123456789@qq.com，更不是123456789@qq.con。请使用正确的邮件名称格式。</p>
          <p>请仔细检查个人收件箱/垃圾箱，修改自己的垃圾邮件设置，再重发邮件。重复发件容易被收件箱拒收，因此请你等待恰当时间间隔再行重发邮件。<span className="red">qq邮箱拒信严重</span>，请尽量不要使用qq邮箱。</p>
          <p>邮箱系统在10pm-1am繁忙，如有可能，建议优先选择凌晨、午后等访问人数较少的时间段进行验证。</p>
          <p>如果一直无法收到验证邮件，建议更换邮箱，重新申请。</p>
        </div>
      </Accordion>
    </Card>);
}