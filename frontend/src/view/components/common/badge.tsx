import * as React from 'react';
import './badge.scss';

interface Props {
  hidden?:boolean;
  dot?:boolean;
  num?:number;
  max?:number;
  children?:React.ReactNode;
  style?:React.CSSProperties;
}

interface State {

}

export class Badge extends React.Component<Props, State> {

  public render() {

    const {hidden, num, dot, max, children, style} = this.props;

    let value = '';
    const max_value:number = max || 99;
    let hidden_value = hidden ;

    if (num) {
      if (num <= 0) {
        hidden_value = true;
      }

      if (num > max_value) {
        value = max_value + '+';
      } else {
        value = '' + num;
      }
    }

    let style_name = 'badge_content';
    if (dot) {
      style_name = style_name + ' ' + 'is_dot';
    }

    return (
      <div className="comps-common-badge" style={style || {}}>
        {children}
        {!hidden_value &&
        <sup className={`${style_name}`}>
          {!dot && value}
        </sup>}
      </div>
    );
  }
}