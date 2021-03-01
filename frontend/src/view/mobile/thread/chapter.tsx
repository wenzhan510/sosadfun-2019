import * as React from 'react';
import { MobileRouteProps } from '../router';
import { NavBar } from '../../components/common/navbar';
import { Page } from '../../components/common/page';
import { APIResponse } from '../../../core/api';
import { notice } from '../../components/common/notice';
import { DB } from '../../../config/db-type';
import { Reply } from '../../components/thread/reply';
import { hasData } from '../../../utils/backend-check';
import { Button } from '../../components/common/button';
import { Reward } from '../../components/thread/reward';
import { ReadingSettings } from '../../components/thread/reading-settings';
import { loadStorage } from '../../../utils/storage';
import { ChapterList } from '../../components/thread/chapter-list';
import './chapter.scss';
import { Card } from '../../components/common/card';
import { Loading } from '../../components/common/loading';
import { CHSTransfer } from '../../../utils/chs-transfer';

interface Props extends MobileRouteProps {
}

interface State {
  data:APIResponse<'getPost'>;
  showReward:boolean;
  page:'reply'|'default'|'chapterIndex';
  showCustomize:boolean;
  loading:boolean;
}

export class Chapter extends React.Component<Props, State> {
  public state:State = {
    data: {
      thread: DB.allocThread(),
      post: DB.allocPost(),
    },
    showReward: false,
    showCustomize: false,
    page: 'default',
    loading: true,
  };

  public fetchData (cid = +this.props.match.params.cid) {
    this.props.core.api.getPost(+this.props.match.params.bid, cid)
    .then((data) => this.setState({
      data,
      loading: false,
    }))
    .catch(notice.requestError);
  }

  public componentDidMount() {
    this.fetchData();

    if (!this.props.core.state.chapterIndex.threadId) {
      this.props.core.api.getThreadProfile(+this.props.match.params.bid, {})
      .then((data) => {
        this.props.core.state.chapterIndex = {
          threadId: data.thread.id,
          chapters: data.thread.component_index_brief,
        };
        this.forceUpdate();
      })
      .catch(() => notice.error('获取章节目录失败'));
    }
  }

  public render () {
    switch (this.state.page) {
      case 'reply':
        return this.renderReply();
      case 'chapterIndex':
        return this.renderChapterIndex();
      case 'default':
      default:
        return this.renderDefault();
    }
  }

  public renderDefault () {
    const { thread, post } = this.state.data;
    const { cid, bid } = this.props.match.params;
    let annotation = '';
    if (hasData(post.info)) {
      annotation = post.info.attributes.annotation || '';
    }

    const readingSettings = loadStorage('readingSettings');

    const { chapterIndex } = this.props.core.state;
    const indexLoaded = !!chapterIndex.threadId;
    let prevIndex = 0;
    let nextIndex = 0;
    if (indexLoaded) {
      const thisIndex = findChapterIndex(chapterIndex.chapters, +cid);
      prevIndex = Math.max(thisIndex - 1, 0);
      nextIndex = thisIndex + 1;
      if (nextIndex >= chapterIndex.chapters.length) {
        nextIndex = 0;
      }
    }

    let body = post.attributes.body || '';
    if (readingSettings.fontType === 'traditionalChinese') {
      body = CHSTransfer.toTraditional(body);
    } else {
      body = CHSTransfer.toSimplified(body);
    }

    return <Page
      className="mobile-forum-chapter"
      noTopBorder
      top={<NavBar
          goBack={() => this.props.core.route.book(bid)}
          menu={<NavBar.MenuText value="个性化" onClick={() => this.setState({showCustomize: true})} />
      }>
        {thread.attributes.title}
      </NavBar>}>

      <Card>
        <div className="navigators">
          <Button disabled={!prevIndex} onClick={() => {
            this.setState({loading: true}, () => {
              const prevId = chapterIndex.chapters[prevIndex].id;
              this.props.core.route.chapter(+bid, prevId);
              this.fetchData(prevId);
            });
          }}>{indexLoaded ? prevIndex ? '上一章' : '这是第一章' : '上一章'}</Button>

          <Button onClick={() => this.setState({page: 'chapterIndex'})}>目录</Button>

          <Button disabled={!nextIndex} onClick={() => {
            this.setState({loading: true}, () => {
              const nextId = chapterIndex.chapters[nextIndex].id;
              this.props.core.route.chapter(+bid, nextId);
              this.fetchData(nextId);
            });
          }}>{indexLoaded ? nextIndex ? '下一章' : '这是最后一章' : '下一章'}</Button>
        </div>

        <Loading isLoading={this.state.loading}>
          <div className="content">
            <div className="book-title">{post.attributes.title}</div>
            <div className="body" style={{
              fontSize: readingSettings.fontSize,
            }}>{body}</div>
          </div>

          {annotation && <div className="annotation">
            <hr />
            <div className="body">{annotation}</div>
          </div>}
        </Loading>

        <div className="actions">
          <Button type="ellipse" onClick={() => this.props.core.api.collectThread(thread.id).catch(notice.requestError)}>收藏</Button>
          <Button type="ellipse" onClick={() => this.setState({page: 'reply'})}>回复</Button>
          <Button type="ellipse" onClick={() => this.setState({showReward: true})}>打赏</Button>
        </div>
      </Card>

      {this.state.showCustomize && <ReadingSettings
        onClose={() => this.setState({showCustomize: false})}
        onConfirm={(theme) => {
          this.props.core.switchTheme(theme);
          this.forceUpdate();
        }}
      />
      }

      {this.state.showReward && <Reward
        onClose={() => this.setState({showReward: false})}
        salt={99 /* todo: */}
        fish={99 /* todo: */}
        ham={99 /* todo: */}
        onReward={(type, value) => {
          this.props.core.api.addReward({
            value,
            rewardable_type: 'Post',
            rewardable_id: post.id,
            attribute: type,
          })
          .then(() => notice.success('打赏成功'))
          .catch(notice.requestError);
        }}
      />}

    </Page>;
  }

  public renderReply () {
    const { post } = this.state.data;
    return <Reply
      goBack={() => this.setState({page: 'default'})}
      submit={(body, anonymous) => {
        this.props.core.api.addPostToThread(post.id, {
          body,
          type: 'post',
          is_anonymous: anonymous || false,
          in_component_id: post.id,
          reply_to_id: post.id,
          reply_to_brief: post.attributes.brief,
        })
          .then(() => {
            this.setState({ page: 'default' });
            notice.success('发表成功');
          })
          .catch(notice.requestError);
      }}
    />;
  }

  public renderChapterIndex () {
    return <Page
    noTopBorder
    top={<NavBar goBack={() => this.setState({page: 'default'})}>
        {this.state.data.thread.attributes.title}
      </NavBar>}>

      <ChapterList
        chapters={this.props.core.state.chapterIndex.chapters}
        showFull
        goChapter={(cid) => {
          this.props.core.route.chapter(+this.props.match.params.bid, cid);
          this.setState({
            page: 'default',
            loading: true,
          });
          this.fetchData(cid);
        }}
      />

    </Page>;
  }
}

function findChapterIndex (chapters:DB.Post[], id:number) {
  for (let i = 0; i < chapters.length; i++) {
    if (chapters[i].id === id) {
      return i;
    }
  }
  return -1;
}