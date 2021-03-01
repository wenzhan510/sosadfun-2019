import * as React from 'react';
import { Card } from '../../../components/common/card';
import { DB } from '../../../../config/db-type';
import { QuizQuestionAnswer } from './register';
import { Checkbox } from '../../../components/common/input/checkbox';

interface Props {
  email:string;
  quiz:DB.QuizQuestion[];
  changeQuizAnswer:(qa:QuizQuestionAnswer[]) => () => void;
}
type CheckBoxState = {[key:number]:{[key:number]:boolean}};
interface State {
  checkboxes:CheckBoxState;
}

// TODO: refactor out quiz component, so 升级题 can reuse

export class RegMail2 extends React.Component<Props, State> {
  public state:State = {
    checkboxes:{},
  };
  public componentDidMount() {
    const checkboxes:CheckBoxState = {};
    this.props.quiz.forEach((question) => {
      checkboxes[question.id] = this.allocQuizAnswer(question);
    });
    this.setState({checkboxes});
  }

  private handleOptionChange = (quiz:DB.QuizQuestion, optionID:number ) => () => {
    const question = this.state.checkboxes[quiz.id];
    question[optionID] = !question[optionID];
    const checkboxes = {
      ...this.state.checkboxes,
      [quiz.id]: question };
    this.setState({checkboxes}, this.checkQuizDone);

    // check if user has finish all questions
  }
  private checkQuizDone() {
    // does not exist a question that has no option selected
    if (Object.values(this.state.checkboxes).findIndex(
      (question) => Object.values(question).findIndex((a) => !!a) < 0,
    ) < 0) {
      // transform checkboxes value to quizAnswer
      this.props.changeQuizAnswer(this.formatQuizAnswer())();
    } else {
      this.props.changeQuizAnswer([])();
    }
  }
  private formatQuizAnswer() {
    const quizAnswer:QuizQuestionAnswer[] =
        Object.keys(this.state.checkboxes).map((questionKey) => {
          const answer = { id: Number(questionKey), answer:'' };
          Object.keys(this.state.checkboxes[questionKey])
            .forEach((optionKey) => {
              if (this.state.checkboxes[questionKey][optionKey]) {
                answer.answer += `${optionKey}, `;
              }
            });
          answer.answer = answer.answer.substring(0, answer.answer.length - 2);
          return answer;
        });
    return quizAnswer;
  }

  private allocQuizAnswer(quiz:DB.QuizQuestion) {
    const question:{[key:number]:boolean} = {};
    quiz.attributes.options.forEach((o) => {
      question[o.id] = false;
    });
    return question;
  }

  private renderQuizQuestion(question:DB.QuizQuestion) {
    return (
      <div className="quiz-question" key={question.id}>
        <p>{ question.attributes.body }</p>
        { question.attributes.options.map((o) => (
          <Checkbox
            type="radio"
            className="quiz-option" key={'' + o.id}
            checked={this.state.checkboxes[question.id][o.id]}
            onChange={this.handleOptionChange(question, o.id)}
            label={o.attributes.body}
          />
        ))}
      </div>
    );
  }
  public render () {
    return (
      <Card className="reg">
      {/* TODO: use h2 here, after h2 is defined in common.scss */}
      <p className="title">步骤二：回答问题（11题中只需答对7题）</p>
      <p className="small-warning">你正在使用 {this.props.email} 进行注册，如果邮箱有误，请勿继续！</p>
      <p>你好！欢迎你前来废文！因为当前排队人数较多，为了避免误入、囤号和机器批量注册，保证真正的申请者能够进入排队队列，请先回答下列问题哦!</p>

      <div id="quiz">
        { Object.keys(this.state.checkboxes).length > 0
          && this.props.quiz.map((q) => this.renderQuizQuestion(q)) }
      </div>

      <p>为保证注册公平，避免机器恶意注册，页面含有防批量注册机制，五分钟只能回答一次问题，请核实后再提交回答，请勿直接“返回”前页面重新提交。</p>
    </Card>
    );
   }
}