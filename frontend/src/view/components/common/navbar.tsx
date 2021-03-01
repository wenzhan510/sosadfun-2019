import * as React from 'react';
import './navbar.scss';
import { classnames } from '../../../utils/classname';

interface Props {
  goBack:() => void;
  goBackText?:string;
  menu?:JSX.Element;
  style?:React.CSSProperties;
}

interface State {
}

export class NavBar extends React.Component<Props, State> {
  public render () {
    return <div style={this.props.style} className="comps-common-navbar">
      <div className="navbar-prev" onClick={this.goBack}>{this.props.goBackText || <i className="fas fa-chevron-left"></i>}</div>

      <div className="navbar-start">
        {this.props.children}
      </div>

      {this.props.menu || ''}
    </div>;
  }

  public goBack = () => {
    this.props.goBack();
  }

  public static MenuText = MenuText;
  public static MenuIcon = MenuIcon;
}

function MenuText (props:{
  disabled?:boolean;
  onClick:() => void;
  value:string;
}) {
  return (
    <div
      className={classnames('menu-text', {'disabled': props.disabled})}
      onClick={ props.onClick }>
    {props.value}
  </div>);
}

function MenuIcon (props:{
  children?:React.ReactNode;
  icon?:string;
  onClick:() => void;
}) {
  const icon = props.icon || 'fas fa-ellipsis-h';
  return <div className="menu-icon" onClick={props.onClick}>
    <i className={icon}></i>
  </div>;
}