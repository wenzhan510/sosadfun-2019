import * as React from 'react';
import { classnames } from '../../../utils/classname';

export class TagList extends React.Component<{
  // props
  children:React.ReactNode;
  className?:string;
  style?:React.CSSProperties;
}, {
  // states
}> {
  public render () {
    return <div className={classnames('tags', this.props.className)} style={this.props.style}>
      {this.props.children}
    </div>;
  }
}