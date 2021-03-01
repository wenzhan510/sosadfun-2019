import * as React from 'react';
import { List } from '../common/list';
import './channel-preview.scss';

type Thread = {
  id:number;
  author:string;
  title:string;
  brief:string;
};

interface Props {
  title:string;
  threads:Thread[];
  goToChannel?:() => void;
  goToThread:(id:number) => void;
}
interface State {
}

export class ChannelPreview extends React.Component<Props, State> {
  public render() {
    return <div className="channel-preview">
      <div className="title" onClick={this.props.goToChannel}>
        {this.props.title}
      </div>

      <List className="list">
        {this.props.threads.map((thread, index) => <List.Item
          key={thread.id}
          onClick={() => this.props.goToThread(thread.id)}
        >
          <div className="item-container">
            <div className="item-first-line">
              <div className="item-title">{thread.title}</div>
              <div className="item-author">{thread.author}</div>
            </div>
            <div className="item-brief">{thread.brief}</div>
          </div>
        </List.Item>)}
      </List>
    </div>;
  }
}