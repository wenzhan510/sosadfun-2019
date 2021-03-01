import React from 'react';
import { Card } from '../common/card';
import { classnames } from '../../../utils/classname';
import { List } from '../common/list';
import { TagList } from '../common/tag-list';
import { Tag } from '../common/tag';
import { Core, Filters } from '../../../core';
import { RoutePath } from '../../../config/route-path';
import { Button } from '../common/button';
import { Colors } from '../../theme/theme';
import './forum-menu.scss';
import { RequestFilter } from '../../../config/request-filter';

enum MenuList {
  none,
  sort,
  filter,
}

const ordered = RequestFilter.thread.ordered;
type ordered = RequestFilter.thread.ordered;
type Filter = {
  id:number;
  name:string;
};

interface State {
  onList:MenuList;
}

enum FilterType {
  tags,
  channels,
  bianyuan,
}

export class ForumMenu extends React.PureComponent<{
  // props
  core:Core;
  selectedSort:RequestFilter.thread.ordered;
  applySort:(sort:RequestFilter.thread.ordered) => void;
  applyFilter:() => void;
}, State> {
  public state = {
    onList:MenuList.none,
  };

  public static SortText:{[name in ordered]:string} = {
    [ordered.latest_add_component]: '最新章节',
    [ordered.jifen]: '总积分',
    [ordered.weighted_jifen]: '均字数积分',
    [ordered.latest_created]: '最新创建',
    [ordered.default]: '最新回复',
    [ordered.total_char]: '总字数',
    [ordered.collection_count]: '收藏量',
    [ordered.random]: '随机',
  };

  public displaySorts:ordered[] = [
    ordered.latest_add_component,
    ordered.default,
    ordered.jifen,
    ordered.weighted_jifen,
  ];

  public static displayFilterTags:{[type:string]:Filter[]} = {
    '篇幅': [
      {name: '短篇', id: 77},
      {name: '中篇', id: 78},
      {name: '长篇', id: 79},
      {name: '大纲', id: 80},
    ],
    '进度': [
      {name: '连载', id: 81},
      {name: '完结', id: 82},
      {name: '暂停', id: 83},
    ],
    '性向': [
      {name: '未知', id: 1},
      {name: 'BL', id: 84},
      {name: 'GL', id: 85},
      {name: 'BG', id: 86},
      {name: 'GB', id: 87},
      {name: '混合', id: 88},
      {name: '无CP', id: 89},
      {name: '其他', id: 90},
    ],
  };

  public static displayFilterChannels:Filter[] = [
    {id: 1, name: '原创'},
    {id: 2, name: '同人'},
  ];

  public render () {
    return <Card className="components-thread-forum-menu">
      <div className="menu">
        <div className="left">
          {this.renderDropdownTitle('排序', MenuList.sort)}
          {this.renderDropdownTitle('筛选', MenuList.filter)}
        </div>
        <div className="right">
          <div className="item" onClick={() => this.props.core.route.go(RoutePath.tags)}>标签列表</div>
        </div>
      </div>

      <div className="content" style={{display: this.state.onList === MenuList.none ? 'none' : undefined}}>
        {this.state.onList === MenuList.sort
          ? this.renderSort()
          : this.renderFilter()
        }
      </div>
    </Card>;
  }

  public renderSort = () => {
    return <List noBorder>
      {this.displaySorts.map((sortType, i) => <List.Item key={i}
        className={classnames({selected: sortType === this.props.selectedSort})}
        noBorder
        onClick={() => this.props.applySort(ordered[sortType])}>
        {ForumMenu.SortText[sortType]}
      </List.Item>)}
    </List>;
  }

  public renderFilter = () => {
    return <List noBorder>
      {this.renderFilterBlock('文章分类', ForumMenu.displayFilterChannels, 'channel')}
      {this.renderFilterBlock('篇幅', ForumMenu.displayFilterTags['篇幅'], 'tag')}
      {this.renderFilterBlock('进度', ForumMenu.displayFilterTags['进度'], 'tag')}
      {this.renderFilterBlock('性向', ForumMenu.displayFilterTags['性向'], 'tag')}
      {this.renderFilterBlock('边限', [{id: 0, name: '非边限'}, {id: 1, name: '边限'}], 'bianyuan')}
      <List.Item noBorder className="title" onClick={this.props.applyFilter}>
        <Button onClick={this.props.applyFilter} color={Colors.primary}>提交</Button>
      </List.Item>
    </List>;
  }

  public renderDropdownTitle = (text:string, list:MenuList) => {
    const selected = this.state.onList === list;
    return <div className={classnames('item', {selected})}
      onClick={() => this.setState((prevState) => ({onList: prevState.onList === list ? MenuList.none : list}))}
    >
      {text}
      <i className={`fa fa-angle-${selected ? 'up' : 'down'}`}></i>
    </div>;
  }

  public renderFilterBlock = (title:string, tags:Filter[], filterName:keyof Filters) => {
    const filterHandler = this.props.core.filter[filterName];
    return <div className="filter-block">
      <div className="title">{title}</div>
      <TagList>
        {tags.map((tag) => <Tag
          key={tag.id}
          onClick={() => {
            filterHandler.select(tag.id);
            console.log(filterHandler.getSelectedList());
            this.forceUpdate();
          }}
          selected={filterHandler.isSelected(tag.id)}
        >
          {tag.name}
        </Tag>)}
      </TagList>
    </div>;
  }
}