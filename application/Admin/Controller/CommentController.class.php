<?php

/**
 * 后台首页
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class CommentController extends AdminbaseController {
		public function index()
		{
			$this->display("index");
		}
	}