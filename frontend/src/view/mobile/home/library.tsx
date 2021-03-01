import * as React from 'react';
import { Page } from '../../components/common/page';
import { NavBar } from '../../components/common/navbar';
import { RoutePath } from '../../../config/route-path';
import { ForumMenu } from '../../components/thread/forum-menu';
import { DB } from '../../../config/db-type';
import { MobileRouteProps } from '../router';
import { ThreadPreview } from '../../components/thread/thread-preview';
import { APIResponse } from '../../../core/api';
import { Loading } from '../../components/common/loading';
import { RequestFilter } from '../../../config/request-filter';

interface State {
  data:APIResponse<'getBooks'>;
  onPage:number;
  ordered:RequestFilter.thread.ordered;
  isLoading:boolean;
}

export class Library extends React.Component<MobileRouteProps, State> {
  public state:State = {
    data: {
      threads: [],
      paginate: DB.allocThreadPaginate(),
    },
    onPage: 1,
    ordered: RequestFilter.thread.ordered.default,
    isLoading: true,
  };

  public componentDidMount() {
    this.fetchData();
  }

  public fetchData () {
    const { tag, channel, bianyuan } = this.props.core.filter;
    this.props.core.api.getBooks({
      page: this.state.onPage,
      channel: channel.getSelectedList(),
      withTag: [tag.getSelectedList()],
      withBianyuan: bianyuan.isSelected(1),
      ordered: this.state.ordered,
    })
    .then((data) => this.setState({data, isLoading: false}))
    .catch(console.error);
  }

  public render () {
    return <Page top={<NavBar
      goBack={() => this.props.core.route.go(RoutePath.home)}
      menu={NavBar.MenuIcon({
        onClick: () => this.props.core.route.go(RoutePath.search),
        icon: 'fa fa-search',
      })}
    >文库</NavBar>}>
      <ForumMenu
        core={this.props.core}
        selectedSort={RequestFilter.thread.ordered.default}
        applySort={(ordered) => {
          this.setState({ordered});
          this.fetchData();
        }}
        applyFilter={() => this.fetchData()}
      />

      <Loading isLoading={this.state.isLoading}>
        {this.state.data.threads.map((thread) => <ThreadPreview
          key={thread.id}
          data={thread}
          onTagClick={(channelId, tagId) => {}}
          onClick={(id) => this.props.core.route.book(id)}
          onUserClick={(id) => this.props.core.route.user(id)}
        />)}
      </Loading>
      }
    </Page>;
  }
}