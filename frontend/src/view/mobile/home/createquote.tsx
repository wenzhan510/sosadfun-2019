import * as React from 'react';
import { NavBar } from '../../components/common/navbar';
import { MobileRouteProps } from '../router';
import { Page } from '../../components/common/page';
import { Card } from '../../components/common/card';
import { NoticeBar } from '../../components/common/notice-bar';

interface State {
  body:string;
  isAnonymous:boolean;
  majia:string;
  errorMsg:string;
  // createQuote: (body:string, is_anonymous:boolean, majia: string) => Promise<APIPost['/quote']['res'] | null>;
}

export class CreateQuote extends React.Component<MobileRouteProps, State> {
  public state = {
    body: '',
    isAnonymous: false,
    majia: '',
    errorMsg: '',
  };

  public createQuote = async (body:string, isAnonymous:boolean, majia) =>
    await this.props.core.api.addQuote({
      body,
      is_anonymous: isAnonymous,
      majia: isAnonymous ? majia : undefined,
    })

  public render () {
    return <Page
        top={<NavBar goBack={this.props.core.route.back}>
          创建题头
        </NavBar>}>
      {this.props.core.user.isLoggedIn() &&
        <Card>
          {this.state.errorMsg && <NoticeBar>{this.state.errorMsg}</NoticeBar>}
          新题头：
          <textarea className="textarea"
            placeholder="不丧不成活~"
            rows={5}
            value={this.state.body}
            onChange={(ev) => this.setState({body: ev.target.value})}
            >
          </textarea>
          <a className="button">恢复数据</a>

          <div className="is-size-7 has-text-grey">
            （每人每天只能提交一次题头。题头需要审核，题头审核通过的条件是“有品、有趣、有点丧”。不满足这个条件，过于私密，或可能引起他人不适的题头不会被通过。）
          </div>

          <label className="checkbox">
            <input type="checkbox"
              checked={this.state.isAnonymous}
              onChange={(ev) => this.setState({isAnonymous: ev.target.checked})}
              />
            马甲？
          </label>
          {this.state.isAnonymous &&
          <input className="input"
            type="text"
            value={this.state.majia}
            onChange={(ev) => this.setState({majia: ev.target.value})}
          />
          }

          <a className="button is-full-width" onClick={async (ev) => {
            if (this.state.body === '') {
              this.setState({errorMsg: '题头正文 不能为空。'});
            } else if (this.state.body.length > 80) {
              this.setState({errorMsg: '题头不能超过80个字符。'});
            } else if (this.state.isAnonymous && this.state.majia === '') {
              this.setState({errorMsg: '马甲不能为空。'});
            } else {
              const res = await this.createQuote(this.state.body, this.state.isAnonymous, this.state.majia);
              if (!res) { /* TOFIX: 现在只处理422错误 */
                // this.setState({errorMsg: '提交失败。'})
                this.setState({errorMsg: '题头已存在，请勿重复提交。'});
              } else {
                this.setState({body: '', errorMsg: ''});
                /* TODO: 提示提交成功 */
              }
            }
          }}>
            提交
          </a>
        </Card>
      }

      {/* 未登录 */
        !this.props.core.user.isLoggedIn() &&
        <Card>
          用户未登录
        </Card>
      }
    </Page>;
  }
}