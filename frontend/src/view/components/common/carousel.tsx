import * as React from 'react';
import { EventBus } from '../../../utils/events';
import './carousel.scss';

interface Props {
  slides:JSX.Element[];
  afterSlides?:JSX.Element[];
  getIndex?:(index:number) => void;
  indicator?:boolean;
  windowResizeEvent:EventBus<void>;
  startIndex?:number;
  showPrev?:boolean;
  showNext?:boolean;
}

interface State {
}

export class Carousel extends React.Component<Props, State> {
  // elements
  public container:HTMLDivElement = document.createElement('div');
  public slider:HTMLDivElement = document.createElement('div');

  // configs
  public duration = 200;
  public easing = 'ease-out';
  public startIndex = this.props.startIndex === undefined ? 0 : this.props.startIndex;
  public draggable = true;
  public threshold = 20;
  public loop = true;

  // dynamic updates
  public slideCount = this.props.slides.length;
  public current = this.startIndex % this.slideCount;
  public lastOffset = 0;
  public startX = 0;
  public endX = 0;
  public mouseDown = false;

  public componentDidMount () {
    this.props.windowResizeEvent.sub(() => this.forceUpdate());
  }

  public componentWillReceiveProps (nextProps:Props) {
    this.slideCount = nextProps.slides.length;
    if (this.props.startIndex !== nextProps.startIndex) {
      this.startIndex = nextProps.startIndex === undefined ? 0 : nextProps.startIndex;
      this.current = this.startIndex % this.slideCount;
    }
    return true;
  }

  public getSlideOffset (index?:number) {
    const i = index === undefined ? this.current : index;
    const width = this.container.offsetWidth;
    return -i * width;
  }

  public translate (offset:number) {
    this.slider.style.transform = `translate3d(${offset}px, 0, 0)`;
  }

  public toggleTransition (enable:boolean) {
    if (enable) {
      this.slider.style.transition = `all ${this.duration}ms ${this.easing}`;
    } else {
      this.slider.style.transition = `all 0ms ${this.easing}`;
    }
  }

  public slideTo (to:number) {
    const from = this.lastOffset;
    const dir = Math.sign(to - from);
    const speed = 20;

    this.toggleTransition(true);

    const step = (dt) => {
      const move = dir * speed;
      this.lastOffset += move;
      this.translate(this.lastOffset);
      if (move * (this.lastOffset - to) > 0) {
        this.lastOffset = to;
        this.translate(this.lastOffset);
        this.forceUpdate();
        if (this.props.getIndex) { this.props.getIndex(this.current); }
      } else {
        requestAnimationFrame(step);
      }
    };
    requestAnimationFrame(step);
  }

  public handleDragStart = (x:number) => {
    this.startX = this.endX = x;
  }

  public handleDrag = (x:number) => {
    this.endX = x;
    this.toggleTransition(false);

    const dx = this.endX - this.startX;
    this.translate(dx + this.lastOffset);
  }

  public handleDragEnd = () => {
    this.toggleTransition(true);
    const dx = this.endX - this.startX;
    this.lastOffset += dx;
    const distance = Math.abs(dx);
    if (distance > 0) {
      if (distance > this.threshold) {
        if (dx > 0) {
          this.prev();
        } else {
          this.next();
        }
      } else {
        const currentSlideOffset = this.getSlideOffset();
        this.slideTo(currentSlideOffset);
      }
    }
  }

  public prev () {
    if (this.current === 0) {
      const currentSlideOffset = this.getSlideOffset();
      this.slideTo(currentSlideOffset);
      return;
    }
    this.current -= 1;
    if (this.props.getIndex) { this.props.getIndex(this.current); }
    const prevSlideOffset = this.getSlideOffset(this.current);
    this.slideTo(prevSlideOffset);
  }

  public next () {
    if (this.current === this.slideCount - 1) {
      const currentSlideOffset = this.getSlideOffset();
      this.slideTo(currentSlideOffset);
      return;
    }
    this.current += 1;
    const nextSlideOffset = this.getSlideOffset();
    this.slideTo(nextSlideOffset);
  }

  public render () {
    return <div className="carousel">
      <div className="slide-wrap">
        <div className="slide-container"
          ref={(el) => el && (this.container = el)}
          onTouchStart={(ev) => {
            ev.stopPropagation();
            ev.touches[0] && this.handleDragStart(ev.touches[0].pageX);
          }}
          onTouchEnd={(ev) => {
            ev.stopPropagation();
            this.handleDragEnd();
          }}
          onTouchMove={(ev) => {
            ev.stopPropagation();
            ev.preventDefault();
            ev.touches[0] && this.handleDrag(ev.touches[0].pageX);
          }}
          onMouseDown={(ev) => {
            ev.preventDefault();
            ev.stopPropagation();
            this.mouseDown = true;
            this.container.style.cursor = '-webkit-grabbing';
            this.handleDragStart(ev.pageX);
          }}
          onMouseUp={(ev) => {
            ev.stopPropagation();
            this.mouseDown = false;
            this.container.style.cursor = '-webkit-grab';
            this.handleDragEnd();
          }}
          onMouseMove={(ev) => {
            if (!this.mouseDown) { return; }
            ev.preventDefault();
            this.handleDrag(ev.pageX);
          }}
          onMouseLeave={(ev) => {
            if (!this.mouseDown) { return; }
            this.mouseDown = false;
            this.container.style.cursor = '-webkit-grab';
            this.endX = ev.pageX;
            this.handleDragEnd();
          }}>

          <div className="slider"
            style={{
              width: `${this.slideCount}00%`,
            }}
            ref={(el) => el && (this.slider = el)}>

            { this.props.slides.map((slide, i) =>
              <div key={i} className="slide">{slide}</div>)
            }
          </div>

        </div>
      </div>

      {this.props.showPrev && <a className="prev" onClick={() => this.prev()}>&#10094;</a>}
      {this.props.showNext && <a className="next" onClick={() => this.next()}>&#10095;</a>}

      { this.props.indicator &&
        <div className="indicator">
          { this.props.slides.map((el, i) =>
            <span key={i}
              className={`dot ${this.current === i && 'active'}`}
              onClick={() => {
                this.current = i;
                this.slideTo(this.getSlideOffset(i));
              }}>
            </span>)
          }
        </div>
      }

      { this.props.afterSlides &&
        <div className="after-slide">
          { this.props.afterSlides }
        </div>
      }

    </div>;
  }
}