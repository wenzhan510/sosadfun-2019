import * as React from 'react';
import { Page } from '../common/page';
import { NavBar } from '../common/navbar';

interface Props {
    goBack:() => void;
    submit:(body:string, anonymous?:boolean) => void;
}

interface State {
    body:string;
    anonymous:boolean;
    submitDisabled:boolean;
}

export class Reply extends React.Component<Props, State> {
    public state:State = {
        body: '',
        anonymous: false,
        submitDisabled: true,
    };

    public render () {
        return <Page top={<NavBar
            goBackText={'取消'}
            goBack={this.props.goBack}
            menu={NavBar.MenuText({
                value: '提交',
                onClick: () => this.props.submit(this.state.body, this.state.anonymous),
                disabled: this.state.submitDisabled,
            })}
        >
            回复
        </NavBar>}>

            {/* //todo: */}
        </Page>;
    }
}