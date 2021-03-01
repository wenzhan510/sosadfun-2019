import * as React from 'react';
import './slider.scss';
import { classnames } from '../../../utils/classname';

class SliderItem extends React.Component<{
  // props
  children:React.ReactNode;
  style?:React.CSSProperties;
  className?:string;
}, {
  // state
}> {
  public render () {
    return <div className={classnames('slider-item', this.props.className)}
        style={this.props.style}>
      {this.props.children}
    </div>;
  }
}

export class Slider extends React.Component<{
  // props
  children:React.ReactNode;
  style?:React.CSSProperties;
  className?:string;
}, {
  // state
}> {
  public static Item = SliderItem;

  public render () {
    return <div className={classnames('slider-container', this.props.className)}
        style={this.props.style}>
      {this.props.children}
    </div>;
  }
}