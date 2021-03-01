import * as React from 'react';
import { Popup } from '../common/popup';
import { Themes, Colors } from '../../theme/theme';
import { FontType, loadStorage, saveStorage } from '../../../utils/storage';
import { Button } from '../common/button';
import './reading-settings.scss';

interface Props {
  onClose:() => void;
  onConfirm:(theme:Themes) => void;
}

interface State {
  theme:Themes;
  fontSize:number;
  fontType:FontType;
}

export class ReadingSettings extends React.Component<Props, State> {

  public options:[keyof State, string, [string, State[keyof State]][]][] = [
    ['fontSize', '字号', [['小', 10], ['中', 14], ['大', 18]]],
    ['theme', '模式', [['白天', Themes.light], ['夜间', Themes.dark]]],
    ['fontType', '字体', [['中文简体', 'simplifiedChinese'], ['中文繁体', 'traditionalChinese']]],
  ];

  constructor(props:Props) {
    super(props);
    // load default settings
    const theme = loadStorage('theme');
    const fontSettings = loadStorage('readingSettings');
    this.state = {
      theme,
      fontSize: fontSettings.fontSize,
      fontType: fontSettings.fontType,
    };
  }

  public onConfirm = () => {
    saveStorage('readingSettings', {
      fontSize: this.state.fontSize,
      fontType: this.state.fontType,
    });
    saveStorage('theme', this.state.theme);
    this.props.onConfirm(this.state.theme);
  }

  public render () {
    return <Popup onClose={this.props.onClose} className="reading-settings"
      style={{
        backgroundColor: 'var(--color-bg-base)',
        padding:'16px',
      }} width="327px">
        <div className="title"> 阅读设置 </div>
        <div>
          {
            this.options.map(([key, title, options]) => {
              return <div className="item" key={key}>
                <div className="item-name"> {title} </div>
                <div className="radios">
                  {
                    options.map(([name, value]) => {
                      return <div key={value} className="radio"
                        onClick={() => {
                          const newState = {};
                          newState[key] = value;
                          this.setState(newState);
                        }}>
                          <input type="radio" name={key} value={value}
                          checked={this.state[key] === value} readOnly/>
                          { name }
                      </div>;
                    })
                  }
                </div>
              </div>;
            })
          }
        </div>
        <Button onClick={this.onConfirm} color={Colors.primary}>
          确认
        </Button>
    </Popup>;
  }
}
