[TOC]

# Banner Widget for ThinkPHP3.2.3

> 适用版本：ThinkPHP 3.2.3

这是一个轮播图部件，基于ThinkPHP的Widget方式开发，前端默认模板使用[swiper3.js](http://www.swiper.com.cn/)，可自定义模板。

## 使用方法

1. 将 `View` 和 `Widget` 文件夹复制到模块目录下，如`Application\Home\`
2. 如果不是将部件放到Home模块下，需要打开`Widget\BannerWidget.class.php` ，修改namespace位置

```php
namespace YourModel\Widget;
```

3. 需要在数据库中增加`prefix_banner` 表，表结构如下：

```sql
CREATE TABLE `prefix_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '轮播位置id/轮播图id',
  `pid` int(11) DEFAULT '0' COMMENT '轮播图所属轮播位置id',
  `title` varchar(255) DEFAULT '' COMMENT '轮播位置说明/轮播图说明',
  `src` varchar(255) DEFAULT '' COMMENT '轮播位置模板文件名/轮播图文件地址',
  `sort` smallint(6) DEFAULT '0' COMMENT '轮播图顺序',
  `data` varchar(255) DEFAULT '' COMMENT '轮播位置配置说明/轮播图链接',
  `isshow` tinyint(1) DEFAULT 1 COMMENT '轮播位置/轮播图是否显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8
```

数据表说明：

- `prefix_`请替换为自己的表前缀
- 如果不希望使用`banner`这个表名，可以自行修改`Widget\BannerWidget.class.php` 中`$modelname` 的值

```php
private $modelname = 'banner';
```

- banner表将轮播图位置和轮播图文件整合在了一起，pid为0即表示为轮播图位置
  - 每个轮播图部件必须有一个对应的轮播图位置和至少一个轮播图文件
  - 可以通过控制isshow和sort字段分别控制轮播图是否显示和显示顺序（默认按sort从小到大排序）
  - 如果pid为0（某一个轮播图位置），则可以在src字段添加此轮播图的模板名（模板名不带后缀，按项目中设置的默认后缀，模板地址为`Home\View\Widget\`），如果不填，则默认模板名为`banner`
  - 如果pid为0（某一个轮播图位置），则可以在data字段添加配置信息（json格式），这些配置信息一般要配合轮播图模板设置，如是否自动播放，是否循环轮播等
  - 如果pid不为0（某一个轮播图文件），则可以在src字段添加图片地址
  - 如果pid不为0（某一个轮播图文件），则可以在data字段添加配置信息（json格式），如图片超链接地址`{"link":"http://www.baidu.com"}`

4. 在数据表中添加一些示例数据之后，可以使用如下方法调用部件

```php+HTML
## 其中$bannerId为轮播图位置id，$width $height为轮播图的宽高（需配合模板css样式）
## 控制器中调用
W('Banner/Show', array($bannerId, $width, $height));
W('Banner/$bannerId', array($width, $height));

## 模板中调用
{:W('Banner/Show', array($bannerId, $width, $height))}
{:W('Banner/$bannerId', array($width, $height))}
```



## 默认模板

>  部件默认添加了一个banner模板，此为默认模板

可配置项：

```json
{
	"width":480,
	"height":300,
	"pagination":"show|hide",
	"nav":"hide|show",
	"direction":"horizontal|vertical",
	"loop":"0|1",
	"autoplay":"1000|-1",
	"speed":300
}
```

- width：轮播图宽度，优先级：调用传值 > 配置（data）设置 > 默认值（480）
- height：轮播图高度，优先级：调用传值 > 配置（data）设置 > 默认值（300）
- pagination：是否显示分页，默认显示（show）
- nav：是否显示导航按钮（prev/next），默认隐藏（hide）
- direction：轮播图方向，默认横向滚动（horizontal）
- loop：是否循环轮播，默认不循环
- autoplay：是否自动播放，单位`ms` ，默认1000ms（1s切换），传入-1则不自动播放
- speed：轮播图滚动速度，但是`ms` ，默认300ms（设置越大，轮播图滚动越缓慢）

