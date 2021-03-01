/*
import * as React from 'react';
import { TagBasicList } from './tagbasic-list';

type taglistType = {
  tagCatagoryName:string,
  childTags:{ tagId:string, tagName:string}[],
};

export class TagBasicListSelect extends React.Component<{
  taglist:taglistType[];
  onBack:() => void;
  onFilter:() => void;
  selectedCounter:number;
  onSelect:([]) => void;
}, {

}>  {

  private selectedTags:string[] = [];
  public onClick = (selected:boolean, selectedId:string) => {
    if (this.selectedTags.length === 0){
      this.selectedTags.push(selectedId);
      this.props.onSelect && this.props.onSelect(this.selectedTags);
      return;
    }
    const pos = this.selectedTags.indexOf(selectedId);
    if (!selected && pos >= 0) {
      this.selectedTags.splice(pos, 1);
    }
    if (selected && pos < 0) {
      this.selectedTags.push(selectedId);
    }
    this.props.onSelect && this.props.onSelect(this.selectedTags);
  }

  public render () {
    return <div style={{
      display:'flex',
      flexDirection:'column',
      alignItems:'center' ,
      backgroundColor:'rgba(244,245,249,1)',
      margin:'0',
      padding:'0',
      width:'100%'}} >
      <div style={{
        margin:'0',
        backgroundColor:'white',
        padding: '10px 0',
        width:'100%',
      }}>
        <div style={{
          margin:'0 20px',
          float:'left'}} onClick={this.props.onBack}>
          <i className="fas fa-chevron-left"></i>
          </div>
        <div style={{
          margin:'0px',
          padding:'0px',
          float:'right',
          width:'50px'}} onClick={this.props.onFilter}>
          <i className="fa fa-search i00"></i>
        </div>
        <div style={{
          float:'none',
          width:'auto',
          margin:'0 50px',
          textAlign:'center',
          fontSize:'1.1rem'}}>
            标签列表
        </div>
      { (this.props.selectedCounter > 0) && <div style={{
        textAlign: 'right',
        color:'red',
        padding:'10px 20px',
        backgroundColor:'rgba(244,245,249,1)',
        width:'100%'}}>取消选择({this.props.selectedCounter})</div> }
        {this.props.taglist.map((category) => {
        return  <TagBasicList
        key={category.tagCatagoryName}
        tagCategoryName={category.tagCatagoryName}
        childTags={ category.childTags}
        tagSize={'medium'}
        tagColor={'light'}
        selectedColor={'danger'}
        showTrashbin={false}
        backgroundColor={'white'}
        onClick={(selected, selectedId) => this.onClick(selected,selectedId)}>
      </TagBasicList> ;
      })}
      </div>
    </div>;
  }
}
*/
