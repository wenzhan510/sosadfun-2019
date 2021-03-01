import * as React from 'react';

export function Center (props:{
  children:React.ReactNode;
  style?:React.CSSProperties;
  width?:string;
  height?:string;
}) {
  return <div style={Object.assign(
    {
      position: 'absolute',
      display: 'flex',
      justifyContent: 'center',
      alignItems: 'center',
      width: props.width || '100%',
      height: props.height || '100%',
    },
    props.style || {})}>
    {props.children}
  </div>;
}