import * as React from 'react';
import { MobileRouteProps } from '../router';
import { Page } from '../../components/common/page';
import { List } from '../../components/common/list';
import { NavBar } from '../../components/common/navbar';
import './style.scss';
import { APIResponse } from '../../../core/api';
import { DB } from '../../../config/db-type';
import { VoteItem } from './vote-item';
import { Toolbar } from './toolbar';
import { notice } from '../../components/common/notice';

interface State {
  votesReceived:APIResponse<'getUserVotesReceived'>;
  votesSent:APIResponse<'getUserVotesSent'>;
  filter:filterType;
}
type filterType = 'all' | 'received' | 'sent';
const filterOptions:{text:string, value:filterType}[] = [
  {text: '全部', value: 'all'},
  {text: '收到的赞', value: 'received'},
  {text: '给出的赞', value: 'sent'},
];

// TODO: content are waiting for API fix: https://trello.com/c/bxlkk1Eb/219-api-show-user-vote%E6%B2%A1%E6%9C%89author%EF%BC%8Cattitue%E5%92%8Ccontent
// TODO: probably refactor vote and reward => write a parent class for them, a lot of dup code

export class Votes extends React.Component<MobileRouteProps, State> {
  public state:State = {
    votesReceived: {
      votes: [],
      paginate: DB.allocThreadPaginate(),
    },
    votesSent: {
      votes: [],
      paginate: DB.allocThreadPaginate(),
    },
    filter: 'all',
  };

  public async componentDidMount() {
    const { getUserVotesReceived, getUserVotesSent } = this.props.core.api;
    const fetchVotesReceived = getUserVotesReceived()
      .catch((e) => {
        notice.requestError(e);
        return this.state.votesReceived;
      });
    const fetchVotesSent = getUserVotesSent()
      .catch((e) => {
        notice.requestError(e);
        return this.state.votesSent;
      });
    const [votesReceived, votesSent] = await Promise.all([fetchVotesReceived, fetchVotesSent]);
    this.setState({votesReceived, votesSent});
  }

  public deleteVote = (voteId:number) => async () => {
    try {
      await this.props.core.api.deleteVote(voteId);
      let votesSent = this.state.votesSent;
      const votes = this.state.votesSent.votes;
      votes.splice(votes.findIndex( (r) => r.id == voteId), 1);
      this.setState({votesSent});

      // due to pagination, after we delete a vote, we have space for vote in page 2
      votesSent = await this.props.core.api.getUserVotesSent();
      this.setState({votesSent});
    } catch (e) {
      // console.log(e);
    }
  }

  public setFilterOption = (option:string, i:number) => {
    this.setState({filter:option as filterType});
  }

  public render () {
    return (<Page className="msg-page"
        top={<NavBar goBack={this.props.core.route.back}>
          点赞提醒
        </NavBar>}>

        <Toolbar
          filterOptions={filterOptions}
          setFilterOption={this.setFilterOption}
        />

        { this.renderVotes() }
      </Page>);
  }

  private getVotes() {
    const { votesReceived, votesSent, filter } = this.state;
    let selectedVotes:DB.Vote[] = [];
    switch (filter) {
      case 'all':
        selectedVotes = [...votesReceived.votes, ...votesSent.votes];
        break;
      case 'received':
        selectedVotes = votesReceived.votes;
        break;
      case 'sent':
        selectedVotes = votesSent.votes;
        break;
    }

    return selectedVotes
      .sort((r1, r2) => {
        const d1 = new Date(r1.attributes.created_at);
        const d2 = new Date(r2.attributes.created_at);
        return (d2.getTime() - d1.getTime());
      });
  }

  private renderVotes () {
    const votes = this.getVotes();
    return (
      <List className="message-list">
        {votes.map((d) =>
          <VoteItem
            key={d.id}
            read={false}
            vote={d}
            userId={this.props.core.user.id}
            deleteVote={this.deleteVote}
          />)}
      </List>);
  }
}