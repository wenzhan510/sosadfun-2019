
import * as React from 'react';
import { DB } from '../../../config/db-type';
import { List } from '../../components/common/list';
import ClampLines from 'react-clamp-lines';
import { Constant } from '../../../config/constant';

export function RewardItem (props:{
  read:boolean;
  reward:DB.Reward;
  userId:number;
  deleteReward:(rewardId:number) => (e:React.MouseEvent) => void;
  className?:string;
}) {
  const { author, receiver, id, attributes } = props.reward;

  const authorName = (author && author.id !== props.userId) ?
    author.attributes.name : '你';
  const receiverName = (receiver && receiver.id !== props.userId) ?
    receiver.attributes.name : '你';
  const fromMe = authorName == '你';

  const {rewardable_type, reward_type, reward_value, created_at} = attributes;
  const brief = '等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API等待API';

  const rewardType = Constant.Reward[reward_type];
  const rewardableType = Constant.ContentType[rewardable_type];

  return (
    <List.Item key={id}>
      <div className="item-container">
        <div className="item-first-line">
          <span className={props.read ? 'left' : 'left unread'}>{authorName}打赏了{receiverName}的{rewardableType}{reward_value}{rewardType}</span>
          <span className="right">
            {fromMe &&
              <span className="delete-btn" onClick={props.deleteReward(id)}>
                删除打赏
              </span>}
            {/* TODO: format Date */}
            <span>{created_at.substr(0, 10)}</span>
          </span>
        </div>
        <div className="item-brief">
          <ClampLines
          text={brief}
          id={'reward' + id}
          lines={2}
          ellipsis="..."
          buttons={false}
          innerElement="p"/>
        </div>
      </div>
    </List.Item>);
}