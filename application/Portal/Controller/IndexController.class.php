<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController; 
/**
 * 首页
 */
 //header("Access-Control-Allow-Origin: *");
class IndexController extends HomebaseController {
    /*
		通过默认分类ID值查询所有分类信息以及相关的项目信息
    */
	public function Project(){
		$typeoneid	= I("request.typeoneid");
		$tabletype 	= D("GoodsType");
		$tablegods 	= D("Goods");
        if(empty($typeoneid)){
            $returnmess['resultnum']    = "101";
            $returnmess['result_mess']  = "提交错误";
            $returnmess['result']       = "";
            $this->ajaxReturn($returnmess);
        }else{
            $lists 		= $tabletype->SelType($typeoneid);
            if($lists['resultnum']==101 | $lists['resultnum']==102){
                $this->ajaxReturn($lists);exit;
            }
            $id 		= $lists['0']['id'];
            $Goods 		= $tablegods->SelMessage($id);
            $Date['type']  = $lists;
            $Date['goods'] = $Goods;
            $returnmess['resultnum']    = "0";
            $returnmess['result_mess']  = "成功";
            $returnmess['result']       = $Date;
            $this->ajaxReturn($returnmess);exit;
        }
	}

	/*
	通过小分类ID值查询相关项目信息
	*/
	public function Projectmess(){
		$id 	= I("request.projectid");
		if($id ==""){
			$error['error'] = 101;
			$error['error_mess'] = "请求错误";
			$result = json_encode($error);
		}else{
			$Table 	= D("Goods");
			$return = $Table->SelMessage($id);
			if(empty($return)){
				$error['error'] = 404;
				$error['error_mess'] = "未能查询到数据";
				$result = json_encode($error);
			}else{
				$result	= json_encode($return);
			}
		}
		print_r($result);die;
	}
	/*
	通过projectID查询项目的详细信息
	*/
	public function Messagedetails(){
		$id 	= I("request.projectid");
		if($id==""){
			$returnmess['resultnum']     = 101;
			$returnmess['result_mess']   = "提交错误";
			$returnmess['result']        = "";
			$this->ajaxReturn($returnmess);exit;
		}else{
			$tablegods 	= D("Goods");
			$Goods 	= $tablegods->SelPjectMessage($id);
			if(empty($Goods)){
				$returnmess['resultnum']     = 404;
				$returnmess['result_mess']   = "未找到";
				$returnmess['result']        = "";
				$this->ajaxReturn($returnmess);exit;
			}else{
				$returnmess['resultnum']     = 0;
				$returnmess['result_mess']   = "成功";
				$returnmess['result']        = $Goods;
				$this->ajaxReturn($returnmess);exit;
			}
		}
	}

/*
	颜值立减活动详细信息
*/
	public function ActiveLink(){
		$id 	= I("request.activeid");
		if($id==""){
			$error['error'] = 101;
			$error['error_mess'] = "请求错误";
			$result = json_encode($error);
			print_r($result);die;
		}else{
			$ActiveModel = D("product");
			$result = $ActiveModel->SelActive($id);
			$result = json_encode($result);
			print_r($result);die;
		}
	}

    /*
     * 搜索加载页面数据
     * */
    public function SearchHot(){
        $GoodsModel = D("Goods");
        $resultGoods = $GoodsModel->SelSearchHot();
        $this->ajaxReturn($resultGoods);
    }
    /*
     * 搜索
     * */
    public function Search(){
        $key = I("request.key");
        $type = I("request.type");
        $GoodsModel = D("Goods");
        $resultGoods = $GoodsModel->SearchKey($key,$type);
        $this->ajaxReturn($resultGoods);
    }
}