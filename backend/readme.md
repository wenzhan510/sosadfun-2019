# Laravel-sosad 后端运行指南
## 1.序言
本文档是为用户理解和使用backend文件夹中的所有内容所准备的。backend的主要任务，是提供一套方便使用的api。本文档内容包含安装配置，数据库表格设计的解释，以及api各个指令的属性、所需数据的格式、返回结果的格式。
### 特别注意事项
前后端分离之后，所有laravel相关的指令，比如`php artisan migrate`，或者composer相关指令，比如`composer update`，都应进入backend文件夹之后操作，否则会报错。本文档中所有“根目录”，如未特别说明，指的都是居于backend文件夹内部的这个地址。

## 2.安装配置
### 2.1 composer更新组件
第一次使用时，运行
```
$ composer update
```
补完需要加载的框架和package文件。
### 2.2 书写和加载`.env`文件中数据库配置

laravel后端中所有环境变量（基本配置比如数据库地址、用户名、密码，和大部分不应被git上传的安全相关的文件比如加密所需的key）都存放在居于最外面的`.env`文件。laravel已经准备了范例，只需在范例基础上稍作加工，就可以得到自己的这个文件。将backend根目录下`.env.example`改写为`.env`，补完其中关于database信息的内容。  
其中环境变量`DB_DATABASE`应指向空白的本地mysql数据库，需要用户自己安装mysql，并创建一个新的数据库，将它的地址和用户名、密码填到这个地方。mac系统推荐使用sequel pro程序浏览自己本地mysql数据库的情形。
### 2.3 数据库migration，使用seeder填充mock数据
接下来，运行数据库migration，并通过预先写好的seeder，给数据库填充用于测试的mock信息   
```
$ php artisan migrate --seed
```
注意，如果之前已经populate过老数据库，建议彻底删除后重新migrate。
### 2.4 配置passport
#### 2.4.1 配置APP_KEY
如果是第一次使用laravel， `.env` 文件中不含APP_KEY这个变量，那么还需要让程序加载初始key。一些情况下，也可以使用以前曾经使用过的key，来确保数据库之间能够对应。只需运行下面这两个指令即可：
```
$ php artisan key:generate
$ php artisan passport:keys
```  
如果之前已经配置了key，程序会提示你，是否想要重置key，按自己需求选择即可。

#### 2.4.2 创建passport client
在这个工程中，我们使用laravel自带的passport这个package，给api进行基本的授权。为了在本地顺利测试相关情况，我们需要对passport进行基本配置，比如说，以Personal access client的名义，给自己的前端部分授权。
```
$ php artisan passport:install
```

这一步按照程序提示，输入任意字符串即可。
### 2.5 使用valet，或直接serve程序，让服务器“运行”起来。
#### 2.5.1 使用valet（mac用户，推荐使用此项）
valet是一个非常便利轻量开发的工具，它可以让编程和测试更方便地衔接起来。使用mac的用户，花费一点点时间配置它，事半功倍。  
使用valet的用户，只要进行到`backend`文件夹根目录，运行
```
$ valet park
```
以后就可以通过`backend.test`这个网址对本工程进行访问。比如，访问`backend.test/api/register`，进行新用户的注册。

#### 2.5.2 使用laravel自带的serve指令，模拟服务器服务
不愿使用valet的用户，可以运行指令  
```
$ php artisan serve
```
然后使用terminal中弹出的地址访问本工程即可。一般是`http://127.0.0.1:8000/`。也就是说，以后可以使用`http://127.0.0.1:8000/api/register`，访问注册页面

以下默认使用`http://127.0.0.1:8000/`作为访问路径。



### 2.6 在成功初次使用之后，需要重新pull代码，更新数据库
#### 2.6.1 使用场景
有的时候我们会面对这样的情况：
1. 有一段时间没工作了想重新开始
2. 刚完成了一部分工作，想在提交前确保和远端兼容
3. 想要进行下一个任务，
4. 其他人刚往远端push了新的代码，本地希望和远程保持同步  

这些时候我们需要重新加载来自远程主branch（`master`）的更新，并且将数据库对应的变化同步更新。

#### 2.6.2 注意事项
- 请确保自己在backend文件夹内！否则会遇到artisan command不存在这一类的报错
- 如果还没配置好env，没有运行过后端，请先按照后端readme安装教程的介绍，将各部分先配置好，不能找搬本教程

#### 2.6.3 方法
依次运行以下指令即可 ：

```
$ git pull
$ composer update
$ php artisan migrate:reset
$ php artisan passport:install
$ php artisan db:seed
$ vendor/bin/phpunit
```
以上步骤的意义依次为：  
1. 从github下载最新更新
2. 用composer加载最新的安装包（重要！有的时候旧包会有一些bug）
3. 将现有数据库所有migration清空
4. 重新安装passport clients（因为数据库清空了，因此需要重新建立sample clients）
5. 将现有数据库用mock数据填充
6. 运行phpunit的测试组件，确定一切正常。

#### 2.6.4 结果
正常的话应该会看到绿色的测试通过公示。一切ok的话，说明这边没有大问题了

### 2.7 使用 docker 安装后端

#### 2.7.1 安装 docker 以及 docker-compose

#### 2.7.2 .env

按照 2.2 中的说明配置 .env 文件

#### 2.7.3 检查配置
在 backend 目录下执行

``` shell
docker-compose config
```

查看是否有以下警告产生，有则需要检查 .env 文件

> WARNING: The xxx variable is not set. Defaulting to a blank string.

#### 2.7.4 运行服务
执行

``` shell
docker-compose up -d
```

如果是后端开发已有 php 环境，可以只执行``` docker-compose up dbadmin ``` 运行 数据库以及 phpmyadmin 服务

等待服务启动完成

#### 2.7.5 完成
成功后即可访问以下服务

- 数据库 mysql: localhost:3306 
- 查看数据库内容 phpmyadmin: localhost:8001 
- api: localhost:8000

git pull 之后需要重新执行

``` shell
docker-compose rm
docker-compose up --build
```

## 3. 数据库结构解释


### 3.1 数据库简单介绍

#### 3.1.1 废文网数据库的基本情况
关于数据库的ER图，请参考`backend`下文件`20200115-Sosad-ER2.png`。这个图还会根据具体情况作出调整。
关于数据库的名称和各项解释，请参考`backend`下文件`20200114-sosadfun-ER-database-structure.xlsx`。这个表格会随时更新。
关于具体的API内容，见trello“废文技术站-API”版的实时更新条目。

## 4. API文档
建议下载并使用postman程序，对api进行测试。
注意：Postman更改method，比如之前是GET后来是POST，有时需要【保存】才能生效。建议经常duplicate指令并将其命名保存下来，便于以后测试。
### 4.1 Authentification 权限管理
本后端采取passport对用户授权与否进行管理。其授权的基础，是采取接受token，并核对token是否属于和数据库匹配的有效token，从而验证是否能够允许用户对应的操作。
#### 4.1.1 注册新用户（register）
打开postman，选择POST方式发送信息（记得不要变成GET！）  
网址设置为`http://127.0.0.1:8000/api/register`  
在下拉parameter表单中填写内容，冒号左边是变量名称，冒号右边是对应内容。可以在postman界面中保存相关指令，便于后续重试：  
name: tester  
email: tester@gmail.com  
password: password  
password_confirmation: password  
这里字串`password`是默认的密码，也可以设置成其他字串。
然后点击发送，成功的话就会收到格式为json的返回信息，其中`code:200`表示成功，所返回的`token`就是之后用户用于登陆的验证信息。
如果信息不符合要求，会出现对应的validation错误提示。

#### 4.1.2 普通登陆（login）
已经注册的用户，也可以通过输入用户邮箱和密码登陆，来获得token用于进一步访问，方法是：
网址设置为`http://127.0.0.1:8000/api/login`，选择POST方式发送信息  
在下拉内容中填写：  
email: tester@example.com  
password: password  
然后点击发送，就可以和上面一步一样，得到正确的访问token  
如果这里发生了信息输入错误，导致信息不匹配（比如说，邮箱输错，或者密码输错），这里会收到对应的错误代码：  
401:'unauthorised'  
全部错误列表，可以从`config/error.php`查看  

#### 4.1.3 使用token，以注册用户的身份进行操作
前端默认使用
```
'headers' => [
    'Accept' => 'application/json',
    'Authorization' => 'Bearer '.$accessToken,
]
```
这样的格式，来表示自己是api终端，需要以xx用户的身份通过验证。
其中$accessToken应该是在之前的login步骤中获得的。  
在postman中下拽header并填写这部分内容就行了。  
实际在postman的Headers（菜单上第三格，不是默认的，需要鼠标点开来）上面显示的效果如下：
| Key       | Value       | 备注  |
| -------------|-------------|-------------|
| Accept  | application/json |照着写就行|
| Authorization | Bearer eyJ0eXAiOiJK...|这个token字串会很长，注意Bearer和token之间有一个英文空格， 还有注意是Bearer，不是Bear|

#### 4.1.4 重置密码
忘记密码，可以通过邮箱进行密码重置，方法是：
网址设置为`http://127.0.0.1:8000/api/password/email`，选择POST方式发送信息  
在下拉内容中填写：  
email: tester@example.com  
正确返回：
200 data email
错误代码：
409 当日注册的用户，12小时内已发送过重置邮件不能重置密码
404 邮箱账户不存在
422 邮箱格式错误
595 发送邮件失败

登陆邮箱读取重置邮件，获取token，利用token进行重置，方法是：
网址设置为`http://127.0.0.1:8000/api/password/reset_via_email`，选择POST方式发送信息  
在下拉内容中填写：  
token: token_example
password: passsword_example
正确返回：
200 
错误代码：
422 密码格式错误/token过期
404 token不存在
409 12小时内已成功重置密码不能重置密码
500 未知错误


### 4.2 错误处理 error handling
全部error 列表目前存放在`config/error.php`中，基本遵循http相关指令的约定：2xx表示成功；4xx表示请求/数据有问题；5xx表示服务器问题。具体问题的解释，在这个文件里可以看。

## 5. 如何测试
#### 5.1 测试设计的原则
1）在业务功能里有任何一个条件判断，测试中都必须用不同的方式加以验证。
比如，业务功能有判断，如果条件1，返回结果1；如果条件2，返回结果2。测试中应包括：条件1，检查结果等于1；条件2，检查结果等于2。
2）测试除了检查返回的错误代码是否等同，对于“修改”了Model数值的情况，还需要验证数据库里Model的值确实发生了改变。在表面上修改而并没有写入数据库的情况，是测试中要重点检查的一环。
3）对于每一种失败的错误条件，也需要设计不同的tester来检查确实会发生
4）在测试中，尽量避免“固有变量”对测试结果造成影响，比如说需要选择一个用户的时候，避免使用固定用户1（因为有可能其他人的程序对这个用户的情况已经造成了影响），可以考虑新建一个随机用户，将它的相关数值设定为测试需要的条件。
5）建议同类测试串联起来。比如，先测试post的创建，再测试post的删除。

#### 5.2 写一个新的专项测试文件
在backend/tests/Feature目录下，放置对应的测试文件。
一些常用的test技巧如下：
```
$response = $this->post('api/thread/', $data);//可以直接使用post指令检查

//var_dump($response->decodeResponseJson());//同样可以使用helper，直接输出response的具体内容，查看错误原因

$response->assertStatus(200)//直接检查status是否符合需求

//可以连续使用->来直接进行多次连续的assert检查。
->assertJsonStructure([//检查数据结构，这适合检查类似于token的，只需要检查有没有，不需要检查是多少的代码。
    'code',
    'data' => [
        //...
    ],
])
->assertJson([//检查具体的数据的值是否和预期相符合，比如具体的内容是否经过了修改，存储为新的值，数据类型是否符合预期
    'code' => 200,
    'data' => [
        'type' => 'thread',
        'attributes' => [
            ...
        ],
    ],
]);

```


#### 普通测试
在backend目录下，运行  
```
vendor/bin/phpunit
```
进行测试。  
如果想要测试具体的某一个内容，可以运行如下代码：  
```
vendor/bin/phpunit --filter 'ChapterTest'
```
换成自己想要单独测试的内容即可。

#### 提交代码前进行最后检查（注意，这会重置数据库，最好先确保其他地方没有明显的问题）
每次新提交完成的pull request之前，后端应该确保自己的工作和现有backend能够协调、在一个fresh database的基础上能够通过检测，方法如下：  
```
git pull
php artisan migrate:reset
php artisan migrate --seed
php artisan passport:install
vendor/bin/phpunit
```
如果这一步发生了报错，那么需要进一步寻找到底是哪里出了问题。  

另外，请确保api documentation里，包含这个api会接受的所有变量和它们的可能的类型（包括必填项、可填项），确保相关api存储行为所改变的所有变量都在test中获得值的检查（比如说，不要出现想要修改xx变量，结果test里没有确保这个值经过修改，因此虽然返回成功代码200，实际上数据库里并没有存储对应值改变……不要出现这样的情况）  
