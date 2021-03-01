import * as React from 'react';
import { Link } from 'react-router-dom';
import { classnames } from '../../../utils/classname';
import './route-menu.scss';

type MenuItem = {
  to:string;
  label:string;
  icon?:string;
  defaultColor?:string;
  selectedColor?:string;
};

interface Props {
  items:MenuItem[];
  style?:React.CSSProperties;
  onIndex?:number;
  onClick?:(item:MenuItem, index:number) => void;
  className?:string;
}
interface State {
}

export class RouteMenu extends React.Component<Props, State> {
  public render () {
    return <div className={classnames('route-menu', this.props.className)} style={this.props.style}>
      {this.props.items.map((item, i) => {
        const selected = i === this.props.onIndex;
        const selectedCln = selected ? 'selected' : '';

        return <Link className={classnames('item', selectedCln)}
          key={item.to + item.label + selected}
          to={item.to}
          onClick={this.props.onClick ? () => this.props.onClick!(item, i) : undefined}
          >

          {item.icon && <i className={classnames(item.icon, {'selected': selected})}></i>}

          <div className={classnames(selectedCln, item.icon ? 'icon-text' : '')}>{item.label}</div>
        </Link>;
      })}
    </div>;
  }
}