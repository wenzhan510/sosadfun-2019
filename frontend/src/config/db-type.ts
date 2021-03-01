import { DBTable } from './db-table';

export type Timestamp = string;
export type Token = string;
export type UInt = number;
export type Increments = number;

export namespace DB {
  export interface Quote {
    type:'quote';
    id:number;
    attributes:{
      body:string;
      user_id?:Increments;
      is_anonymous?:boolean;
      majia?:string;
      not_sad?:boolean;
      is_approved?:boolean;
      reviewer_id?:Increments;
      fish?:number;
      created_at?:Timestamp;
    };
    author:User;
  }

  export function allocQuote () {
    return {
      type: 'quote',
      id: 0,
      attributes: {
        body: '',
      },
      author: allocUser(),
    };
  }

  export interface User {
    type:'user';
    id:number;
    attributes:DBTable.UserDefault;
    followInfo?:{
      keep_updated:boolean;
      is_updated:boolean;
    };
  }

  export function allocUser () : User {
    return {
      type: 'user',
      id: 0,
      attributes: {
        name: '',
      },
    };
  }

  export interface Channel {
    type:'channel';
    id:number;
    attributes:DBTable.Channel;
  }

  export function allocChannel () : Channel {
    return {
      type: 'channel',
      id: 0,
      attributes: {
        channel_name: '',
        channel_type: '',
      },
    };
  }

  export interface ChannelBrief {
    id:number;
    channel_name:string;
    channel_explanation:string;
    order_by:string;
    type:'thread';
    allow_anonymous:boolean;
    allow_edit:boolean;
    allow_deletion:boolean;
    is_public:boolean;
    show_on_homepage:boolean;
  }

  export function allocChannelBrief () : ChannelBrief {
    return {
      type: 'thread',
      id: 0,
      channel_name: '',
      channel_explanation: '',
      order_by: '',
      allow_anonymous: true,
      allow_edit: true,
      allow_deletion: true,
      is_public: true,
      show_on_homepage: false,
    };
  }

  export interface Tongren {
    id:number;
    type:'tongren';
    attributes:{
      thread_id:number;
      tongren_yuanzhu:string;
      tongren_CP:string;
    };
  }


  export function allocTongren () : Tongren {
    return {
      id: 0,
      type: 'tongren',
      attributes: {
        thread_id: 0,
        tongren_yuanzhu: '',
        tongren_CP: '',
      },
    };
  }

  export interface Thread {
    type:'thread';
    id:number;
    attributes:DBTable.Thread;
    author:User;
    tags:Tag[];
    last_component?:Post;
    last_post?:Post;
    component_index_brief:Post[];
    recent_rewards:Reward[];
    random_review:Post[];
    tongren:Tongren[];
  }

  export function allocThread () : Thread {
    return {
      type: 'thread',
      id: 0,
      attributes: {
        title: '',
        channel_id: 0,
      },
      author: allocUser(),
      component_index_brief: [],
      recent_rewards: [],
      random_review: [],
      tongren: [],
      tags: [],
    };
  }

  export interface Status {
    type:'status';
    id:number;
    attributes:DBTable.Status;
    author:User;
    attachable:Post | Thread | Status | null;
    parent?:Status;
    last_reply:null | Status;
    replies?:Status[];
    recent_rewards:Reward[];
    recent_upvotes:Vote[];
  }

  export interface Tag {
    type:'tag';
    id:number;
    attributes:DBTable.Tag;
  }

  export interface ThreadPaginate {
    total:number;
    count:number;
    per_page:number;
    current_page:number;
    total_pages:number;
  }

  export function allocThreadPaginate () : ThreadPaginate {
    return {
      total: 1,
      count: 1,
      per_page: 1,
      current_page: 1,
      total_pages: 1,
    };
  }

  export interface PostInfo {
    type:'post_info';
    id:number;
    attributes:DBTable.PostInfo;
    reviewee:Thread;
  }

  export function allocPostInfo () : PostInfo {
    return {
      type: 'post_info',
      id: 0,
      attributes: {
        order_by: 0,
        abstract: '',
        previous_id: 0,
        next_id: 0,
        reviewee_id: 0,
        reviewee_type: 'thread',
        rating: 0,
        redirect_count: 0,
        author_attitude: 0,
        summary: '',
        annotation: null,
        warning: '',
      },
      reviewee: allocThread(),
    };
  }

  export interface Post {
    type:'post';
    id:number;
    attributes:DBTable.Post;
    author:User;
    info:PostInfo;
    parent:Post[];
    last_reply:null|Post;
    recent_rewards:Reward[];
    recent_upvotes:Post[];
    new_replies:Post[];
    thread?:Thread;
  }

  export function allocPost () : Post {
    return {
      type: 'post',
      id: 0,
      attributes: {
        body: '',
      },
      info: allocPostInfo(),
      parent: [],
      author: allocUser(),
      last_reply: null,
      recent_upvotes: [],
      recent_rewards: [],
      new_replies: [],
    };
  }

  export interface Review {
    type:'review';
    id:number;
    attributes:{};
    reviewee:DBTable.Thread;
  }

  export function allocReview () {
    return {
      type: 'review',
      id: 0,
      attributes: {},
      reviewee: allocThread(),
    };
  }

  export interface Recommendation {
    type:'recommendation';
    id:number;
    attributes:{
      brief:string;
      body:string;
      type:'long'|'shot';
      created_at:DBTable.Timestamp;
    };
    authors:User[];
  }

  export interface Chapter {
    type:'chapter';
    id:number;
    attributes:DBTable.Chapter;
  }

  export function allocChapter () : Chapter {
    return {
      type: 'chapter',
      id: 0,
      attributes: {

      },
    };
  }

  export interface Volumn {
    type:'volumn';
    id:number;
    attributes:DBTable.Volume;
  }

  export interface Date {
    date:Timestamp;
    timezone_type:number;
    timezone:string;
  }

  export interface Activity {
    type:'activity';
    id:number;
    attributes:{
      kind:number;
      seen:boolean;
      item_id:number;
      item_type:string;
      user_id:number;
    };
    item:Post | Status | Quote | Thread;
    author?:User;
  }

  export interface Message {
    type:'message';
    id:number;
    attributes:{
      poster_id:number;
      receiver_id:number;
      body_id:number;
      created_at:Timestamp;
      seen:boolean;
    };
    poster?:User;
    message_body?:MessageBody;
    receiver?:User;
  }

  export interface MessageBody {
    type:'message_body';
    id:number;
    attributes:{
      body:string;
      bulk:boolean;
    };
  }

  export function allocMessage () : Message {
    return {
      type: 'message',
      id: 0,
      attributes: {
        poster_id: 0,
        receiver_id: 0,
        body_id: 0,
        created_at: '',
        seen: false,
      },
      poster: allocUser(),
      receiver: allocUser(),
      message_body: allocMessageBody(),
    };
  }

  export function allocMessageBody () : MessageBody {
    return {
      type: 'message_body',
      id: 0,
      attributes: {
          body: '',
          bulk: false,
      },
    };
  }
  export interface PublicNotice {
    type:'public_notice';
    id:number;
    attributes:{
      user_id:number;
      title:string;
      body:string;
      created_at:Timestamp;
      edited_at:Timestamp;
    };
    author?:User;
  }
  export function allocPublicNotice () : PublicNotice {
    return {
      type: 'public_notice',
      id: 0,
      attributes: {
        user_id: 0,
        title: '',
        body: '',
        created_at: '',
        edited_at: '',
      },
      author: allocUser(),
    };
  }

  export interface Title {
    type:'title';
    id:number;
    attributes:{
      name:string;
      description:string;
      user_count:number;
      style_id:number;
      type:string;
      level:number;
      style_type:string;
    };
  }
  export function allocTitle () : Title {
    return {
      type: 'title',
      id: 0,
      attributes: {
        name: '',
        description: '',
        user_count: 0,
        style_id:0,
        type:'',
        level:0,
        style_type:'',
      },
    };
  }
  export type VoteType = 'Post'|'Quote'|'Status'|'Thread';
  export type VoteAttribute = 'upvote'|'downvote'|'funnyvote'|'foldvote';
  export interface Vote {
    type:'vote';
    id:number;
    attributes:{
      votable_type:VoteType;
      votable_id:number;
      attitude:VoteAttribute;
      created_at:Timestamp;
    };
    author?:User;
    receiver?:User;
    votable?:null | DB.Thread | DB.Post | DB.Status | DB.Quote;
  }

  export type RewardType = 'salt'|'fish'|'ham';
  export type RewardableType = 'post'|'status'|'thread'|'quote';
  export interface Reward {
    type:'reward';
    id:number;
    attributes:{
      rewardable_type:RewardableType;
      rewardable_id:number;
      reward_type:RewardType;
      reward_value:number;
      created_at:Timestamp;
      deleted_at:Timestamp;
    };
    author?:User;   //available at reward_received
    receiver?:User; //available at reward_sent
    rewardable?:null | DB.Thread | DB.Post | DB.Status | DB.Quote;
  }
  export function allocReward () : Reward {
    return {
      id: 0,
      type: 'reward',
      attributes: {
        rewardable_type:'post',
        rewardable_id: 0,
        reward_value: 0,
        reward_type: 'fish',
        created_at: '',
        deleted_at: '',
      },
      author: allocUser(),
    };
  }

  export interface Collection {
    type:'collection';
    id:number;
    attributes:{
      user_id:number;
      thread_id:number;
      keep_updated:boolean;
      updated:boolean;
      group_id:number;
      last_read_post_id:number;
    };
  }
  export interface CollectionGroup {
    type:'collection_group';
    id:number;
    attributes:{
      user_id:number;
      name:string;
      update_count:number;
      order_by:number;
    };
  }

  export interface BriefHomework {
    type:'homework';
    id:number;
    attributes:{
      title:string;
      topic:string;
      level:number;
      is_active:boolean;
      purchase_count:number;
      worker_count:number;
      critic_count:number;
    };
  }

  export interface FAQ {
    type:'faq';
    id:number;
    attributes:{
      key:string;
      question:string;
      answer:string;
    };
  }

  // registration
  export interface QuizQuestion {
    type:'quiz';
    id:number;
    attributes:{
      body:string;
      hint:string;
      options:QuizQuestionOption[];
    };
  }
  export interface QuizQuestionOption {
    type:'quiz_option';
    id:number;
    attributes:{
      body:string;
    };
  }
  export interface Essay {
    type:'essay';
    id:number;
    attributes:{
      body:string,
      hint:string,
    };
  }
  export function allocEssay() : Essay {
    return {
      type:'essay',
      id:0,
      attributes:{
        body:'',
        hint:'',
      },
    };
  }
  export interface RegistrationApplication {
    type:'registration_application';
    id:number;
    attributes:{
      email:string,
      has_quizzed:boolean,
      email_verified_at:Timestamp,
      submitted_at:Timestamp,
      is_passed:boolean,
      last_invited_at:Timestamp,
      is_in_cooldown:boolean,
    };
  }
  export function allocRegistrationApplication () : RegistrationApplication {
    return {
      type: 'registration_application',
      id: 0,
      attributes: {
        email:'',
        has_quizzed:false,
        email_verified_at:'',
        submitted_at:'',
        is_passed:false,
        last_invited_at:'',
        is_in_cooldown:false,
      },
    };
  }

  export interface ChannelPrimaryTag {
    id:number;
    tag_name:string;
    tag_explanation?:string;
    tag_type?:string;
    is_bianyuan?:boolean;
    is_primary?:boolean;
    channel_id?:number;
    parent_id?:number;
    thread_count?:number;
    created_at?:Timestamp;
    deleted_at?:Timestamp;
  }
}