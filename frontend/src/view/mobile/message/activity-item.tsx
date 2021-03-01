
import * as React from 'react';
import { DB } from '../../../config/db-type';
import { List } from '../../components/common/list';
import ClampLines from 'react-clamp-lines';

export function ActivityItem (props:{
  read:boolean;
  activity:DB.Activity;
  className?:string;
}) {
  const { title, brief } = getTitle();

  function getTitle() {
    const activity = props.activity;
    const itemType = activity.attributes.item_type;
    const authorName = activity.author ? activity.author.attributes.name : '有人';

    if (itemType != 'post') {
      return {
        title: '',
        brief: '',
      };
    }

    const post = activity.item as DB.Post;
    const threadName = post.thread ? post.thread.attributes.title : '未知主题';
    return {
          title: `${authorName}回复了你的主题《${threadName}》`,
          brief: post.attributes.brief || '',
        };
    // TODO: support 'status', 'quote', 'thread'
  }
  return (
    <List.Item>
      <div className="item-container">
        <div className="item-first-line">
          <div className={props.read ? '' : 'unread'}>{title}</div>
        </div>
        <div className="item-brief">
          <ClampLines
            text={brief}
            id={'text' + props.activity.id}
            lines={2}
            ellipsis="..."
            buttons={false}
            innerElement="p"/>
        </div>
      </div>
    </List.Item>);
}