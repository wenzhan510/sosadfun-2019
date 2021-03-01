import * as React from 'react';
import { MobileRouteProps } from '../router';
import { Page } from '../../components/common/page';
import { MainMenu } from '../main-menu';
import { SearchBar } from '../search/search-bar';
import { Card } from '../../components/common/card';
import { DB } from '../../../config/db-type';
import { Loading } from '../../components/common/loading';
import { ThreadPreview } from '../../components/thread/thread-preview';
import { APIResponse } from '../../../core/api';
import { notice } from '../../components/common/notice';
import './home.scss';
import { PublishThread } from '../../components/thread/publish-thread';
import { PublishThreadButton } from '../../components/thread/publish-button';

interface State {
  data:APIResponse<'getThreadHome'>;
  channels:APIResponse<'getAllChannels'>;
  isLoading:boolean;
  page:'default'|'createPost';
}

export class ThreadHome extends React.Component<MobileRouteProps, State> {
  public state:State = {
    data: {
      simple_threads: [],
      threads: [],
      pagination: DB.allocThreadPaginate(),
    },
    channels: {},
    isLoading: true,
    page: 'default',
  };

  public channelIndex:{id:number, rename?:string}[] = [
    {id: 4},
    {id: 5},
    {id: 6},
    {id: 14, rename: '清单评鉴'},
    {id: 0, rename: '废文公务'},
  ];

  public render () {
    switch (this.state.page) {
      case 'createPost':
        return <PublishThread
          type={'thread'}
          onCancel={() => this.setState({page: 'default'})}
          onSubmit={(spec) => this.props.core.api.publishThread(spec)
            .then((thread) => {
              notice.success('发布成功');
              this.props.core.route.thread(thread.id);
            })
            .catch((e) => notice.requestError(e))
          }
        />;
      case 'default':
      default:
        return this.renderDefault();
    }
  }

  public renderDefault () {
    console.log(this.state);
    return <Page bottom={<MainMenu />} className="mobile-thread-index">
      <SearchBar core={this.props.core} />

      <Loading isLoading={this.state.isLoading}>
        <Card>
          <div className="banner">
            {/* todo: fit this.state.data.simple_threads here */}
          </div>

          <div className="channel-index">
            {this.channelIndex.map((channel) => {
              const channelData = this.state.channels[channel.id];
              const channelName = channel.rename || (channelData ? channelData.attributes.channel_name : '');
              return <div className="item" key={channel.id} onClick={() => this.props.core.route.channel(channel.id)}>
                <div className="logo">{channelName.charAt(0)}</div>
                <div className="text">{channelName}</div>
              </div>;
            })}
          </div>
        </Card>

        {this.state.data.threads.map((thread) => <ThreadPreview
          key={thread.id}
          data={thread}
          onTagClick={(channelId, tagId) => { /** todo: */}}
          onClick={(id) => this.props.core.route.thread(id)}
          onUserClick={(id) => this.props.core.route.user(id)}
        />)}
      </Loading>

      <PublishThreadButton onClick={() => this.setState({page: 'createPost'})} />
    </Page>;
  }

  public fetchData = async () => {
    try {
      const data = await this.props.core.api.getThreadHome();
      this.setState({
        data,
        isLoading: false,
      });
    } catch (e) {
      notice.requestError(e);
    }
  }

  public async componentDidMount () {
    const channels = await this.props.core.cache.channels.get();
    this.setState({channels});
    await this.fetchData();
  }
}