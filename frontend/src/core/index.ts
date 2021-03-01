import { API } from './api';
import { User } from './user';
import { History, UnregisterCallback, createBrowserHistory } from 'history';
import { EventBus } from '../utils/events';
import * as _ from 'lodash/core';
import { TagFilter, ChannelFilter, BianyuanFilter } from './filter-handler';
import { Route } from './route';
import { saveStorage, FontType } from '../utils/storage';
import { Themes } from '../view/theme/theme';
import { updateNoticeTheme } from '../view/components/common/notice';
import { DB } from '../config/db-type';
import { FAQCache, ChannelsCache } from './cache-handler';
const debounce = require('lodash/debounce');

export type Filters = {
  tag:TagFilter,
  channel:ChannelFilter,
  bianyuan:BianyuanFilter,
};
export type Cache = {
  FAQ:FAQCache,
  channels:ChannelsCache,
};

interface State {
  chapterIndex:{
    threadId:number;
    chapters:DB.Post[];
  };
}

export class Core {
  public api:API;
  public user:User;
  public history:History;
  public unlistenHistory:UnregisterCallback;
  public windowResizeEvent:EventBus<void>;
  public route:Route;
  public filter:Filters;
  public cache:Cache;

  // data used cross components
  public state:State = {
    chapterIndex: {
      threadId: 0,
      chapters: [],
    },
  };

  constructor () {
    this.history = createBrowserHistory();
    this.unlistenHistory = this.history.listen((location, action) => {
    });

    this.user = new User(this.history);
    this.api = new API(this.user, this.history);
    this.filter = {
      tag: new TagFilter(this.api),
      channel: new ChannelFilter(this.api),
      bianyuan: new BianyuanFilter(this.api),
    };
    this.cache = {
      FAQ: new FAQCache(this.api),
      channels: new ChannelsCache(this.api),
    };
    this.route = new Route(this.history);
    this.windowResizeEvent = new EventBus();
    window.addEventListener('resize', debounce(() => {
      this.windowResizeEvent.notify(undefined);
    }));
  }

  public async init () {
    await this.filter.tag.init();
    await this.filter.channel.init();
    await this.filter.bianyuan.init();
  }

  public switchTheme (theme:Themes) {
    updateNoticeTheme(theme);
    const appElement = document.getElementById('app');
    if (appElement) {
      appElement.setAttribute('class', `theme-${theme}`);
      appElement.setAttribute('data-theme', theme);
      saveStorage('theme', theme);
    }
  }
}