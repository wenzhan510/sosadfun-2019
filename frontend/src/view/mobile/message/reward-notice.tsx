import * as React from 'react';
import { DB } from '../../../config/db-type';
import { MobileRouteProps } from '../router';
import { Page } from '../../components/common/page';
import { NavBar } from '../../components/common/navbar';
import { List } from '../../components/common/list';
import { Toolbar } from './toolbar';
import { RewardItem } from './reward-item';
import { APIResponse } from '../../../core/api';
import { notice } from '../../components/common/notice';

interface State {
  rewardsReceived:APIResponse<'getUserRewardsReceived'>;
  rewardsSent:APIResponse<'getUserRewardsSent'>;
  filter:filterType;
}

type filterType = 'all' | 'received' | 'sent';
const filterOptions:{text:string, value:filterType}[] = [
  {text: '全部', value: 'all'},
  {text: '收到的打赏', value: 'received'},
  {text: '给出的打赏', value: 'sent'},
];

export class RewardNotice extends React.Component<MobileRouteProps, State> {
  public state:State = {
    rewardsReceived: {
      rewards: [],
      paginate: DB.allocThreadPaginate(),
    },
    rewardsSent: {
      rewards: [],
      paginate: DB.allocThreadPaginate(),
    },
    filter: 'all',
  };

  public async componentDidMount() {
    const { getUserRewardsReceived, getUserRewardsSent } = this.props.core.api;
    const fetchRewardsReceived = getUserRewardsReceived()
      .catch((e) => {
        notice.requestError(e);
        return this.state.rewardsReceived;
      });
    const fetchRewardsSent = getUserRewardsSent()
      .catch((e) => {
        notice.requestError(e);
        return this.state.rewardsSent;
      });
    const [rewardsReceived, rewardsSent] = await Promise.all([fetchRewardsReceived, fetchRewardsSent]);
    this.setState({rewardsReceived, rewardsSent});
  }

  public deleteReward = (rewardId:number) => async () => {
    try {
      await this.props.core.api.deleteReward(rewardId);
      let rewardsSent = this.state.rewardsSent;
      const rewards = rewardsSent.rewards;
      rewards.splice(rewards.findIndex( (r) => r.id == rewardId), 1);
      this.setState({rewardsSent});

      // due to pagination, after we delete a reward, we have space for reward in page 2
      rewardsSent = await this.props.core.api.getUserRewardsSent();
      this.setState({rewardsSent});
    } catch (e) {
      notice.requestError(e);
    }
  }

  public setFilterOption = (option:string, i:number) => {
    this.setState({filter:option as filterType});
  }

  public render () {
    return (<Page className="msg-page"
        top={<NavBar goBack={this.props.core.route.back}>
          打赏提醒
        </NavBar>}>

        <Toolbar
          filterOptions={filterOptions}
          setFilterOption={this.setFilterOption}
        />

        { this.renderRewards() }
      </Page>);
  }

  private getRewards() {
    const { rewardsReceived, rewardsSent, filter } = this.state;
    let selectedRewards:DB.Reward[] = [];
    switch (filter) {
      case 'all':
        selectedRewards = [...rewardsReceived.rewards, ...rewardsSent.rewards];
        break;
      case 'received':
        selectedRewards = rewardsReceived.rewards;
        break;
      case 'sent':
        selectedRewards = rewardsSent.rewards;
        break;
    }

    return selectedRewards
      .sort((r1, r2) => {
        const d1 = new Date(r1.attributes.created_at);
        const d2 = new Date(r2.attributes.created_at);
        return (d2.getTime() - d1.getTime());
      });
  }

  private renderRewards () {
    const rewards = this.getRewards();
    return (
      <List className="message-list">
        {rewards.map((d) =>
          <RewardItem
            key={d.id}
            read={false}
            reward={d}
            userId={this.props.core.user.id}
            deleteReward={this.deleteReward}
          />)}
      </List>);
  }
}