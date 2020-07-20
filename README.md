# Typecho Markdown 解析增强插件


@package KMarkdownPlus  
@version 1.2.0  

这是一个Typecho Markdown 解析增强的插件，目前仅增加了两种语法支持，分别是 Steam游戏引用 以及 BiliBili视频引用。  
评论中支持Steam引用，但是不支持BiliBili视频引用。  

## 目录 ##
 - [安装](#安装)
 - [使用方法 && 效果预览](#使用方法&&效果预览)
 - [设置](#设置)
   - [是否启用steam引用代理加速](#是否启用steam引用代理加速)
   - [是否开启评论区解析](#是否开启评论区解析)
   - [其否开启请求来源判断](#其否开启请求来源判断)
 - [Q & A](#q&a)
   - [为什么评论区引用steam不是撑满整行](#为什么评论区引用steam不是撑满整行)
 - [TODO](#todo)

## 安装 ##
你可以直接在typecho的`usr/plugins`文件夹中clone 项目的master分支，然后将文件夹重命名为`KMarkdownPlus`，如下：  
```bash
cd 网站根目录/usr/plugins
git clone https://github.com/kgysf/typecho-KMarkdownPlus-plugin.git
mv typecho-KMarkdownPlus-plugin KMarkdownPlus
```

如果你没有安装git，你也可以通过如下方式获取：  

```bash
cd 网站根目录/usr/plugins
wget -c https://github.com/kgysf/typecho-KMarkdownPlus-plugin/archive/1.1.0.tar.gz -O KMarkdownPlus.tar.gz
tar -zxvf KMarkdownPlus.tar.gz
mv typecho-KMarkdownPlus-plugin-1.1.0 KMarkdownPlus
```

最新版本下载地址请到[此处][1]获取  

然后到网站后台的插件设置中启用插件即可。  

## 使用方法 && 效果预览 ##

> Steam游戏引用，语法：`[steam id=steam游戏id /]`    

![Steam预览图][2]


> 语法：`[bili id=B站视频AV或BV号 /]`，如果是bv号一定要附带BV开头   

![Bili预览图][3]

## 设置 ##

### 是否启用steam引用代理加速 ###

> Steam在国内访问非常不稳定，随时会无法访问，开启Steam引用代理加速后，引用Steam将会走你的服务器代理转发，加速Steam访问。  
**需要注意：**
 - 开启后，引用Steam将走你服务器的流量。  
 - 首次访问会在服务器缓存需要的文件，可能会响应超时，属于正常情况，只有第一次会出现。  
 - 如果你的服务器也完全无法访问steam，那么加速无效。  


### 是否开启评论区解析 ###

> 是否开启评论区解析Steam引用语法，开启后需要在Typecho的 `设置 - 评论 - 允许使用的HTML标签和属性` 中添加 `<iframe src="" style="">`  

### 其否开启请求来源判断 ###

> 如果开启了steam引用代理加速，那么建议设置此项，设置此项后只有白名单中的域名才可引用。  

 - 如果此项为空，那么表示不限制请求来源，任何网站都可以引用。    
 - 如果此项不为空，则表示限制请求来源，只允许设置的网站引用，多个域名用','(英文逗号)隔开。    

**列如：**
你的网站域名是 `www.example.com` 那么就直接填写 `www.example.com`    
你的网站可以同时通过 `www.example.com` 和 `example.com` 访问，那么就填写 `www.example.com,example.com`    

**注意：**
不支持泛域名，填写的时候请不要附带 `http://` 或者 `https://` ，请直接填写域名。  


## Q & A ##

### 为什么评论区引用steam不是撑满整行？ ###
因为你的主题评论区样式可能不是块级元素，需要修改主题样式，将评论容器的宽度设置为100%，如果是handsome主题，请在`主题设置 - 开发者设置 - 自定义CSS`中添加如下CSS
```CSS
.comment-content-true {
    width: calc(100% - 5px);
}
``` 

## TODO ##

 - [ ] 番剧信息引用 ~~(咕咕咕)~~


 [1]: https://github.com/kgysf/typecho-KMarkdownPlus-plugin/releases
 [2]: https://github.com/kgysf/typecho-KMarkdownPlus-plugin/blob/master/steam.png?raw=true
 [3]: https://github.com/kgysf/typecho-KMarkdownPlus-plugin/blob/master/bilibili.png?raw=true
