# Startup
第一次拉下代码后需要初始化: (注意当前路径需要在frontend目录中)
```
npm install
```

开发:
```
npm run start
```
之后修改前端代码, 浏览器窗口也会即时刷新.

使用 [storybook](https://github.com/storybooks/storybook) 来测试组件:
```
npm run storybook
```


生产环境打包:
```
npm run build
```

# 需要学习:

- React
- TypeScript
  - Basic Types
  - Variable Declarations
  - Interfaces
  - Classes
  - Functions
  - Enums
  - 进阶: Generics
- React-router 4.0
- ES6 async

# 前端目录结构

- `frontend`
  - `src` 源码目录
  - `dist` 生成后的代码目录, 由webpack生成, 一般需要在ide搜索目录中排除出去
  - `tsconfig.json` ts设置文件
  - `tslint.json` ts lint文件
  - `webpack.config.js` webpack设置文件
  - `yarn.lock` yarn包锁定文件
  - `package.json` node包管理文件
  - `index.html` 
  - `.storybook` storybook配置目录
  - `assets` 媒体文件夹(储存图片等非代码文件)
  - `bin` 脚本文件夹
  - `stories` storybook主要源码目录

- `frontend/src`
  - `config` 设置类, 如网站url, 如path, 等
  - `core` 所有控制组件
    - `index.ts` 负责初始化其他所有控制组件实例并提供一个统一的入口
    - `db.ts` 数据库操作相关
  - `test` 测试代码目录
  - `utils` 其他常用function/class
  - `view` 页面渲染
    - `components` 小块的页面组件, 手机端和电脑端可共用的
    - `mobile` 手机端
      - `router.tsx` 路由文件
      - `navbar.tsx` 一级导航条
      - `login.tsx` 登录/注册等页面
      - `home` 首页页面
      - `collection` 收藏页面
      - `notification` 通知页面
      - `status` 动态页面
      - `user` 用户页面
    - `pc` 电脑端
      - `index.tsx` 初始化、入口文件
      - `content.tsx` 路由文件
    - `index.tsx` 页面组件入口文件, 负责做一些公共(mobile和pc)的初始化处理
  - `index.tsx` 前端入口文件

# 前后端据交互(原ajax)

将想要测试的数据和对应路径添加到`bin/server.js`文件中:  

```js
const config = {
    '/example':  (req) => ({data: 'this is an example msg', code: 1}),
    // 可按照上面示范继续添加测试数据, data下可以放任意数据, 目前code = 1表示数据获取成功, code = 0表示数据获取失败
}
```
以上采用了es6的函数箭头写法, 上面写法等同于:
```js
'/example': function (req) {
  return {
    data: '...',
    code: 1,
  };
}
```

之后开一个新的终端页面开启测试服务器: `npm run server`

前端代码发送和获取数据范例:

```js
const data = await core.db.request('example');
console.log(data); // {data:'this is an example msg', code: 1}
```

为了方便以后修改数据接口, 建议将数据交互添加在`src/core`目录的对应文件中, 添加新的方法来处理数据, 在react component中只调用该方法来获得返回数据. 具体可以参考login和register页面的写法.

# 备注
- 目前代码都在`mobile`目录里, 我们优先开发手机端界面
- 非页面交互方面的逻辑功能, 建议在`core`目录下建议相应的类来处理, 比如`core`下`user`类负责管理所有用户数据.
- 页面分为两部分, 上面的部分由路由控制切换页面, 具体的页面内容需要包裹在`<Page>`标签下(参考`/src/view/mobile/home/default.tsx`). 下面的是一级导航菜单, 对应的按钮分别跳转到对应的mobile主目录.
- 每一个可以考虑同时插入在手机端和pc端的组件, 需要包裹在`<Card>`标签下, 并放在`src/view/components`目录内(参考`src/view/components`里的各文件).
- `src/view/components`目录内的components仅负责纯粹的UI渲染和操作, 不做其他逻辑, 这里的props请尽量放纯粹的数据或回调函数, 不要放`core`对象.
- 使用率较高的简单组件, 可以自行创建并放在`src/view/components/common.tsx`文件内.
- UI方面的调试建议使用storybook