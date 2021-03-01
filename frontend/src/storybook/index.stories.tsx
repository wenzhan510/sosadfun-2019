import '@fortawesome/fontawesome-free-webfonts/css/fa-brands.css';
import '@fortawesome/fontawesome-free-webfonts/css/fa-regular.css';
import '@fortawesome/fontawesome-free-webfonts/css/fa-solid.css';
import '@fortawesome/fontawesome-free-webfonts/css/fontawesome.css';

import '../view/common.scss';
import '../view/theme/index.scss';

import { action } from '@storybook/addon-actions';
import { withConsole } from '@storybook/addon-console';
import { boolean, number, select, text, withKnobs } from '@storybook/addon-knobs';
import { withViewport } from '@storybook/addon-viewport';
import { addDecorator, storiesOf } from '@storybook/react';
import React from 'react';
import { Router } from 'react-router';
import { Accordion } from '../view/components/common/accordion';
import { Animate } from '../view/components/common/animate';
import { Badge } from '../view/components/common/badge';
import { Card } from '../view/components/common/card';
import { Center } from '../view/components/common/center';
import { Dropdown } from '../view/components/common/dropdown';
import { List } from '../view/components/common/list';
import { InfiniteScroll } from '../view/components/common/infiniteScroll';
import { Mark } from '../view/components/common/mark';
import { NavBar } from '../view/components/common/navbar';
import { Popup } from '../view/components/common/popup';
import { PopupMenu } from '../view/components/common/popup-menu';
import { RouteMenu } from '../view/components/common/route-menu';
import { Slider } from '../view/components/common/slider';
import { Tab } from '../view/components/common/tab';
import { Tag } from '../view/components/common/tag';
import { TagList } from '../view/components/common/tag-list';
import { Core } from '../core/index';
import { Carousel } from '../view/components/common/carousel';
import { NoticeBar } from '../view/components/common/notice-bar';
import { Loading } from '../view/components/common/loading';
import { InputText } from '../view/components/common/input/text';
import { ThreadPreview } from '../view/components/thread/thread-preview';
import { randomCnWords } from '../utils/fake';
import { ChannelPreview } from '../view/components/home/channel-preview';
/*
import { TagBasicList } from '../view/components/home/tagbasic-list';
import { TagBasicListSelect } from '../view/components/home/tagbasiclist-select';
import { TagBasicListFilter } from '../view/components/home/tagbasiclist-filter';
*/
import { ChatBubble } from '../view/components/message/chat-bubble';
import { ExpandableMessage } from '../view/components/message/expandable-message';
import { Fragment } from 'react';
import { fakeDB } from '../test/mock-data/fake-db';
import { Button } from '../view/components/common/button';
import { Colors } from '../view/theme/theme';
import { DB } from '../config/db-type';
import { TextEditor } from '../view/components/common/textEditor';
import { createBrowserHistory } from 'history';
import { bbcode2html, html2bbcode, test } from '../utils/text-formater';
import { bbcodTestCases } from '../test/bbcode/bbcode';
import { loadTestData, formatTestData } from '../test/bbcode/additionalTest';
import { App } from '../view';
import { Reward } from '../view/components/thread/reward';
import { InputNumber } from '../view/components/common/input/number';
import { MenuItem, Menu } from '../view/components/common/menu';
import { NoticeType, notice } from '../view/components/common/notice';
import { HomeworkPreview } from '../view/components/home/homework-preview';
import { Picker, Item } from '../view/components/common/picker';
import { ReadingSettings } from '../view/components/thread/reading-settings';
import { loadStorage } from '../utils/storage';
import { Checkbox } from '../view/components/common/input/checkbox';
import { EmptyState } from '../view/components/common/empty-state';

const core = new Core();
fakeDB(core.api);

addDecorator((storyFn, context) => withConsole()(storyFn)(context));
addDecorator(withViewport());
addDecorator(withKnobs);
addDecorator((storyFn) => <div id="app" className="theme-light" data-theme="light">{storyFn()}</div>); //todo: add theme switcher

storiesOf('Common Components', module)
  .add('button', () => <Button
    onClick={action('onClick')}
    icon={text('icon', 'fa fa-search')}
    disabled={boolean('disabled', false)}
    color={select('color', {primary: 'primary', default: ''}, '')}
    size={select('size', {default: '', small: 'small'}, '')}
    type={select('type', {default: '', ellipse: 'ellipse'}, '')}
  >I am a button</Button>)
  .add('Badge', () => <Badge num={number('num', 10)}
      max={number('max', 0)}
      dot={boolean('dot', false)}
      hidden={boolean('hidden', false)}>
      {text('text', 'test')}
    </Badge>,
  )
  .add('Tag', () => {
    return <Tag
      selected={boolean('selected', false)}
      onClick={action('tag click')}
      size={select('size', {
        default: 'default',
        small: 'small',
      })}
      color={select('color', Colors, '')}
      selectedColor={select('selectedColor', Colors, Colors.primary)}
      rounded={boolean('rounded', false)}
    >{text('text', 'test')}</Tag>;
  })
  .add('TagList', () => {
    return <TagList>
      {(new Array(number('length', 20)).fill(text('text', 'tag')).map((content, i) => <Tag
        key={i}
        onClick={action('tag click ' + i)}
        size={select('size', {
          default: 'default',
          small: 'small',
        })}
        color={select('color', Colors, undefined)}
        selectedColor={select('selectedColor', Colors, Colors.primary)}
        rounded={boolean('rounded', false)}
      >{content}</Tag>))}
    </TagList>;
  })
  .add('Popup', () => React.createElement(class extends React.Component {
    public state = {
      showPopup: true,
    };
    public render () {
      return <div>
        <button className="button" onClick={() => this.setState({showPopup: true})}>show popup</button>
        {this.state.showPopup &&
          <Popup
            width={text('width', '')}
            bottom={text('bottom', '')}
            onClose={() => this.setState({showPopup: false})}>
            {text('content', 'example content')}
          </Popup>
        }
      </div>;
    }
  }))
  .add('Center', () => <Center width={text('width', '')} height={text('height', '')}>
    <div>center anything</div>
  </Center>)
  .add('List', () => <List>
    {['a', 'b', 'c'].map((item, i) => <List.Item
      key={i}
      onClick={() => alert('click item ' + item)}
      arrow={boolean('arrow', false)}>
      {item}
    </List.Item>)}
  </List>)
  .add('InfiniteScroll', () => React.createElement(class extends React.Component {
    public state = {
      items: [],
      isLoading: true,
      cursor: 0,
    };
    private divStyle = {
      border: '5px solid pink',
      background: 'antiquewhite',
      height: '250px',
      width: '300px',
    };

    public componentDidMount() {
      // do some paginated fetch
      this.loadMore();
    }

    public loadMore = () => {
      const cursor = this.state.cursor;
      this.setState({ isLoading: true, error: undefined });
      const load = new Promise<number[]>((resolve, reject) => {
        setTimeout(() => resolve([cursor, cursor + 1, cursor + 2, cursor + 3, cursor + 4, cursor + 5]), 500);
      });

      // cursor should not be used like this. It should be used to make paginated db fetch, but here it's used to generate dumb data
      load
        .then(
          (res) =>
            (this.setState({
              items: [...this.state.items, ...res],
              cursor: res[5] + 1,
              isLoading: false,
            }))
          ,
          (error) => {
            this.setState({ isLoading: false, error });
          });
    }

    public render() {
      // here the internal element is <p>, but it can be any element
      return (
        <Fragment>
        <div style={this.divStyle}>
          <InfiniteScroll
            throttle={100}
            threshold={50}
            isLoading={this.state.isLoading}
            hasMore={this.state.cursor < 200}
            onLoadMore={this.loadMore}
          >
            {this.state.items.length > 0
              ? this.state.items.map((item, i) => (
                  <p key={i}>{item}</p>
                ))
              : null}
          </InfiniteScroll>
          {this.state.isLoading && (
            <p>Loading...</p>
          )}
        </div>
        </Fragment>
      );
    }
  }))
  .add('Accordion', () => <Accordion
    title={text('title', 'accordion title')}
    arrow={boolean('arrow', true)}
  >
    <List>
      <List.Item>1</List.Item>
      <List.Item>2</List.Item>
    </List>
  </Accordion>)
  .add('Menu', () => React.createElement(class extends React.Component {
    public state = {
      onIndex: 0,
      icon: boolean('icon', false),
    };
    public render () {
      const items = [
        {to: '', label: 'menu1'},
        {to: '', label: 'menu2'},
        {to: '', label: 'menu3'},
      ];
      if (this.state.icon) {
        for (let i = 0; i < items.length; i ++) {
          const item = items[i];
          item['icon'] = 'fas fa-star';
          item['selectedColor'] = 'red';
          item['defaultColor'] = 'black';
        }
      }
      return <Router history={createBrowserHistory()}>
          <RouteMenu
            items={items}
            onIndex={this.state.onIndex}
            onClick={(_, i) => this.setState({onIndex: i})}
          ></RouteMenu>
      </Router>;
    }
  }))
  .add('Mark', () => <Mark length={number('length', 5)}
      mark={boolean('disabled', false) ? 4 : undefined}
      onClick={action('onClick')} />,
  )
  .add('Slider', () =>
    <Slider>
      {[1, 2, 3, 4, 5, 6, 7].map((i) =>
        <Slider.Item key={i}>
          <Card style={{
            width: '70px',
            height: '70px',
            border: '1px solid grey',
            padding: '1px',
            marginTop: '0',
          }}>
            <Center>
              card {i}
            </Center>
          </Card>
        </Slider.Item>,
      )}
    </Slider>,
  )
  .add('tab', () => {
    const tabContent = [1, 2, 3, 4, 5].map((tab) => {
      return {
        name: 'tab' + tab,
        children: <List noBorder>
          {[1, 2, 3, 4].map((item) =>
            <List.Item key={item}>tab {tab} list-item {item}</List.Item>,
          )}
        </List>,
      };
    });
    return <Tab
      tabs={tabContent}
      onClickTab={action('onClickTab')}
    />;
  })
  .add('popup menu', () => React.createElement(class extends React.Component<{}, {showPopup:boolean}> {
    public state = {
      showPopup: true,
    };
    public render () {
      return <div>
        <div className="button"
          onClick={() => this.setState((prevState) => ({showPopup: !prevState.showPopup}))}>
          show popup
        </div>
        {this.state.showPopup && <PopupMenu
          list={[
            { title: 'one', onClick: action('clickOne')},
            { title: 'two', onClick: action('clickTwo')},
          ]}
          onClose={() => this.setState({showPopup: false})}
        />}
    </div>;
    }
  }))
  .add('animation', () => <Animate
    name={select(
      'name',
      {
        slideInUp: 'slideInUp',
        slideOutUp: 'slideOutUp',
        slideInDown: 'slideInDown',
        slideOutDown: 'slideOutDown',
        slideInRight: 'slideInRight',
        slideOutRight: 'slideOutRight',
        slideInLeft: 'slideInLeft',
        slideOutLeft: 'slideOutLeft',
      },
      'slideInUp')}
    speed={select(
      'speed',
      {
        slow: 'slow',
        slower: 'slower',
        fast: 'fast',
        faster: 'faster',
      },
      undefined)}
    infinite={boolean('infinite', false)}
    ><div>example animation</div></Animate>)
  .add('carousel', () => <Carousel
    windowResizeEvent={core.windowResizeEvent}
    slides={[
      <div> sample slide 1 </div>,
      <div> sample slide 2 </div>,
      <div> sample slide 3 </div>,
    ]}
    getIndex={action('getIndex')}
    indicator={boolean('indicator', true)}
    startIndex={number('startIndex', 0)}
  />)
  .add('Loading', () =>
    <Loading isLoading={boolean('isLoading', true)}>
      <div style={{
        width: '200px',
        height: '200px',
        border: '1px solid black',
        lineHeight: '200px',
        textAlign: 'center',
      }}>
        <p> 加载好了 </p>
      </div>
    </Loading>,
  )
  .add('Menu', () => (
    <Menu>
      <MenuItem icon="far fa-thumbs-up icon" title="点赞提醒" badgeNum={1000} />
      <MenuItem icon="fas fa-gift icon" title="打赏提醒" badgeNum={1} />
    </Menu>
  ))
  .add('Notice', () => (React.createElement(class extends React.Component<{}, {content:string}> {
    public state = {
      content:'message content',
    };

    public onChange = (event:React.ChangeEvent<HTMLInputElement>) => {
      this.setState({
        content: event.target.value,
      });
    }

    public render() {
      return <div>
        <input value={this.state.content} onChange={this.onChange}/>
        {
          Object.keys(NoticeType).map((value:string) => <button key={value}
            onClick={() => notice.addNotice(this.state.content, value as NoticeType, 5000) }>
              {value}
            </button>)
        }
      </div>;
    }
  },
  )))
  .add('Picker', () => (React.createElement(
    class extends React.Component<{}, {
      showPicker:boolean,
    }> {
      public state = {
        showPicker: false,
      };

      public show = () => {
        this.setState({showPicker:  true});
      }

      public hide = () => {
        this.setState({showPicker:  false});
      }

      public confirm = (v) => {
        this.hide();
        console.log(v);
      }

      public generateItems = (max:number) => {
        const items:Item[]= [];
        for (let index = 0; index < max; index++) {
          items.push({
            label: index,
            value: index.toString(),
          });
        }
        return items;
      }

      public render() {
        return (
          <div>
            <Button onClick={this.show}>显示 Picker</Button>
            {
              this.state.showPicker &&
              <Picker
                onCancel={this.hide}
                onConfirm={this.confirm}
                columnOpts={[
                  {
                    key: '1',
                    items: () => this.generateItems(10),
                  },
                  {
                    key: '2',
                    items: (selectedValue) => selectedValue['1'] ? this.generateItems(Number.parseInt(selectedValue['1'])) :[],
                  },
                  {
                    key: '3',
                    items: (selectedValue) => selectedValue['2'] ? this.generateItems(Number.parseInt(selectedValue['2'])) :[],
                  },
                ]}
              />
            }
          </div>
        );
      }
    },
  )))
  .add('EmptyState', () => (<div>
    <EmptyState/>
    <EmptyState size="middle" tip="这是一个中 TIP"/>
    <EmptyState size="large" tip="这是一个大 TIP"/>
  </div>));

storiesOf('Common Components/Notice Bar', module)
  .add('short message', () => <NoticeBar
    icon={text('icon', 'fas fa-bullhorn')}
    closable={boolean('closable', true)}
    onClick={action('onClick')}
    customizeLink={(() => {
      if (boolean('customizeLink', false)) {
        return <div>go</div>;
      }
      return undefined;
    })()}
    >example notice message
  </NoticeBar>)
  .add('long message', () => <NoticeBar
    icon={text('icon', 'fas fa-bullhorn')}
    closable={boolean('closable', true)}
    onClick={action('onClick')}
  >
    example super super super super super super super super super super super super super super super super super super long notice message
  </NoticeBar>)
;

storiesOf('Common Components/Dropdown', module)
  .add('Dropwdown', () => <Dropdown
    list={[{text: '1', value: 1}, {text: '2', value: 2}]}
    onIndex={0}
    onClick={action('onClick')}
    />)
  .add('Dropdown(with title)', () => {
    return <Dropdown
      list={[{text: '1', value: 1}, {text: '2', value: 2}]}
      title={text('title', 'dropdown menu')}
      onClick={action('onClick')}
    />;
  })
;

storiesOf('Common Components/Input', module)
  .add('InputText', () => (React.createElement(class extends React.Component<{}, { value:string, value2:string }> {
    public state = {
      value: '',
      value2: '',
    };
    public render() {
      const placeholderCentered = boolean('placeholderCentered', true);
      const withLabel = boolean('withLabel', true);
      return (
        <div>
          <p>type: default</p>
          <InputText
            label={withLabel ? <i className="fa fa-search" /> : null}
            value={this.state.value}
            placeholder="placeholder"
            placeholderCentered={placeholderCentered}
            style={{
              height: '30px',
            }}
            onChange={(value) => {
              this.setState({
                value,
              });
            }}
            onConfirm={() =>
              console.log('onConfirm')
            }
            onClick={() =>
              console.log('onClick')
            }
            onKeyDown={() =>
              console.log('onKeyDown')
            }
          />
          <br/>
          <p>type: wide</p>
          <InputText
            value={this.state.value2}
            placeholder="placeholder"
            type="wide"
            info="here goes some info msg"
            warning="here goes some warnings"
            onChange={(value2) => {
              this.setState({
                value2,
              });
            }}
            onConfirm={() =>
              console.log('onConfirm')
            }
            onClick={() =>
              console.log('onClick')
            }
            onKeyDown={() =>
              console.log('onKeyDown')
            }
          />
        </div>);
    }
  })))
  .add('InputNumber', () => (React.createElement(class extends React.Component<{}, {
    value:number,
  }> {
    public state = {
      value:0,
    };

    public onChange = (value:number) => {
      this.setState({
        value,
      });
    }

    public render() {
      return <div>
        <InputNumber
          value={this.state.value}
          onChange={this.onChange}
          fractionDigits={number('fractionDigits', 0)}
          disabled={boolean('disabled', false)}
          placeholder={text('placeholder', 'placeholder')}
          min={number('min', -10)}
          max={number('max', 10)}
        />
        <button onClick={() => console.log(this.state.value)}>打印</button>
      </div>;
    }
  })))
  .add('checkbox', () => React.createElement(class extends React.Component<{}, {
    checked1:boolean;
    checked2:boolean;
    checked3:boolean;
  }> {
    public state = {
      checked1:false,
      checked2:false,
      checked3:false,
    };

    public render() {
      return (
        <div style={{paddingLeft: '20px'}}>
          <p>checkbox only</p>
          <Checkbox
            checked={this.state.checked1}
            onChange={() => this.setState({checked1: !this.state.checked1})}/>
          <br/>
          <p>checkbox with label</p>
          <Checkbox
            checked={this.state.checked2}
            onChange={() => {
              this.setState({checked2: !this.state.checked2});
            }}
            label="this is a label"/>
          <br/>
          <p>radio with label</p>
          <Checkbox
            checked={this.state.checked3}
            onChange={() => {
              this.setState({checked3: !this.state.checked3});
            }}
            label="this is a label"
            type="radio"/>
        </div>
      );
    }
  }));

storiesOf('Common Components/Input/TextEditor', module)
.add('style1', () => React.createElement(class extends React.Component<{}, {content:string, generatedBBCODE:string, test:any, extraData:any[], testId:number, useDefaultTest:boolean}> {
  private ref = React.createRef<TextEditor>();  // you have to use ref with this component
  public state = {
    content: '',
    generatedBBCODE: '',
    test: '',
    extraData: [],
    testId: 0,
    useDefaultTest: true,
  };

  public async componentWillMount () {
    await this.loadExtraTestData();
  }
      // will return content in bbcode
  private getContent = () => {
    let content = '';
    if (this.ref.current) {
      content = this.ref.current.getContent();
    }
    return content;
  }

  private test = () => {
    const bbcode = this.getContent();
    const result = test(bbcode);
    this.setState({content: bbcode,
      test: result ? 'success' :'failure, check console'});
  }

  private loadExtraTestData = async () => {
    const extraData = await loadTestData();
    this.setState({extraData});
  }

  private getTest() {
    let testId = this.state.testId || 0;
    if (testId < 0) {
      testId = 0;
    }

    if (this.state.useDefaultTest) {
      if ( testId >= bbcodTestCases.length ) { testId = 0; }
      return {type: 'normal', testID: bbcodTestCases[testId].id, testContent: bbcodTestCases[testId].test};
    } else {
      if ( testId >= this.state.extraData.length ) { testId = 0; }
      return {type: 'excel', testID: testId, testContent: formatTestData(this.state.extraData[testId])};
    }
  }

  public render() {
    const { type, testID, testContent } = this.getTest();

    return  (
    <div>
      <TextEditor ref={this.ref} content={ testContent }></TextEditor>
      <br/>
      <button onClick={() => this.setState({generatedBBCODE: this.getContent()})}>Generate BBCODE</button>
      {this.state.generatedBBCODE && (
        <div>
          Following is the generate BBCODE:
          <pre>{this.state.generatedBBCODE}</pre>
          <button onClick={() => this.setState({generatedBBCODE: ''})}>
            Clear
          </button>
        </div>
        ) }
      <br/>
      <button onClick={this.test}>TEST</button>
      <div>
        The TEST button will export bbcode, convert the bbcode to html, and the generated html to bbcode, then compare the two bbcode strings, they should be the same.
        {this.state.test ?  (
        <p>
          <strong>
            { this.state.test }
          </strong>
          <button onClick={() => this.setState({test: ''})}>
            Clear
          </button>
        </p>) : null
      }
      </div>
      <br/>
      <div>
        Using { this.state.useDefaultTest ? 'local default test suit' : 'remote excel test suit' }.
        <button onClick={() => { this.setState({useDefaultTest: !this.state.useDefaultTest }); }}> switch to { this.state.useDefaultTest ? 'remote excel test suit' : 'local default test suit' } </button>

        { this.state.useDefaultTest ? (
          <p>
            There are <strong>{bbcodTestCases.length}</strong> test cases available. To test a test case, enter a number from <strong>0 ~ {bbcodTestCases.length - 1}</strong>
          </p>
        ) : (
          <div>
            { this.state.extraData.length == 0 ? 'still loading remote remote test suit...' :
              <p>There are <strong>{this.state.extraData.length}</strong> test cases available. To test a test case, enter a number from <strong>0 ~ {this.state.extraData.length - 1}</strong></p>}
          </div>
        )}
      </div>
      <input type="number" value={this.state.testId} min="0" max={this.state.useDefaultTest ? bbcodTestCases.length - 1 : this.state.extraData.length - 1} onChange={(e) => this.setState({testId: Number(e.target.value)})}></input>
    <div>
      <br/>
      Current Test case is <strong>{ testID }</strong>. Following is the test case bbcode:
      <pre>{ testContent }</pre>
    </div>
  </div>);
  }
}))
.add('style2', () => (
  <div style={{ height: '400px', position: 'relative' }}>
    <TextEditor theme="bubble" placeholder="写回复" style={{position: 'absolute', bottom: 0, width: '100%'}}></TextEditor>
  </div>
));

storiesOf('Common Components/Navigation Bar', module)
  .add('simple', () => <NavBar goBack={action('goBack')} >
    {text('title', 'example title')}
  </NavBar>)
  .add('with menu', () => React.createElement(class extends React.Component {
    public state = {
      showPopup: false,
    };
    public render () {
      return <NavBar goBack={action('goBack')}
        menu={NavBar.MenuIcon({
          onClick: () => this.setState({showPopup: true}),
        })}>
      {text('title', 'example title')}
      {this.state.showPopup &&
        <PopupMenu
          list={[
            {title: 'menu1', onClick: action('click menu1')},
            {title: 'menu2', onClick: action('click menu2')},
          ]}
          onClose={() => this.setState({showPopup: false})}
        />
      }
    </NavBar>;
    }
  }))
;

storiesOf('Home Components/HomePage', module)
  .add('ChannelPreview', () => React.createElement(class extends React.Component {
    public render () {
      const items = [
        {id:1, title:'春潮', brief:'我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性', author:'小山鬼'},
        {id:2, title:'stay gold', brief:'娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷娱乐圈万人迷', author:'草率'},
        {id:3, title:'英国病人', brief:'我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性我要吞下蛮荒的野兽本性', author:'小山鬼'},
    ];
      return <Router history={createBrowserHistory()}>
          <ChannelPreview
            goToThread={action('goToThread')}
            title="推荐榜单"
            threads={items}>
        </ChannelPreview>
        </Router>;
    }
  }))
  .add('HomeworkPreview', () => React.createElement(class extends React.Component {
    public onMoreClick = () => {
      console.log('click more');
    }

    public onHomeworkClick = (id:number) => {
      console.log(`click id: ${id}`);
    }

    private homeworks:DB.BriefHomework[] = [
      {'type':'homework', 'id':5, 'attributes':{'title':'第五次作业', 'topic':'纽约客', 'level':0, 'is_active':true, 'purchase_count':0, 'worker_count':14, 'critic_count':6}},
      {'type':'homework', 'id':4, 'attributes':{'title':'第四次作业', 'topic':'合不上的行李箱', 'level':0, 'is_active':true, 'purchase_count':0, 'worker_count':10, 'critic_count':8}},
      {'type':'homework', 'id':3, 'attributes':{'title':'第三次作业', 'topic':'失控', 'level':0, 'is_active':true, 'purchase_count':0, 'worker_count':11, 'critic_count':23}},
      {'type':'homework', 'id':2, 'attributes':{'title':'第二次作业', 'topic':'最大的恐惧不是死亡', 'level':0, 'is_active':false, 'purchase_count':0, 'worker_count':5, 'critic_count':14}},
      {'type':'homework', 'id':1, 'attributes':{'title':'第一次作业', 'topic':'春天的故事', 'level':0, 'is_active':false, 'purchase_count':0, 'worker_count':7, 'critic_count':8}},
    ];

    public render() {
      return <div style={{
        backgroundColor:'#f4f5f9',
      }}>
        <HomeworkPreview
          onHomeworkClick={this.onHomeworkClick}
          onMoreClick={this.onMoreClick}
          homeworks={this.homeworks}/>
      </div>;
    }
  }))
  /*
  .add('TagBasicList1', () => React.createElement(class extends React.Component {
    public render () {
      return <div style={{
        width: '100%',
        margin: '0',
        padding: '0',
        backgroundColor:'rgba(244,245,249,1)'}}>
        <TagBasicList
          tagCategoryName={'最近搜索'}
          childTags={ [{tagId:'12', tagName:'星星'} , {tagId:'13' , tagName:'星星月亮'}]}
          tagSize={'normal'}
          tagColor={'white'}
          selectedColor={'danger'}
          showTrashbin={true}
          backgroundColor={'rgba(244,245,249,1)'}
          onClick={(selected, selectedId) => {console.log('selectedId' , selectedId); }}>
        </TagBasicList>
      </div>;
    }
  }))
  .add('TagBasicList2', () => React.createElement(class extends React.Component {
    public render () {
      return <div style={{
        width: '100%',
        margin: '0',
        padding: '0',
        backgroundColor:'rgba(244,245,249,1)'}}>
        <TagBasicList
          tagCategoryName={'热门推荐'}
          childTags={ [{tagId:'12', tagName:'得偿所愿'} , {tagId:'13' , tagName:'翅膀养护日记'}]}
          tagSize={'normal'}
          tagColor={'white'}
          selectedColor={'danger'}
          showTrashbin={false}
          backgroundColor={'rgba(244,245,249,1)'}
          onClick={(selected, selectedId) => {console.log('selectedId' , selectedId); }}>
        </TagBasicList>
      </div>;
    }
  }))
  .add('TagBasicList3', () => React.createElement(class extends React.Component {
    public render () {
      return <div style={{
        width: '100%',
        margin: '0',
        padding: '0',
        backgroundColor:'white'}}>
        <TagBasicList
          tagCategoryName={'文章分类'}
          childTags={ [{tagId:'12', tagName:'原创'} , {tagId:'13' , tagName:'同人'}]}
          tagSize={'medium'}
          tagColor={'light'}
          selectedColor={'danger'}
          showTrashbin={false}
          backgroundColor={'white'}
          onClick={(selected, selectedId) => {console.log('selectedId' , selectedId); }}>
        </TagBasicList>
      </div>;
    }
  }))
  .add('tagBasicListSelect', () => React.createElement(class extends React.Component {
    public state = {
        counter: 0,
        selectedTags: [],
    };
    public render () {
        return <TagBasicListSelect
        taglist={[{tagCatagoryName:'文章分类',
        childTags:[{tagId:'12', tagName:'原创'} , {tagId:'13' , tagName:'同人'}]},
        {tagCatagoryName:'篇幅',
        childTags:[{tagId:'14', tagName:'短篇'} , {tagId:'15' , tagName:'中篇'},
        {tagId:'16', tagName:'长篇'}, {tagId:'17', tagName:'大纲'}]},
        ]}
        onBack={() => {console.log('back'); }}
        onFilter={() => {console.log('filter'); }}
        selectedCounter={this.state.counter}
        onSelect={(tags) => {
          const selectedTags = [...tags];
          this.setState({selectedTags});
          this.setState({counter:selectedTags.length});
          console.log('stearry', this.state.selectedTags);
          console.log('stearbuber', this.state.counter);
        }}
        >
        </TagBasicListSelect>;
    }
  }))
  .add('tagBasicListFilter', () => React.createElement(class extends React.Component {
    public state = {
        recentSearchTags: [],
    };
    public render () {
        return <TagBasicListFilter
        taglist={[{tagCategoryName:'最近搜索',
        categoryTrash:true,
        childTags:[{tagId:'12', tagName:'夏天'} , {tagId:'13' , tagName:'星赭'}]},
        {tagCategoryName:'热门推荐',
        categoryTrash:false,
        childTags:[{tagId:'14', tagName:'九州见闻'} , {tagId:'15' , tagName:'得偿所愿'},
        {tagId:'16', tagName:'翅膀养护日记'}, {tagId:'17', tagName:'不知道写啥'}]},
        ]}
        onBack={() => {console.log('back'); }}
        onDelete={(tags) => {
          console.log('filter');
          // 1,从最近搜索标签组中删除
          const recentSearchTags = [...tags];
          this.setState({recentSearchTags});
        }}
        onFilter={ (filCriteria) => {
          // 1，加入到最近搜索
          // 2，进行搜索
          console.log('filter condition is ', filCriteria); } }
        >
        </TagBasicListFilter>;
    }
  }))
  */
;
storiesOf('Home Components', module)
;

storiesOf('User Components', module)
;

storiesOf('Thread Components', module)
  .add('list preview', () => <Card>
    <ThreadPreview
      mini={boolean('mini', false)}
      data={(() => {
        const thread = DB.allocThread();
        thread.id = 1;
        thread.attributes.title = randomCnWords(number('title', 20), 0.15);
        thread.attributes.brief = randomCnWords(number('brief', 40), 0.2);
        thread.attributes.view_count = number('view', 200);
        thread.attributes.reply_count = number('reply', 40);
        thread.attributes.channel_id = 1;
        thread.last_post = DB.allocPost();
        thread.last_post.id = 1;
        thread.last_post.attributes.title = randomCnWords(number('post title', 40), 0.2);
        thread.last_post.attributes.brief = randomCnWords(20);
        thread.author.attributes.name = randomCnWords(number('author name', 3), 0),
        thread.author.id = 1;
        thread.tags.push({
          type: 'tag',
          id: 1,
          attributes: {
            tag_name: '日常闲聊',
            tag_type: '',
          },
        });
        return thread;
      })()}
      onTagClick={action('toChannelTag')}
      onClick={action('onClick')}
      onUserClick={action('onUserClick')}
    />
  </Card>)
  .add('Reward', () => (React.createElement(class extends React.Component<{}, {showReward:boolean}> {
    public state= {
      showReward: false,
    };

    public changeShow = (showReward:boolean) => {
      this.setState({
        showReward,
      });
    }

    public render() {
      return <div>
        <button onClick={() => this.changeShow(true)}>打赏</button>
        {
          this.state.showReward && <Reward
            onClose={() => this.changeShow(false)}
            salt={0}
            fish={59}
            ham={5849}
            onReward={(type, num) => console.log(type, num)}
          />
        }
      </div>;
    }
  })))
  .add('ReadingSettings', () => (React.createElement(class extends React.Component<{}, {show:boolean}> {
    public state= {
      show: false,
    };

    public changeShow = (show:boolean) => {
      this.setState({
        show,
      });
    }

    public render() {
      return <div>
        <button onClick={() => this.changeShow(true)}>打开设置</button>
        {
          this.state.show && <ReadingSettings
            onClose={() => this.changeShow(false)}
            onConfirm={(theme) => {
              console.log(theme, loadStorage('readingSettings'));
              this.changeShow(false);
            }}
          />
        }
      </div>;
    }
  })));

storiesOf('Message Components', module)
  .add('chatBubble', () =>
    (<div style={{'width':'100%', 'height':'100%', 'background':'#f4f5f9', 'padding':'30px'}}>
      <ChatBubble fromMe={boolean('fromMe', false)} content={text('content', 'This is a chat bubble!')}></ChatBubble>
    </div>))
  .add('Expandable Message', () => {
    const content =
    `今日，系统查封了大批共享账号。
    其中，对于一些有被盗嫌疑的共享账号，系统进行了保护性的临时禁封。
    这些账号不一定是注册者恶意共享，而是被攻击者通过撞库盗号后，盗号者不做任何改动，便将账号密码贩卖出去，从而进行多人共享。
    也就是说，在原主人不知道的情况下，该账号被偷偷地、反复地使用。
    因此，系统会定时对多人异地使用、行为异常的账户进行封禁。
    为了避免这种情况，避免被系统禁封，请使用了简单密码的用户早日更换为在别处不经常使用的密码，增加账号安全。
    废文禁止盗号，禁止任何形式的账户买卖，买号者付出的金钱，正是攻击者攻击网站的动力。

    没有买卖就没有攻击。`;
    const title = '没有买卖就没有攻击';
    const footer = '废文网大内总管 2019-10-29 18:03:54';
    return (<ExpandableMessage title={text('title', title)} content={text('content', content)} footer={footer} uid={'1'} boldTitle={boolean('bold title', false)}></ExpandableMessage>);
  })
;

storiesOf('Status Components', module)
;

storiesOf('Collection Components', module)
;
