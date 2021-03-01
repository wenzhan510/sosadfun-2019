import * as React from 'react';
import { classnames } from '../../../utils/classname';
import { Colors } from '../../theme/theme';
import './tag.scss';

export class Tag extends React.Component<{
  // props
  children?:React.ReactNode;
  className?:string;
  style?:React.CSSProperties;
  onClick?:() => void;
  selected?:boolean;
  size?:'small'|'default'|'tiny';
  color?:Colors;
  selectedColor?:Colors;
  rounded?:boolean;
}, {
}> {
  public render () {
    return <span className={classnames(
        'tag',
        this.props.className,
        this.props.size,
        {'is-rounded': this.props.rounded},
        {[this.props.selectedColor || 'color-primary']: this.props.selected},
        {[this.props.color || '']: !this.props.selected},
      )}
      style={this.props.style}
      onClick={this.props.onClick}>{this.props.children}</span>;
  }
}