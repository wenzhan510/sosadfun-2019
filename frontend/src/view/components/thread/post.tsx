import * as React from 'react';
import { DB } from '../../../config/db-type';
import { Card } from '../common/card';
import { RequestFilter } from '../../../config/request-filter';

interface Props {
  data:DB.Post;
  isAuthor:boolean;
  onVote:(attitude:DB.VoteAttribute) => void; //vote: true=点赞, false=移除点赞
  onReply:() => void;
}

interface State {
}

export class Post extends React.Component<Props, State> {
  public render () {
    return <Card>

      {/* remove follow lines */}
      <button onClick={() => this.props.onVote('upvote')}>vote</button>
      <button onClick={() => this.props.onVote('upvote')}>unVote</button>
      <button onClick={() => this.props.onReply()}>reply</button>

    </Card>;
  }
}