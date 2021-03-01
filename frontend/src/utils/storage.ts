import { Themes } from '../view/theme/theme';
import { DB } from '../config/db-type';
import { APIResponse } from '../core/api';

export type FontType = 'simplifiedChinese'|'traditionalChinese';

type Timestamp = number;
export type FilterDataType<T> = {
  updated_at:number;
  list:T[];
  selectedList:number[];
};

export type CacheData<T> = {
  updated_at:number;
  data:T;
};

export interface Storage {
  auth:{
    token:string,
    username:string,
    userId:number,
  };
  theme:Themes;
  tagFilter:FilterDataType<DB.Tag>;
  channelFilter:FilterDataType<DB.Channel>;
  bianyuanFilter:FilterDataType<{id:number, name:string}>;
  readingSettings:{
    fontSize:number;
    fontType:FontType;
  };
  faq:CacheData<APIResponse<'getFAQs'>>;
  allChannels:CacheData<APIResponse<'getAllChannels'>>;
}

export function allocStorage () : Storage {
  return {
    auth:{
      token:'',
      username:'',
      userId:-1,
    },
    theme: Themes.light,
    tagFilter: {
      updated_at: 0,
      list: [],
      selectedList: [],
    },
    channelFilter: {
      updated_at: 0,
      list: [],
      selectedList: [],
    },
    bianyuanFilter: {
      updated_at: 0,
      list: [],
      selectedList: [],
    },
    readingSettings: {
      fontSize: 14,
      fontType: 'simplifiedChinese',
    },
    faq: {
      updated_at: 0,
      data: [],
    },
    allChannels: { updated_at: 0, data: {} },
  };
}

export function saveStorage<K extends keyof Storage> (key:K, value:Storage[K]) {
  try {
    localStorage.setItem(key, JSON.stringify(value));
  } catch (e) {
    console.error(`saving storage failed ${key}:${value}`);
  }
}

export function loadStorage<K extends keyof Storage> (key:K) : Storage[K] {
  try {
    const data = localStorage.getItem(key);
    if (data) {
      const res = JSON.parse(data);
      return res;
    }
  } catch (e) {
    console.error('load storage failed with key ' + key);
  }
  return allocStorage()[key];
}

export function clearStorage<K extends keyof Storage> (key:K) {
  const clearState = allocStorage()[key];
  saveStorage(key, clearState);
}