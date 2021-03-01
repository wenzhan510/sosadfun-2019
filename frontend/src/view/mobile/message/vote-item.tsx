
import * as React from 'react';
import { DB } from '../../../config/db-type';
import { List } from '../../components/common/list';
import ClampLines from 'react-clamp-lines';
import { Constant } from '../../../config/constant';

export function VoteItem (props:{
  read:boolean;
  vote:DB.Vote;
  userId:number;
  deleteVote:(voteId:number) => (e:React.MouseEvent) => void;
  className?:string;
}) {
  const { author, receiver, id, attributes } = props.vote;

  const authorName = (author && author.id !== props.userId) ?
    author.attributes.name : '你';
  const receiverName = (receiver && receiver.id !== props.userId) ?
    receiver.attributes.name : '你';
  const fromMe = authorName == '你';

  const {votable_type, created_at} = attributes;
  const brief = '等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API';

  const votableType = Constant.ContentType[votable_type];

  return (
    <List.Item key={id}>
      <div className="item-container">
        <div className="item-first-line">
          <span className={props.read ? 'left' : 'left unread'}>{authorName}赞了{receiverName}的{votableType}</span>
          <span className="right">
            {fromMe &&
              <span className="delete-btn" onClick={props.deleteVote(id)}>
                取消点赞
              </span>}
            {/* TODO: format Date */}
            <span>{created_at.substr(0, 10)}</span>
          </span>
        </div>
        <div className="item-brief">
          <ClampLines
          text={brief}
          id={'vote' + id}
          lines={2}
          ellipsis="..."
          buttons={false}
          innerElement="p"/>
        </div>
      </div>
    </List.Item>);
}