/*
import * as React from 'react';
import { TagBasicList } from './tagbasic-list';

export class TagBasicListFilter extends React.Component<{
  taglist:{
    tagCategoryName:string,
    categoryTrash:boolean,
    childTags:{ tagId:string, tagName:string}[],
  }[];
  onBack:() => void;
  onFilter:(filCriteria:string) => void;
  onDelete:([]) => void;
}, {
  searchCondtion:string;
}>  {

  private selectedTags:string[] = [];
  public onDelete = (selected:boolean, selectedId:string) => {
    if (this.selectedTags.length === 0) {
      this.selectedTags.push(selectedId);
      this.props.onDelete && this.props.onDelete(this.selectedTags);
      return;
    }
    const pos = this.selectedTags.indexOf(selectedId);
    if (!selected && pos >= 0) {
      this.selectedTags.splice(pos, 1);
    }
    if (selected && pos < 0) {
      this.selectedTags.push(selectedId);
    }
    this.props.onDelete && this.props.onDelete(this.selectedTags);
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
        backgroundColor:'rgba(244,245,249,1)',
        padding: '10px 0',
        width:'100%',
      }}>
        <div style={{
          width:'100%',
          margin:'0 auto',
          padding:'10px  20px',
          display:'flex',
          flexDirection:'row',
          flexWrap:'nowrap',
          justifyContent:'space-around',
          alignContent: 'center',
        }}>

        <div style={{
            flex:'1',
            backgroundColor: 'white',
            marginRight:'20px',
            borderRadius: '15px',
        }}>
          <i className="fas fa-search" style={{margin:'0 5px 0 10px'}}></i>
          <input type="text" placeholder=""
          style={window.screen.width >= 400 ? {
            border:'none', margin:'0', padding:'0', textAlign: 'left',
            fontSize:'1rem', outline:'none', width:'90%'}:{
              border:'none', margin:'0', padding:'0', textAlign: 'left',
              fontSize:'1rem', outline:'none', width:'85%'}}
            onChange={(ev) => {
              console.log(ev.target.value);
              this.setState({searchCondtion: ev.target.value});
            }}
            onKeyDown={(ev) => {
              // 如果按下回车键&&值不为空,
              if (ev.keyCode === 13 && this.state.searchCondtion) {
                this.props.onFilter(this.state.searchCondtion);
              }
            }}
            />
        </div>
        <div onClick={() => this.props.onBack }>取消</div>
      </div>
        {this.props.taglist.map((category) => {
        return  <TagBasicList
          key={category.tagCategoryName}
          tagCategoryName={category.tagCategoryName}
          childTags={ category.childTags}
          tagSize={'normal'}
          tagColor={'white'}
          selectedColor={'danger'}
          showTrashbin={category.categoryTrash}
          backgroundColor={'rgba(244,245,249,1)'}
          onClick={(selected, selectedId) => selected && this.onDelete(selected, selectedId)}>
      </TagBasicList> ;
      })}
      </div>
    </div>;
  }
}
*/