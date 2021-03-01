import * as React from 'react';
import { DB } from '../../../config/db-type';
import { Tag } from '../common/tag';
import './thread-preview.scss';
import { Card } from '../common/card';

// todo: mini version
interface Props {
  data:DB.Thread;
  mini?:boolean; // 精简版/非精简版
  onTagClick:(channelId:number, tagId:number) => void;
  onClick:(id:number) => void;
  onUserClick:(id:number) => void;
}
interface State {
}

export class ThreadPreview extends React.Component<Props, State> {
  public render () {
    const { attributes, id, author, tags, last_post } = this.props.data;
    const mini = this.props.mini || false; // true表示精简版， false表示非精简版

    return <Card className="thread-item" key={id} onClick={() => this.props.onClick(id)}>

    <div className="first-line">
      { !mini && tags && <span className="tags">
        {tags.map((tag, i) =>
          <Tag
            key={i}
            onClick={() => this.props.onTagClick(attributes.channel_id, tag.id)}
          >{tag.attributes.tag_name}</Tag>)
        }
      </span>}
      <div className="thread-title">
        {attributes.title}
      </div>
    </div>

    <div className="second-line">
      {attributes.brief}
    </div>

    {!mini && last_post && <div className="third-line">
      <span>{last_post.attributes.title}</span>
    </div>}

    <div className="meta">
      {/* {attributes.created_at && attributes.edited_at &&
        <span className="date">
          {parseDate(attributes.created_at)}/{parseDate(attributes.edited_at)}
        </span>
      } */}

      {
        !mini && <span className="counters">
          <span><i className="fas fa-eye"></i>{attributes.view_count}</span>
          <span><i className="fas fa-comment-alt"></i>{attributes.reply_count}</span>
        </span>
      }

      <div className="author" onClick={() => this.props.onUserClick(author.id)}>{author.attributes.name}</div>
    </div>
  </Card>;
  }
}