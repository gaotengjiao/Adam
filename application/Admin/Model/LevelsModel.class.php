<?php
/*
	颜值圈
 */
namespace Common\Model;
use Common\Model\CommonModel;
class LevelsModel extends CommonModel {
    /*
     * 颜值圈数据显示
     */
    public function show_score(){
        $table = M('levels');
        $return= $table -> select();

        foreach($return as $k=>$v) {
            $table1 = M('comments');
            $where['post_table'] = 'levels';
            $where['post_id'] = $v['id'];
            $comment = $table1->where($where)->count();
            $return[$k]['count'] = $comment;
        }
        return $return;
    }
    /*
     * 添加内容
     */
    public function add_con($data){
        $data['level_date'] = date("Y-m-d h:i:s", time());

        $member = M('member');
        $where['usergroup'] = 0;
        $uid = $member -> where($where) -> field('uid') -> select();
        foreach($uid as $k => $v){
            $id[] = $v['uid'];
        }
        $sui = array_rand($id);
        $data['level_author'] = $id[$sui];
        $levels = M('levels');
        $return = $levels -> add($data);
        return $return;

    }
    /*
     * 上传手术前后对比图
     */
    public function uploads($before, $after, $aid){
        $data['before_img'] = json_encode("data/upload/".$before);
        $data['after_img'] = json_encode("data/upload/".$after);
        $data['lid'] = $aid;
        $photo = M('levels_photo');
        $result = $photo -> add($data);
        return $result;
    }

}




