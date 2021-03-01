import * as React from 'react';
import { classnames } from '../../../utils/classname';
import { Tag } from '../common/tag';

type TagColor = 'black'|'dark'|'light'|'white'|'primary'|'link'|'info'|'success'|'warning'|'danger';
type tagType = {
  tagId:string,
  tagName:string,
};

/*
export class TagBasicList extends React.Component<{
  // props
  tagCategoryName:string;
  childTags:tagType[];
  tagSize:'normal'|'medium'|'large',
  tagColor:TagColor,
  selectedColor:TagColor,
  showTrashbin:boolean,
  onClick:(selected:boolean, selectedId:string) => void;
  backgroundColor: string,
  className?:string;
  style?:React.CSSProperties;
  sortAvailable?:boolean;
  sortFlag?:boolean;
}, {
  // states
  sortFlag:boolean;
  sortAvailable:boolean;
  myChildTags:tagType[];
}> {
  public state = {
    sortAvailable: this.props.sortAvailable || false, // false, 默认排序按钮不可见
    sortFlag: this.props.sortFlag || false, // false, 默认按照标签名字升序排列
    myChildTags: this.props.childTags || [],
  };

  public onSort = (sortFlag:boolean) => {
    console.log('click onsort', sortFlag);
    const childTags = [...this.state.myChildTags];
    console.log('tags',childTags);
    // false,升序
    if (!sortFlag) {
      childTags.sort( (a, b) => (a.tagName > b.tagName) ? 1 : ((b.tagName > a.tagName) ? -1 : 0));
    }
    //true,降序
    if (sortFlag) {
      childTags.sort( (a, b) => (a.tagName > b.tagName) ? -1 : ((b.tagName > a.tagName) ? 1 : 0));
    }
    console.log('tags2',childTags);
    this.setState({myChildTags:childTags});
  }

  public render () {
    return (
      <div  style={{
        width:'100%',
        margin:'0 auto',
        padding:'10px  20px',
        borderBottom: '3px solid rgba(244,245,249,1)',
        display:'flex',
        flexDirection: 'column',
        justifyContent: 'flex-start',
        backgroundColor: this.props.backgroundColor}} >
      <div>
        <h6 className="title is-6"
          style={{ float:'left', textAlign:'left', marginBottom: '5px'}}>
          {this.props.tagCategoryName}
          {this.state.sortAvailable && <i className={classnames(
            'fas',
            {'fa-sort-down': !this.state.sortFlag},
            {'fa-sort-up': this.state.sortFlag})}
            style={{ marginLeft:'3px'}}
            onClick={() => {
              this.setState((prevState) => {
                this.onSort && this.onSort(!prevState.sortFlag);
                return {
                  sortFlag: !prevState.sortFlag,
                };
              });
            }}>
          </i>}
        </h6>
        <span style={{ float:'right', display:this.props.showTrashbin ? 'inline' : 'none'}}>
          <i className="far fa-trash-alt"></i>
        </span>
      </div>
      <div className={classnames('tags')} >
          {this.state.myChildTags.map((child, inx) => {
          return <Tag
            key={child.tagId}
            selected={false}
            selectedColor={this.props.selectedColor}
            onClick={(selected, selectedId) => 
              {
                this.props.onClick(selected, selectedId);
              }} 
            tagId={child.tagId}
            tagName={child.tagName}
            size={this.props.tagSize}
            color={this.props.tagColor}></Tag>; })}
      </div>
    </div>
    );
  }
}
*/