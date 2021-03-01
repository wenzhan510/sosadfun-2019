import React from 'react';
import { Page } from '../common/page';
import { NavBar } from '../common/navbar';
import { APIRequest } from '../../../core/api';

type RequestData = APIRequest<'publishThread'>[0];

interface Props {
  onCancel:() => void;
  onSubmit:(data:RequestData) => void;
  type:'thread'|'book';
}

interface State extends RequestData {
  submitDisabled:boolean;
}

export class PublishThread extends React.Component<Props, State> {
  public state:State = {
    submitDisabled: true,
    title: '',
    brief: '',
    body: '',
  };

  public render () {
    return <Page
      top={<NavBar
        goBackText={'取消'}
        goBack={this.props.onCancel}
        menu={NavBar.MenuText({
            value: '发布',
            onClick: () => this.props.onSubmit(this.state),
            disabled: this.state.submitDisabled,
        })}
      >帖子详情</NavBar>}
    >

    </Page>;
  }
}