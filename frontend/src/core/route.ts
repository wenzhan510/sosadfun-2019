import { History } from 'history';

export class Route {
  constructor (
    private _history:History,
  ) { }

  public go (path:string) {
    this._history.push(path);
    this._history.goForward();
  }

  public back = () => {
    if (this._history.length <= 2) {
      this.go('/');
    } else {
      this._history.goBack();
    }
  }

  public channelTag (channelId:number, tagId:number) {
    this.go(`/threads/?channels=[${channelId}]&tags=[${tagId}]`);
  }
  public thread (threadId:number) {
    this.go(`/thread/${threadId}`);
  }

  public user (userId:number) {
    this.go(`/user/${userId}`);
  }

  public book (threadId:number) {
    this.go(`/book/${threadId}`);
  }

  public chapter (threadId:number, chapterId:number) {
    this.go(`/book/${threadId}/chapter/${chapterId}`);
  }

  public channel (channelId:number, tagId = 0) {
    this.go(`/channel/${channelId}/tag/${tagId}`);
  }
}