import * as React from 'react';
import { Card } from '../../../components/common/card';

export function RegMail1 (props:{
  email:string;
  className?:string;
  changeMailAddress:(mail:string) => () => void;
  redirectToRegMailInfo:() => void;
}) {
  return (
    <Card className="reg">
      {/* TODO: use h2 here, after h2 is defined in common.scss */}
      <p className="title">步骤一：填写邮箱</p>
      <div id="reg-mail1-info">
        <p>- 如果你没有提交过邮箱，可以在这里提交【新邮箱】。</p>
        <p>- 未完成的申请，必须从本页面输入邮箱【继续申请】，将自动前往当前申请所需的页面。</p>
        <p>- 页面含有防批量注册机制，申请中请<span className="red">【不要】</span>刷新，<span className="red">【不要】</span>使用浏览器“返回”前页面继续提交。</p>
        <p>- 如果你已完成申请，可以在这里输入邮箱【查询进度】。正常查询不属于“重复提交”。</p>
        <p>-【站内活动】奖励的链接会直接发送到参与活动的邮箱，同样可以在这里查询和补发。</p>
        <p>- 继续下一步之前，请确保你已阅读以下文档:<br/>
          <a onClick={props.redirectToRegMailInfo}>《通过邮箱申请注册邀请链接的详细步骤》</a>
        </p>
      </div>

      <div className="input-text">
        {/* TODO: check if valid mail address, replace with common component inputText */}
        <input type="text"
          value={props.email}
          onChange={(e) => props.changeMailAddress(e.target.value)()}
          placeholder="请输入邮箱"></input>
      </div>
      <p>为保证注册公平，避免机器恶意注册，本页面含有防批量注册机制，五分钟只能提交一次邮箱，请核实后再提交邮箱，避免反复提交邮箱。</p>
    </Card>);
}