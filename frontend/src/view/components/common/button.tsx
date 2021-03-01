import React from 'react';
import { classnames } from '../../../utils/classname';
import './button.scss';
import { Colors } from '../../theme/theme';

export class Button extends React.Component<{
  // props
  onClick:() => void;
  icon?:string;
  disabled?:boolean;
  ghost?:boolean;
  color?:Colors;
  size?:'small',
  type?:'ellipse' | 'high',
  inline?:boolean,
}, {}> {
  public ghostStyle:React.CSSProperties = {
    backgroundColor: 'rgba(0, 0, 0, 0)',
    color: 'var(--color-font)',
  };

  public render () {
    return (
      <div className={classnames('button-container', {'inline': this.props.inline})}>
        <div
          className={classnames(
            'button',
            this.props.color,
            this.props.size,
            this.props.type,
            {'disabled': this.props.disabled},
            {'ghost': this.props.ghost},
          )}
          onClick={() => !this.props.disabled && this.props.onClick()}
          style={this.props.ghost ? this.ghostStyle : undefined}
        >
          {this.props.icon && <i className={this.props.icon}></i>}
          {this.props.children}
        </div>
      </div>
    );
  }
}