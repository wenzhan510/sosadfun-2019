import { API } from './api';
import { loadStorage, saveStorage, FilterDataType } from '../utils/storage';
import { DB } from '../config/db-type';

const EXPIRE_TIME_MS = 1000 * 3600 * 24;

class Filter<T> {
  protected _db:API;
  protected _selectedList:number[] = [];
  protected _list:T[] = [];

  private _saveData:(data:FilterDataType<T>) => void;
  private _loadData:() => Promise<FilterDataType<T>>;

  constructor (
    db:API,
    loadData:() => Promise<FilterDataType<T>>,
    saveData:(data:FilterDataType<T>) => void,
  ) {
    this._db = db;
    this._saveData = saveData;
    this._loadData = loadData;
  }

  public async init () {
    const data = await this._loadData();
    this._selectedList = data.selectedList;
    this._list = data.list;
  }

  public get (id:number) {
    return this._list[id - 1];
  }

  public select (id:number) {
    const idx = this._selectedList.indexOf(id);
    if (idx < 0) {
      this._selectedList.push(id);
    } else {
      this._selectedList.splice(idx, 1);
    }
    this._saveData({
      updated_at: Date.now(),
      list: this._list,
      selectedList: this._selectedList,
    });
  }

  public isSelected (id:number) {
    return this._selectedList.indexOf(id) >= 0;
  }

  public getSelectedList () {
    return this._selectedList.slice();
  }

  public save () {
    this._saveData({
      updated_at: Date.now(),
      list: this._list,
      selectedList: this._selectedList,
    });
  }
}

export class TagFilter extends Filter<DB.Tag> {
  private _types:{[type:string]:DB.Tag[]} = {};

  constructor (db:API) {
    super(
      db,
      async () => {
        const data = loadStorage('tagFilter');
        if (data.updated_at - Date.now() > EXPIRE_TIME_MS || !data.list.length) {
          const res = await db.getAllTags();
          const updatedData:FilterDataType<DB.Tag> = {
            updated_at: Date.now(),
            list: res.tags,
            selectedList: data.selectedList,
          };
          saveStorage('tagFilter', updatedData);
          return updatedData;
        }
        return data;
      },
      (data) => {
        saveStorage('tagFilter', data);
      },
    );
  }

  private _parseTypes = () => {
    const types = this._types;
    this._list.forEach((tag) => {
      if (!types[tag.attributes.tag_type]) {
        types[tag.attributes.tag_type] = [];
      }
      types[tag.attributes.tag_type].push(tag);
    });
  }

  public getAllTagTypes () {
    const keys = Object.keys(this._types);
    if (!keys.length) {
      this._parseTypes();
    }
    return Object.keys(this._types);
  }
}

export class ChannelFilter extends Filter<DB.Channel> {
  constructor (db:API) {
    super (
      db,
      async () => {
        const data = loadStorage('channelFilter');
        if (data.updated_at - Date.now() > EXPIRE_TIME_MS) {
          const res = await db.getAllChannels();
          const updatedData = {
            updated_at: Date.now(),
            list: Object.values(res),
            selectedList: data.selectedList,
          };
          saveStorage('channelFilter', updatedData);
          return updatedData;
        }
        return data;
      },
      (data) => {
        saveStorage('channelFilter', data);
      },
    );
  }
}

export class BianyuanFilter extends Filter<{id:number, name:string}> {
  constructor (db:API) {
    super (
      db,
      async () => {
        return loadStorage('bianyuanFilter');
      },
      (data) => {
        saveStorage('bianyuanFilter', data);
      },
    );
  }
}