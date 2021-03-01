import * as React from 'react';
import { Animate } from './animate';
import { List } from './list';
import './popup-menu.scss';

type ListItem = {title:string, onClick:() => void};
interface Props {
  list:ListItem[];
  onClose:() => void;
}
interface State {
  onClosing:boolean;
}

export class PopupMenu extends React.Component<Props, State> {
  public readonly timeout = 500;
  public state:State = {
    onClosing: false,
  };
  public render () {
    const name = this.state.onClosing ? 'slideOutDown' : 'slideInUp';
    const listStyle:React.CSSProperties = {
      backgroundColor: 'rgba(0, 0, 0, 0)',
    };
    const listItemStyle:React.CSSProperties = {
      color: '#3679e8',
      padding: '12px 0',
    };
    return <div className="popupMenu-wrapper">
      <div className="background" onClick={this.onClose}></div>
      <Animate name={name} className="animate-wrapper" speed="faster">
        <div className="panel">
          <List noBorder style={listStyle}>
            {this.props.list.map((item, i) => <List.Item
              style={listItemStyle}
              key={i}
              onClick={item.onClick}>
              {item.title}
            </List.Item>)}
          </List>
        </div>
        <div className="panel" onClick={this.onClose}>
          <List noBorder style={listStyle}>
            <List.Item style={listItemStyle}>取消</List.Item>
          </List>
        </div>
      </Animate>
    </div>;
  }
  public onClose = () => {
    this.setState({ onClosing: true }, () => requestAnimationFrame(this.waitClose));
  }

  public tick = 0;
  public waitClose = () => {
    this.tick += 1000 / 60;
    if (this.tick >= this.timeout) {
      this.props.onClose();
      return;
    }
    requestAnimationFrame(this.waitClose);
  }
}