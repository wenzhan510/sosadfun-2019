export class EventBus<EventT> {
  public actions:((e:EventT) => void)[] = [];

  public notify (e:EventT) {
    for (let i = 0; i < this.actions.length; i ++) {
      this.actions[i](e);
    }
  }

  public sub (action:(e:EventT) => void) {
    this.actions.push(action);
  }

  public unsub (action:(e:EventT) => void) {
    this.actions.splice(this.actions.indexOf(action), 1);
  }
}