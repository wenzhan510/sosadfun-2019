import { API, APIResponse } from './api';
import { loadStorage, saveStorage, Storage, CacheData, allocStorage } from '../utils/storage';

// TODO: CacheHandler is based on filterHandler, refactor the two files later
// 可以在filterHandler的constructor构建时引入cache对象
// private _cache:CacheHandler;
// constructor (cache:CacheHandler) {
//   this._cache = cache;
// }

// TODO: use version nummber to force frontend clear all cache even before expiredTime
class Cache<T> {
  protected db:API;
  protected data:CacheData<T> | null = null;
  private expireTime:number;
  private key:keyof Storage;

  private loadData:() => Promise<T>;

  constructor (
    db:API,
    loadData:() => Promise<T>,
    key:(keyof Storage),
    expireTime:number = 1000 * 3600 * 24,
  ) {
    this.db = db;
    this.loadData = loadData.bind(db);
    this.key = key;
    this.expireTime = expireTime;
  }

  // init is called in "get" for lazy initialization
  public _init () {
    // FIXME: do not use type any
    const data:CacheData<T> = loadStorage(this.key) as any;
    this.data = data;
  }

  private async updateData() {
    if (!this.data) {
      this._init();
    }
    if (this.data && Date.now() - this.data.updated_at > this.expireTime) {
      try {
        const res = await this.loadData();
        this.save(res);
      } catch (e) {
        // console.log(e);
      }
    }
  }

  public async get () : Promise<T> {
    await this.updateData();
    if (this.data) {
      return this.data.data;
    } else {
      // this should never be triggered
      console.error('cache error');
      return new Promise(() => allocStorage()[this.key]);
    }
  }

  public save (data?:T) {
    if (data) {
      const updatedData = {
        updated_at: Date.now(),
        data,
      };
      this.data = updatedData;
    }
    if (this.data != null) {
      saveStorage(this.key, this.data as any);
    }
  }
}

export class FAQCache extends Cache<APIResponse<'getFAQs'>> {
  constructor (db:API) {
    super(
      db,
      db.getFAQs,
      'faq',
    );
  }
}

export class ChannelsCache extends Cache<APIResponse<'getAllChannels'>> {
  constructor (db:API) {
    super (db, db.getAllChannels, 'allChannels');
  }
}