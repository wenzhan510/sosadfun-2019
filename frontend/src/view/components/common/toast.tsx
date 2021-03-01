import React from 'react';
import { classnames } from '../../../utils/classname';
import './toast.scss';

export enum ToastType {
  error= 'error',
  warning='warning',
  success='success',
  info='info',
}

export interface ToastProps {
  type?:ToastType;
  content:string;
  onClose:() => void;
  style?:React.CSSProperties;
}

export class Toast extends React.Component<ToastProps> {
  public render() {
    const toastType = this.props.type || ToastType.info;
    return <div className={classnames('toast', toastType)} style={this.props.style}>
      <i className={classnames('fa fa-info-circle', 'icon-info')}/>
      <div className="content">{this.props.content}</div>
      <div onClick={this.props.onClose} className="icon">
        <i className="fa fa-times"/>
      </div>
    </div>;
  }
}