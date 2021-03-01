// generated from bin/parse-selector-php.js
export namespace RequestFilter {
  export namespace thread {
    export enum isPublic {
      include_private = 'include_private', // 包含未公开
      private_only = 'private_only', // 只看未公开
    }
    export enum ordered {
      default = 'default', // 最后回复
      latest_add_component = 'latest_add_component', // 最新更新
      total_char = 'total_char', // 总字数
      jifen = 'jifen', // 总积分
      weighted_jifen = 'weighted_jifen', // 均字数积分
      latest_created = 'latest_created', // 最新创建
      collection_count = 'collection_count', // 最多收藏
      random = 'random', // 随机乱序
    }
    export enum withBianyuan {
      include_bianyuan = 'include_bianyuan', // 包含边限
      bianyuan_only = 'bianyuan_only', // 只看边限
    }
  }

  export namespace book {
    export enum inChannel {
      yuanchuang = '1', // 原创小说
      tongren = '2', // 同人小说
    }
    export enum ordered {
      default = 'default', // 最后回复
      latest_add_component = 'latest_add_component', // 最新更新
      total_char = 'total_char', // 总字数
      jifen = 'jifen', // 总积分
      weighted_jifen = 'weighted_jifen', // 均字数积分
      latest_created = 'latest_created', // 最新创建
      collection_count = 'collection_count', // 最多收藏
      random = 'random', // 随机乱序
    }
  }

  export namespace list {
    export enum withType {
      post = 'post', // 只看回帖
      review = 'review', // 只看书评
    }
    export enum withComponent {
      component_only = 'component_only', // 只显示书评
      post_N_comment = 'post_N_comment', // 只显示回帖和点评
      include_comment = 'include_comment', // 显示点评
    }
    export enum withFolded {
      include_folded = 'include_folded', // 显示折叠内容
    }
    export enum ordered {
      default = 'default', // 最早发布
      latest_created = 'latest_created', // 最新发布
      most_upvoted = 'most_upvoted', // 最高赞
    }
  }

  export namespace box {
    export enum withType {
      post = 'post', // 只看回帖
      question = 'question', // 只看提问
      answer = 'answer', // 只看回答
    }
    export enum withComponent {
      component_only = 'component_only', // 只显示问
      post_N_comment = 'post_N_comment', // 只显示回帖和点评
      include_comment = 'include_comment', // 显示点评
    }
    export enum withFolded {
      include_folded = 'include_folded', // 显示折叠内容
    }
    export enum ordered {
      default = 'default', // 时间顺序
      latest_created = 'latest_created', // 最新发布
      most_upvoted = 'most_upvoted', // 最高赞
    }
  }

  export namespace homework {
    export enum withType {
      post = 'post', // 回帖
      work = 'work', // 作业正文
      critique = 'critique', // 作业批评
    }
    export enum withComponent {
      include_comment = 'include_comment', // 显示点评
    }
    export enum withFolded {
      include_folded = 'include_folded', // 显示折叠内容
    }
    export enum ordered {
      default = 'default', // 最早回复
      latest_created = 'latest_created', // 最新发布
      most_upvoted = 'most_upvoted', // 最高赞
    }
  }

  export namespace collection {
    export enum order_by {
      collect = '0', // 最新收藏
      reply = '1', // 最新回复
      chapter = '2', // 最新章节
      created = '3', // 最新创立
    }
  }

  export namespace review {
    export enum reviewType {
      all = 'all', // 全部书评
      sosad_only = 'sosad_only', // 站内文评
      none_sosad_only = 'none_sosad_only', // 非站内文评
    }
    export enum withLength {
      short = 'short', // 短评
      medium = 'medium', // 中评
      long = 'long', // 长评
      no_limit = 'no_limit', // 不限长度
    }
    export enum reviewRecommend {
      recommend_only = 'recommend_only', // 推荐
      none_recommend_only = 'none_recommend_only', // 未推荐
    }
    export enum reviewEditor {
      editor_only = 'editor_only', // 编推
      none_editor_only = 'none_editor_only', // 非编推
    }
    export enum ordered {
      default = 'default', // 最早回复
      latest_created = 'latest_created', // 最新发布
      most_upvoted = 'most_upvoted', // 最高赞
    }
  }

}

export namespace RequestFilterText {
  export namespace thread {
    export const isPublic:{[name in RequestFilter.thread.isPublic]:string} = {
      [RequestFilter.thread.isPublic.include_private]: '包含未公开',
      [RequestFilter.thread.isPublic.private_only]: '只看未公开',
    };
    export const ordered:{[name in RequestFilter.thread.ordered]:string} = {
      [RequestFilter.thread.ordered.default]: '最后回复',
      [RequestFilter.thread.ordered.latest_add_component]: '最新更新',
      [RequestFilter.thread.ordered.total_char]: '总字数',
      [RequestFilter.thread.ordered.jifen]: '总积分',
      [RequestFilter.thread.ordered.weighted_jifen]: '均字数积分',
      [RequestFilter.thread.ordered.latest_created]: '最新创建',
      [RequestFilter.thread.ordered.collection_count]: '最多收藏',
      [RequestFilter.thread.ordered.random]: '随机乱序',
    };
    export const withBianyuan:{[name in RequestFilter.thread.withBianyuan]:string} = {
      [RequestFilter.thread.withBianyuan.include_bianyuan]: '包含边限',
      [RequestFilter.thread.withBianyuan.bianyuan_only]: '只看边限',
    };
  }

  export namespace book {
    export const inChannel:{[name in RequestFilter.book.inChannel]:string} = {
      [RequestFilter.book.inChannel.yuanchuang]: '原创小说',
      [RequestFilter.book.inChannel.tongren]: '同人小说',
    };
    export const ordered:{[name in RequestFilter.book.ordered]:string} = {
      [RequestFilter.book.ordered.default]: '最后回复',
      [RequestFilter.book.ordered.latest_add_component]: '最新更新',
      [RequestFilter.book.ordered.total_char]: '总字数',
      [RequestFilter.book.ordered.jifen]: '总积分',
      [RequestFilter.book.ordered.weighted_jifen]: '均字数积分',
      [RequestFilter.book.ordered.latest_created]: '最新创建',
      [RequestFilter.book.ordered.collection_count]: '最多收藏',
      [RequestFilter.book.ordered.random]: '随机乱序',
    };
  }

  export namespace list {
    export const withType:{[name in RequestFilter.list.withType]:string} = {
      [RequestFilter.list.withType.post]: '只看回帖',
      [RequestFilter.list.withType.review]: '只看书评',
    };
    export const withComponent:{[name in RequestFilter.list.withComponent]:string} = {
      [RequestFilter.list.withComponent.component_only]: '只显示书评',
      [RequestFilter.list.withComponent.post_N_comment]: '只显示回帖和点评',
      [RequestFilter.list.withComponent.include_comment]: '显示点评',
    };
    export const withFolded:{[name in RequestFilter.list.withFolded]:string} = {
      [RequestFilter.list.withFolded.include_folded]: '显示折叠内容',
    };
    export const ordered:{[name in RequestFilter.list.ordered]:string} = {
      [RequestFilter.list.ordered.default]: '最早发布',
      [RequestFilter.list.ordered.latest_created]: '最新发布',
      [RequestFilter.list.ordered.most_upvoted]: '最高赞',
    };
  }

  export namespace box {
    export const withType:{[name in RequestFilter.box.withType]:string} = {
      [RequestFilter.box.withType.post]: '只看回帖',
      [RequestFilter.box.withType.question]: '只看提问',
      [RequestFilter.box.withType.answer]: '只看回答',
    };
    export const withComponent:{[name in RequestFilter.box.withComponent]:string} = {
      [RequestFilter.box.withComponent.component_only]: '只显示问',
      [RequestFilter.box.withComponent.post_N_comment]: '只显示回帖和点评',
      [RequestFilter.box.withComponent.include_comment]: '显示点评',
    };
    export const withFolded:{[name in RequestFilter.box.withFolded]:string} = {
      [RequestFilter.box.withFolded.include_folded]: '显示折叠内容',
    };
    export const ordered:{[name in RequestFilter.box.ordered]:string} = {
      [RequestFilter.box.ordered.default]: '时间顺序',
      [RequestFilter.box.ordered.latest_created]: '最新发布',
      [RequestFilter.box.ordered.most_upvoted]: '最高赞',
    };
  }

  export namespace homework {
    export const withType:{[name in RequestFilter.homework.withType]:string} = {
      [RequestFilter.homework.withType.post]: '回帖',
      [RequestFilter.homework.withType.work]: '作业正文',
      [RequestFilter.homework.withType.critique]: '作业批评',
    };
    export const withComponent:{[name in RequestFilter.homework.withComponent]:string} = {
      [RequestFilter.homework.withComponent.include_comment]: '显示点评',
    };
    export const withFolded:{[name in RequestFilter.homework.withFolded]:string} = {
      [RequestFilter.homework.withFolded.include_folded]: '显示折叠内容',
    };
    export const ordered:{[name in RequestFilter.homework.ordered]:string} = {
      [RequestFilter.homework.ordered.default]: '最早回复',
      [RequestFilter.homework.ordered.latest_created]: '最新发布',
      [RequestFilter.homework.ordered.most_upvoted]: '最高赞',
    };
  }

  export namespace collection {
    export const order_by:{[name in RequestFilter.collection.order_by]:string} = {
      [RequestFilter.collection.order_by.collect]: '最新收藏',
      [RequestFilter.collection.order_by.reply]: '最新回复',
      [RequestFilter.collection.order_by.chapter]: '最新章节',
      [RequestFilter.collection.order_by.created]: '最新创立',
    };
  }

  export namespace review {
    export const reviewType:{[name in RequestFilter.review.reviewType]:string} = {
      [RequestFilter.review.reviewType.all]: '全部书评',
      [RequestFilter.review.reviewType.sosad_only]: '站内文评',
      [RequestFilter.review.reviewType.none_sosad_only]: '非站内文评',
    };
    export const withLength:{[name in RequestFilter.review.withLength]:string} = {
      [RequestFilter.review.withLength.short]: '短评',
      [RequestFilter.review.withLength.medium]: '中评',
      [RequestFilter.review.withLength.long]: '长评',
      [RequestFilter.review.withLength.no_limit]: '不限长度',
    };
    export const reviewRecommend:{[name in RequestFilter.review.reviewRecommend]:string} = {
      [RequestFilter.review.reviewRecommend.recommend_only]: '推荐',
      [RequestFilter.review.reviewRecommend.none_recommend_only]: '未推荐',
    };
    export const reviewEditor:{[name in RequestFilter.review.reviewEditor]:string} = {
      [RequestFilter.review.reviewEditor.editor_only]: '编推',
      [RequestFilter.review.reviewEditor.none_editor_only]: '非编推',
    };
    export const ordered:{[name in RequestFilter.review.ordered]:string} = {
      [RequestFilter.review.ordered.default]: '最早回复',
      [RequestFilter.review.ordered.latest_created]: '最新发布',
      [RequestFilter.review.ordered.most_upvoted]: '最高赞',
    };
  }

}