import * as React from 'react';
import './page.scss';
import { classnames } from '../../../utils/classname';

export function Page (props:{
  children:React.ReactNode;
  top?:React.ReactNode;
  noTopBorder?:boolean;
  bottom?:React.ReactNode;
  className?:string;
  style?:React.CSSProperties;
  zIndex?:number;
}) {
  return <div className="page" style={{zIndex: props.zIndex || undefined}}>
    { props.top &&
      <div className="top" style={{
        borderBottom: props.noTopBorder ? 'unset' : undefined,
      }}>
        {props.top}
      </div>
    }

    <div className={classnames('body', props.className)} style={props.style}>
      {props.children}
    </div>

    { props.bottom &&
      <div className="bottom">
        {props.bottom}
      </div>
    }

  </div>;
}