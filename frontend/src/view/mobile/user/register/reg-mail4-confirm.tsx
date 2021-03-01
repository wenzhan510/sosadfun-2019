import * as React from 'react';

export function RegMail4Confirm (props:{
  onClick:() => void;
  className?:string;
}) {
  return (
    <div className="popup-content">
      <p className="title"><b>确认提交</b></p>
      <p className="message">申请问卷一经提交【无法修改】，切勿匆忙作答！请确认完成后再提交。<br/>如果你暂时无法完成问卷，可以关闭页面，稍后只要从入口输入同一个邮箱即可继续完成问卷（无需重新做题）。<br/>请注意，如发生抄袭，换邮箱重复申请等情况，邮箱和IP将进入黑名单。</p>
      <div className="bottom-button">
        <button
          className="button color-primary"
          onClick={props.onClick}>
            确认
        </button>
      </div>
    </div>);
}