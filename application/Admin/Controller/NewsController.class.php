<?php

/*
 * 消息管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class NewsController extends AdminbaseController {
	/*
		显示消息列表
	*/
		public function Index()
		{
			$this->display("index");
		}
	}