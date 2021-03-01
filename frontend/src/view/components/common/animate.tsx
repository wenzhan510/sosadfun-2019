import * as React from 'react';
import './animate_slide.scss';
import { classnames } from '../../../utils/classname';

export type AnimateName =
  'slideInUp' |
  'slideOutUp' |
  'slideInDown' |
  'slideOutDown' |
  'slideInRight' |
  'slideOutRight' |
  'slideInLeft' |
  'slideOutLeft';

type AnimateSpeed =
  'fast' |
  'faster' |
  'slow' |
  'slower';

interface Props {
  children:React.ReactNode;
  name:AnimateName;
  speed?:AnimateSpeed;
  infinite?:boolean;
  className?:string;
  duration?:number;
  delay?:number;
}
interface State {
}

export class Animate extends React.Component<Props, State> {
  public render () {
    return <div
      className={classnames(
        'animated',
        this.props.className,
        {'infinite': this.props.infinite},
        this.props.speed,
      )}
      style={{
        animationDelay: this.props.delay && `${this.props.delay}s` || undefined,
        animationDuration: this.props.duration && `${this.props.duration || 200}s` || undefined,
        animationName: this.props.name,
      }}
    >
      {this.props.children}
    </div>;
  }
}