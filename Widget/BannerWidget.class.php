<?php
/**
 * BannerWidget 轮播图插件
 * @author lan
 */
namespace Home\Widget;
use Think\Controller;

/**
* BannerWidget 轮播图插件
* 需搭配数据库使用
CREATE TABLE `pref_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '轮播位置id/轮播图id',
  `pid` int(11) DEFAULT '0' COMMENT '轮播图所属轮播位置id',
  `title` varchar(255) DEFAULT '' COMMENT '轮播位置说明/轮播图说明',
  `src` varchar(255) DEFAULT '' COMMENT '轮播位置模板文件名/轮播图文件地址',
  `sort` smallint(6) DEFAULT '0' COMMENT '轮播图顺序',
  `data` varchar(255) DEFAULT '' COMMENT '轮播位置配置说明/轮播图链接',
  `isshow` tinyint(1) DEFAULT 1 COMMENT '轮播位置/轮播图是否显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8
*
* @api 调用示例
*
*      W('Banner/Show', array($bannerId, $width, $height)); （控制器中）
*      {:W('Banner/Show', array($bannerId, $width, $height))}（模板中）
*      W('Banner/$bannerId', array($width, $height)); （控制器中）
*      {:W('Banner/$bannerId', array($width, $height))}（模板中）
*/
class BannerWidget extends Controller
{
	private $modelname = 'banner';
	public function __call($name, $arguments){
		$this->show($name, $arguments[0], $arguments[1]);
	}

	public function show($bannerId=0, $width=0, $height=0){
		$bannerId = intval($bannerId);
		if($bannerId <= 0){
			$this->showError('w201:Error bannerId');
			return false;
		}
		// 轮播图位置信息
		$condition = array();
		$condition['id'] = $bannerId;
		$condition['isshow'] = 1;
		$condition['pid'] = 0;
		$banner_position = M($this->modelname)->where($condition)->find();
		if(!$banner_position){
			$this->showError('w202:Error bannerId');
			return false;
		}
		// 轮播图所用模板文件
		$template = $banner_position['src'] ? $banner_position['src'] : 'banner';
		// 轮播图配置信息
		$config = $banner_position['data'] ? json_decode($banner_position['data'], true) : array();
		// 自定义宽高
		if(intval($width) > 0){
			$config['width'] = intval($width);
		}elseif (isset($config['width'])) {
			$config['width'] = intval($config['width']);
		}
		if(intval($height) > 0){
			$config['height'] = intval($height);
		}elseif (isset($config['height'])) {
			$config['height'] = intval($config['height']);
		}

		// 图片列表
		$condition = array();
		$condition['pid'] = $bannerId;
		$condition['isshow'] = 1;
		$list = M($this->modelname)->where($condition)->order('sort asc')->select();
		foreach ($list as $key => $item) {
			$item['data'] = json_decode($item['data'], true);
			if(!$item['data']){
				$item['data'] = array();
			}
			$list[$key]['data'] =  $item['data'];
		}

		$this->assign('bannerId', $bannerId);
		$this->assign('config', $config);
		$this->assign('list', $list);
		$this->display('Widget:'.$template);
	}

	private function showError($message=''){
		echo '<pre>'. $message .'</pre>';
	}
}