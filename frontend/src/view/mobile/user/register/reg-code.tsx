import * as React from 'react';
import { Card } from '../../../components/common/card';

export function RegCode (props:{
  className?:string;
  regCode:string;
  changeRegCode:(code:string) => () => void;
}) {
  return (

    <Card className="reg">
      <div className="input-text" id="reg-code-input">
        <input type="text"
          maxLength={191}
          minLength={6}
          value={props.regCode}
          onChange={(e) => props.changeRegCode(e.target.value)()}
          placeholder="请输入邀请码"></input>
      </div>
      <p>为保证注册公平，避免机器恶意注册，本页面含有防批量注册机制，五分钟内只能提交一次邀请码。请核实后再提交邀请码，避免反复提交邀请码。</p>
    </Card>);
}