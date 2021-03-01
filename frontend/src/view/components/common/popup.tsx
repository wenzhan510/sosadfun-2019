import * as React from 'react';
import { classnames } from '../../../utils/classname';
import './popup.scss';

export class Popup extends React.Component <{
  // props
  onClose:() => void;
  bottom?:string; // default is center
  width?:string;
  minHeight?:string;
  style?:React.CSSProperties;
  className?:string;
  customizeContent?:boolean;
}, {
  // state
}> {
  public render () {
    const { props } = this;
    return <div className={classnames('popup', {'bottom': !!props.bottom})}>
      <div className="background"
        onClick={() => props.onClose()}>
      </div>

      {this.renderContent()}

    </div>;
  }

  public renderContent () : undefined|JSX.Element|React.ReactNode {
    const { props } = this;
    if (!this.props.customizeContent) {
      return <div className={classnames('content', props.className)}
        style={Object.assign({
          width: props.width || undefined,
          minHeight: props.minHeight || undefined,
          bottom: props.bottom || undefined,
        },                   props.style || {})}>
          {props.children}
      </div>;
    }
    return props.children;
  }
}