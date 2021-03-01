import * as React from 'react';
import { classnames } from '../../../utils/classname';
import './list.scss';

class ListItem extends React.Component<{
  //props
  children:React.ReactNode;
  className?:string;
  arrow?:boolean;
  onClick?:() => void;
  style?:React.CSSProperties;
  noBorder?:boolean;
}, {
  //state
}> {
  public render () {
    return <div className={classnames('list-item', this.props.className)}
      style={Object.assign(
        {
          border: this.props.noBorder ? 'none' : undefined,
        },
        this.props.style || {})}
      onClick={this.props.onClick}>
      {this.props.arrow && <div className="list-arrow">
        <i className="fas fa-angle-right icon"></i>
      </div>}
      {this.props.children}
    </div>;
  }
}

export class List extends React.Component<{
  //props
  children:React.ReactNode;
  className?:string;
  noBorder?:boolean;
  style?:React.CSSProperties;
}, {
  //state
}> {
  public static Item = ListItem;

  public render () {
    return <div className={classnames('list', this.props.className)}
        style={Object.assign({
          border: this.props.noBorder ? 'none' : undefined,
          boxShadow: this.props.noBorder ? 'none' : undefined,
        },                   this.props.style || {})}>
      {this.props.children}
    </div>;
  }
}