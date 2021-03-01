import React from 'react';
import { Page } from '../../components/common/page';
import { MobileRouteProps } from '../router';
import { NavBar } from '../../components/common/navbar';
import { PublishThreadButton } from '../../components/thread/publish-button';
import { PublishThread } from '../../components/thread/publish-thread';
import { notice } from '../../components/common/notice';
import { RequestFilter, RequestFilterText } from '../../../config/request-filter';
import { Loading } from '../../components/common/loading';
import { APIResponse } from '../../../core/api';
import { DB } from '../../../config/db-type';
import { Slider } from '../../components/common/slider';
import { Card } from '../../components/common/card';
import { List } from '../../components/common/list';
import { ThreadPreview } from '../../components/thread/thread-preview';
import { PopupMenu } from '../../components/common/popup-menu';
import './channel.scss';
import { classnames } from '../../../utils/classname';

interface Props extends MobileRouteProps {
}

interface State {
  page:'default'|'createPost';
  channelId:number;
  isLoading:boolean;
  data:APIResponse<'getChannel'>;
  threads:DB.Thread[];
  ordered:RequestFilter.thread.ordered;
  showOrderSelector:boolean;
  onSelectTag:number;
  withBianyuan:boolean;
}

export class Channel extends React.Component <Props, State> {
  public state:State = {
    page: 'default',
    isLoading: true,
    channelId: +this.props.match.params.id,
    data: {
      channel: DB.allocChannelBrief(),
      threads: [],
      primary_tags: [],
      request_data: [],
      simplethreads: [],
      paginate: DB.allocThreadPaginate(),
    },
    threads: [],
    ordered: RequestFilter.thread.ordered.default,
    showOrderSelector: false,
    onSelectTag: +(this.props.match.params.tagId || 0),
    withBianyuan: false,
  };

  public async fetchData (query:Partial<State> = {}) {
    try {
      const channelId = this.state.channelId;
      const ordered = query.ordered || this.state.ordered;
      const withBianyuan = query.withBianyuan || this.state.withBianyuan;
      const onSelectTag = query.onSelectTag === undefined ? this.state.onSelectTag : query.onSelectTag;
      if (onSelectTag !== this.state.onSelectTag) {
        this.props.core.route.channel(channelId, onSelectTag);
      }
      let data = this.state.data;
      let threads = this.state.threads;
      if (!channelId) {
        const primary_tags = [{id: 7, tag_name: '站务管理'}, {id: 8, tag_name: '违规举报'}, {id: 9, tag_name: '投诉仲裁'}];
        const res = await this.props.core.api.getThreadList({
          channels: primary_tags.map((channel) => channel.id),
          withBianyuan,
          ordered,
        });
        threads = res.thread;
        data.paginate = res.paginate;
        data.primary_tags = primary_tags;
        data.channel.channel_name = '废文公务';
      } else if (onSelectTag === 0) {
        const res = await this.props.core.api.getChannel(channelId, {
          isPublic: RequestFilter.thread.isPublic.include_private,
          ordered,
          withBianyuan,
        });
        data = res;
        threads = res.threads;
      } else {
        const res = await this.props.core.api.getThreadList({
          channels: [channelId],
          tags: [onSelectTag],
          withBianyuan,
          ordered,
        });
        threads = res.thread;
        data.paginate = res.paginate;
      }
      this.setState({
        isLoading: false,
        ordered,
        withBianyuan,
        onSelectTag,
        data,
        threads,
      });
    } catch (e) {
      notice.requestError(e);
      this.setState({isLoading: false});
    }
  }

  public async componentDidMount () {
    await this.fetchData();
  }

  public render() {
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
  public renderDefault() {
    return <Page className="mobile-thread-channel"
      top={
        <NavBar goBack = {
          this.props.core.route.back
        }>{this.state.data.channel.channel_name}</NavBar>}>

      <PublishThreadButton onClick={() => this.setState({ page: 'createPost'})} />

      <Loading isLoading={this.state.isLoading}>
        <div className="top-tags">
          <Slider>
            <div className={classnames({'selected': this.state.onSelectTag === 0})}
              onClick={() => this.setState({isLoading: true}, () => this.fetchData({onSelectTag: 0}))}>
              全部
            </div>
            {this.state.data.primary_tags.map((tag) => {
              return <div
                key={tag.id}
                className={classnames({'selected': this.state.onSelectTag === tag.id})}
                onClick={() => this.setState({isLoading: true}, () => this.fetchData({onSelectTag: tag.id}))}>
                {tag.tag_name}
              </div>;
            })}
          </Slider>
          <div className="blur"></div>
        </div>

        {!!this.state.data.simplethreads.length &&
        <Card className="top-threads">
          <List>
            {this.state.data.simplethreads.map((thread) =>
            <List.Item key={thread.id} onClick={() => this.props.core.route.thread(thread.id)}>
              <div className="tag">置顶</div>
              <div className="text">{thread.attributes.title}</div>
            </List.Item>)}
          </List>
        </Card>
        }

        <Card className="filter-menu" style={{marginBottom: '1px'}}>
          <div onClick={() => this.setState({showOrderSelector: true})}>排序 <i className="fa fa-angle-down"></i></div>
          <div>显示边限</div>
        </Card>
        {this.state.threads.map((thread) =>
        <ThreadPreview
          key={thread.id}
          data={thread}
          onTagClick={(channelId, tagId) => this.props.core.route.channelTag(channelId, tagId)}
          onClick={(id) => this.props.core.route.thread(id)}
          onUserClick={(id) => this.props.core.route.user(id)}
        />,
        )}
      </Loading>

      {this.state.showOrderSelector && <PopupMenu
        list={Object.keys(RequestFilterText.thread.ordered).map((key) => {
          return {
            title: RequestFilterText.thread.ordered[key],
            onClick: () => this.setState(
              {
                isLoading:true,
                showOrderSelector: false,
              },
              () => this.fetchData({
                ordered: key as RequestFilter.thread.ordered,
              })),
          };
        })}
        onClose={() => this.setState({ showOrderSelector: false })}
      />}
    </Page>;
  }
}