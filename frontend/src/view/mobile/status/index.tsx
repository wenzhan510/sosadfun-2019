import * as React from 'react';
import { MobileRouteProps } from '../router';
import { List } from '../../components/common/list';
import  './status.scss';
import { Page } from '../../components/common/page';
import { MainMenu } from '../main-menu';
import { SearchBar } from '../search/search-bar';
import { TextEditor } from '../../components/common/textEditor';
import { Button } from '../../components/common/button';
import { Colors } from '../../theme/theme';
import { APIResponse } from '../../../core/api';
import { DB } from '../../../config/db-type';
import { notice } from '../../components/common/notice';
import { Loading } from '../../components/common/loading';
import { bbcode2html } from '../../../utils/text-formater';

interface State {
  allStatuses:APIResponse<'getStatuses'>;
  followStatuses:APIResponse<'getFollowStatuses'>;
  isAll:boolean;
  publishDisabled:boolean;
  isLoading:boolean;
}
// TODO: pagination, next page, cache
export class Status extends React.Component<MobileRouteProps, State> {
  public state:State = {
    allStatuses: {
      statuses: [],
      paginate: DB.allocThreadPaginate(),
    },
    followStatuses: {
      statuses: [],
      paginate: DB.allocThreadPaginate(),
    },
    isAll: true,
    publishDisabled: true,
    isLoading: true,
};
private textEditorRef = React.createRef<TextEditor>();

public async componentDidMount() {
  await this.fetchData();
}

public async fetchData(isLoading=true) {
  this.setState({isLoading});
  try {
    if (this.state.isAll) {
      const allStatuses = await this.props.core.api.getStatuses();
      this.setState({allStatuses, isLoading:false});
    } else {
      const followStatuses = await this.props.core.api.getFollowStatuses();
      this.setState({followStatuses, isLoading:false});
    }
  } catch (e) {
    notice.requestError(e);
  }
}

private getPushlishDisabled = () => {
  const ref = this.textEditorRef.current;
  const publishDisabled = !ref || ref.state.text.length == 0
      || ref.state.text == '<p><br></p>';
  // FIXME: empty state of text editor is p br p sometimes.
  if (publishDisabled != this.state.publishDisabled) {
      this.setState({publishDisabled});
  }
}

public publishStatue = async () => {
  const ref = this.textEditorRef.current;
  if (!ref) { return; }
  const content = ref.getContent();
  try {
    const { status } = await this.props.core.api.postStatue(content);
    const allStatuses = this.state.allStatuses;
    allStatuses.statuses = [ status, ...allStatuses.statuses ];
    this.setState({allStatuses});
  } catch (e) {
    notice.requestError(e);
  }
}

public setFilter = (isAll:boolean) => () => {
  const { allStatuses, followStatuses } = this.state;

  if (isAll == this.state.isAll) {
    return;
  } else {
    const statuses = isAll ? allStatuses.statuses : followStatuses.statuses;
    const hasLoadedBefore = statuses.length > 0;
    this.setState({isAll}, async () => {
      await this.fetchData(!hasLoadedBefore);
    });
  }
}

public render () {
  const { publishDisabled } = this.state;
  return (
      <Page bottom={<MainMenu />} className="mobile-status">
        <SearchBar core={this.props.core} />
        <div id="compose-status">
          <TextEditor
            onChange={this.getPushlishDisabled}
            ref={this.textEditorRef}
            placeholder="今天你丧了吗…"/>
          <div className="publish-btn">
            <Button
              size="small"
              disabled={publishDisabled}
              color={Colors.light}
              inline={true}
              onClick={this.publishStatue}>发布</Button>
          </div>
        </div>
        <div className="content">
          <div className="tiddings-tabs">
            <button className={ this.state.isAll ? 'tab-btn tab-btn-active' : 'tab-btn' }
              onClick={this.setFilter(true)}>
                全部
            </button>
            <button className={ !this.state.isAll ? 'tab-btn tab-btn-active' : 'tab-btn' }
              onClick={this.setFilter(false)}>
                关注
            </button>
          </div>
          <Loading isLoading={this.state.isLoading}>
            <List> {this.renderList()} </List>
          </Loading>
        </div>
      </Page>
    );
  }

  private getFilteredStatuses() {
    const { isAll, allStatuses, followStatuses } = this.state;
    if (isAll) {
      return allStatuses.statuses;
    } else {
      return followStatuses.statuses;
    }
  }

  // 根据获取的动态信息渲染列表
  public renderList () {
    const statuses = this.getFilteredStatuses();
    const renderItem = (statue) => {
      const htmlContent = bbcode2html(statue.attributes.body);
      return (
        <List.Item key={ statue.id } className="status-item">
          <div className="status-author">
            <span>{ statue.author.attributes.name }</span>
            <span>{ statue.attributes.created_at }</span>
          </div>
          <div className="status-content" dangerouslySetInnerHTML={{__html:htmlContent}} />
        </List.Item>);
    }
    return statuses.map((statue) => renderItem(statue));
  }
}