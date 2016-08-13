<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
/**
 * 王鑫磊  2016-07-29  增加颜值圈Controller
 */
class FavorableController extends HomebaseController {
    /*
     *优惠首页
     * */
    public function FavorableIndex(){
        $FavorableModel = D('Favorable');
        $Result = $FavorableModel->SelAllFavorable();
        $this->ajaxReturn($Result);
    }
}