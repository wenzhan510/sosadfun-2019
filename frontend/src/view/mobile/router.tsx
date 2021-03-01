import * as React from 'react';
import { Switch, Route, RouteComponentProps } from 'react-router-dom';
import { Core } from '../../core';
import { User } from './user';
import { Status } from './status';
import { LoginRoute } from './user/login';
import { HomeMain } from './home/main';
import { Chapter } from './thread/chapter';
import { CreateQuote } from './home/createquote';

import { RoutePath } from '../../config/route-path';
import { ThreadHome } from './thread/home';
import { SearchPage } from './search/search-page';
import { Suggestion } from './home/suggestion';
import { Library } from './home/library';
import { Collection } from './collection';
import { ForumTags } from '../components/thread/forum-tags';

// msg system
import { Message } from './message';
import { PersonalMessage } from './message/personal-msg';
import { Dialogue } from './message/dialogue';
import { PublicNotice } from './message/public-notice';
import { Votes } from './message/votes';
import { Book } from './thread/book';
import { RewardNotice } from './message/reward-notice';

// my
import { FAQMenu } from './faq/faq-menu';
import { FAQContent } from './faq/faq-content';
import { Register } from './user/register/register';
import { Thread } from './thread/thread';
import { Channel } from './thread/channel';

interface Props {
  core:Core;
}

interface State {

}

export interface MobileRouteProps extends RouteComponentProps<any> {
  core:Core;
  path:string;
}

export const MobileRoutes = {
  // home
  [RoutePath.home]: HomeMain,
  [RoutePath.createQuote]: CreateQuote,
  [RoutePath.suggestion]: Suggestion,
  [RoutePath.library]: Library,
  [RoutePath.chapter]: Chapter,
  [RoutePath.book]: Book,
  // '/thread/:id': Thread,

  // forum
  [RoutePath.threadHome]: ThreadHome,
  [RoutePath.thread]: Thread,
  [RoutePath.channel]: Channel,

  // user
  [RoutePath.user]: User,
  [RoutePath.login]: LoginRoute,
  [RoutePath.register]: Register,

  // collection
  [RoutePath.collection]: Collection,

  // status
  [RoutePath.status]: Status,

  //message
  [RoutePath.personalMessages]: PersonalMessage,
  [RoutePath.publicNotice]: PublicNotice,
  [RoutePath.dialogue]: Dialogue,
  [RoutePath.messages]: Message,
  [RoutePath.votes]: Votes,
  [RoutePath.rewards]: RewardNotice,

  // my
  [RoutePath.FAQMenu]: FAQMenu,
  [RoutePath.FAQContent]: FAQContent,
  // other
  [RoutePath.tags]: ForumTags,
  [RoutePath.search]: SearchPage,
};

export class MobileRouter extends React.Component<Props, State> {
  public render () {
    const { core } = this.props;
    const paths = Object.keys(MobileRoutes);

    return (<div>
      <Switch>
        {paths.map((_path, i) =>
          <Route exact={_path === '/'}
            path={_path}
            key={i}
            render={(props) => React.createElement(
              MobileRoutes[_path],
              {
                core,
                path: _path,
                ...props,
              },
            )}
          />,
        )}
      </Switch>
    </div>);
  }
}