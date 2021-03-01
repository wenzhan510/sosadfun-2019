import * as React from 'react';
import { Quotes } from '../../components/home/quotes';
import { MobileRouteProps } from '../router';
import { Page } from '../../components/common/page';
import { MainMenu } from '../main-menu';
import { SearchBar } from '../search/search-bar';
import { RoutePath } from '../../../config/route-path';
import { ChannelPreview } from '../../components/home/channel-preview';
import { Button } from '../../components/common/button';
import { Colors } from '../../theme/theme';
import { HomeworkPreview } from '../../components/home/homework-preview';
import './main.scss';
import { APIResponse } from '../../../core/api';
import { Loading } from '../../components/common/loading';

interface State {
  data:APIResponse<'getPageHome'>;
  isLoading:boolean;
}

export class HomeMain extends React.Component<MobileRouteProps, State> {
  public state:State = {
    data:{
      quotes: [],
      recent_recommendations: [],
      homeworks: [],
      channel_threads: [
        {threads: [], channel_id: 1},
        {threads: [], channel_id: 2},
      ],
    },
    isLoading: true,
  };

  public async componentDidMount () {
    try {
      const data = await this.props.core.api.getPageHome();
      this.setState({data, isLoading: false});
    } catch (err) {
      console.error(err);
    }
  }

  public render () {
    return (<Page bottom={<MainMenu />} className="page-main">
      <SearchBar core={this.props.core} />
      <Loading isLoading={this.state.isLoading}>
        <Quotes
          quotes={this.state.data.quotes}
          core={this.props.core}
        />
        <div className="main-buttons">
          {this.renderMainButton('推荐', 'fas fa-fire', RoutePath.suggestion)}
          {this.renderMainButton('文库', 'fas fa-book-open', RoutePath.library)}
        </div>

        <ChannelPreview
          title={'编辑推荐'}
          threads={this.state.data.recent_recommendations.map((post) => ({
            id: post.id,
            author: post.info.reviewee.author.attributes.name,
            title: post.info.reviewee.attributes.title,
            brief: post.attributes.brief || '',
          }))}
          goToThread={(id) => this.props.core.route.thread(id)}
        />

        <HomeworkPreview homeworks={this.state.data.homeworks}
          onHomeworkClick={(id:number) => {/* TODO */}}
          onMoreClick={() => {/* TODO */}}
        />

        <ChannelPreview
          title={'原创榜单'}
          threads={this.state.data.channel_threads[0].threads.map((thread) => ({
            id: thread.id,
            author: thread.author.attributes.name,
            brief: thread.attributes.brief || '',
            title: thread.attributes.title,
          }))}
          goToThread={(id) => this.props.core.route.thread(id)}
        />
        <ChannelPreview
          title={'同人榜单'}
          threads={this.state.data.channel_threads[1].threads.map((thread) => ({
            id: thread.id,
            author: thread.author.attributes.name,
            brief: thread.attributes.brief || '',
            title: thread.attributes.title,
          }))}
          goToThread={(id) => this.props.core.route.thread(id)}
        />
      </Loading>
    </Page>);
  }

  public renderMainButton = (text:string, icon:string, link:RoutePath) => {
    return <Button onClick={() => this.props.core.route.go(link)}
      icon={icon}
      color={Colors.primary}
    >{text}</Button>;
  }
}