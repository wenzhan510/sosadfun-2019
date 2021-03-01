import { Timestamp } from '../config/db-type';

export function parseDate (date?:Timestamp) {
  if (!date) { return ''; }
  // fixme: 后端把需要paser的timestring已经parse成'xx天/小时前'的格式了
  // 其他timestring往往是需要直接print time string的.. 我想了想就先直接return 吧
  return date;
}

export function isNewThread (date?:Timestamp) {
  if (!date) { return false; }
  return (new Date(date)).getTime() - (Date.now()) <= 1000 * 3600 * 24;
}