import * as React from 'react';
import { RouteMenu } from '../components/common/route-menu';
import { RoutePath } from '../../config/route-path';
import './main-menu.scss';

type MenuItem = {
  to:RoutePath,
  label:string,
  icon:string,
  defaultColor:string,
  selectedColor:string,
};

export function MainMenu () {
  const items:MenuItem[] = [
    {to: RoutePath.home, label: '首页', icon: 'fas fa-home', defaultColor:'black', selectedColor:'red'},
    {to: RoutePath.threadHome, label: '论坛', icon: 'fas fa-comments', defaultColor:'black', selectedColor:'red'},
    {to: RoutePath.status, label: '动态', icon: 'far fa-compass', defaultColor:'black', selectedColor:'red'},
    {to: RoutePath.collection, label: '收藏', icon: 'far fa-star', defaultColor:'black', selectedColor:'red'},
    {to: RoutePath.user, label: '我的', icon: 'far fa-user', defaultColor:'black', selectedColor:'red'},
  ];
  let onIndex = 0;
  for (let i = 0; i < items.length; i ++) {
    if (location.pathname === items[i].to) {
      onIndex = i;
    }
  }

  return <RouteMenu
    onIndex={onIndex}
    items={items}
    className="route-menu"
  />;
}