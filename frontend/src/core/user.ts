import { loadStorage, clearStorage } from '../utils/storage';
import { History } from 'history';

export class User {
  private history:History;

  private isLogin = false;
  public name = '';
  public id = -1;
  public token = '';

  constructor (history:History) {
    this.history = history;

    const auth = loadStorage('auth');
    if (auth.userId != -1) {
      this.isLogin = true;
      this.name = auth.username;
      this.id = auth.userId;
      this.token = auth.token;
    }
  }

  public login(name:string, id:number, token:string) {
    this.isLogin = true;
    this.name = name;
    this.id = id;
    this.token = token;
  }

  public isAdmin () : boolean {
    // fixme:
    return true;
  }

  public isLoggedIn () : boolean {
    return this.isLogin;
  }

  public logout () {
    clearStorage('auth');
    this.isLogin = false;
    this.name = '';
    this.id = -1;
    this.token = '';
    this.history.push('/');
  }
}