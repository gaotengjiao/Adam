<?php
namespace Common\Model;
use Common\Model\CommonModel;
class RecordsModel extends CommonModel {
    public function bill(){
        $bill = M('Records');
        $count      = $bill->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
    	$list = $bill->join("s_user on s_user.uid=s_records.uid")->order('s_records.time DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $billarr['1'] = $list;
        $billarr['2'] = $show;
        return $billarr;
    }
    public function check($id){
        $bill = M('Records');
        $id = $id['id'];
        $uid = $id['uid'];
        $Reinfo = $bill->join("s_member on s_records.uid = s_member.uid")->join("s_product on s_records.aid = s_product.id")->where("s_records.aid=$id")->find();
//        echo $this->getLastSql();
        return $Reinfo;
    }
}
?>