<?php

namespace Common\Model;

use Common\Model\CommonModel;

class GoodsModel extends CommonModel {

    public function Goods($post){
        $res = $this->indexsearch($post);
        $where = $res[1];
        if($where){
            $where .= " and state = 1";
        }else{
            $where = "state = 1";
        }
         
        $goods = M('goods');
        $demo = $goods->where($where)->select();
        $lens = count($demo);
        $Page       = new \Think\Page($lens,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $goods->order("gorder asc")->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $len = count($list);
        // 分类查询
        for($i=0; $i<$len; $i++){
            $id = $list[$i]['gtype'];
            $list[$i]['type'] = implode(',', M('goods_type')->field('typename')->where("id = $id")->find());

            // 活动查询
            $bosty = explode(',',$list[$i]['aid']);
            $lens = count($bosty);
            $table = '';
            for($j=0; $j<$lens; $j++){
                $aid = $bosty[$j];
                $table .= implode(',',M('product')->where("id = $aid")->field('p_name')->find()).' ';
            }
            $list[$i]['aid'] = $table;

            // 医师查询
            $bosty = explode(',',$list[$i]['usersid']);
            $lens = count($bosty);
            $table = '';
            for($j=0; $j<$lens; $j++){
                $aid = $bosty[$j];
                $table .= implode(',',M('users')->where("id = $aid")->field('user_login')->find()).' ';
            }
            $list[$i]['usersid'] = $table;
        }
        // 分类查询
        $facearr = M("goods_type")->where("state = 1")->select();
        $arr = $this->sortOut($facearr);

        // 服务流程查询
        $service = M('service')->where("status = 0")->select();
        $goodsarr['1'] = $list; // goods数据 
        $goodsarr['2'] = $show; // 分页数据
        $goodsarr['3'] = $arr;  // 分类
        $goodsarr['4'] = $res[2];
        $goodsarr['5'] = $res[3];
        $goodsarr['6'] = $res[4];
        $goodsarr['7'] = $res[5];
        $goodsarr['8'] = $service;
        return $goodsarr;
    }

    //添加页面查询分类
    public function Seltype(){
        $cate = M("goods_type")->select();
        $face = M("product")->where("state = 1")->field("id,p_name")->select();
        $doctor = $this->SelDoctor();
        $lists = $this->sortOut($cate);
        // 医师查询
        $users = M('users')->where("is_doctor = 1")->select();
        // 服务流程查询
        $service = M('service')->where("status = 0")->select();
        $lists['service'] = $service;
        $lists['users'] = $users;
        $lists['cate'] = $face;
        $lists['face'] = $lists;
        $lists['doctor'] = $doctor;
        return $lists;
    }

    /*

     * 查询医生

     * */

    public function SelDoctor(){
        $DoctorTable = M("users")->where("is_doctor = 1")->field("id,user_login,user_url")->select();
        return $DoctorTable;exit;
    }

    /*

      修改页面查询分类

    */

    public function Editype($id){
        $cate = M("goods_type")->select();
        $face = M("product")->where("state = 1")->field("id,p_name")->select();
        $lists1 = $this->sortOut($cate);
        $lists = M("goods")->where("id=$id")->find();
        $s = M("goods_2")->where("gid=$id")->find();
        // 医师查询
        $users = M('users')->where("is_doctor = 1")->field('id, user_login')->select();
        // 服务流程查询
        $service = M('service')->where("status = 0")->select();
        $aidss = explode(',',$lists['aid']);
        foreach ($aidss as $k => $v) {
            $aid[$k]['id'] = $v;
        }
        $usersid = explode(',',$lists['usersid']);
        foreach ($usersid as $k => $v) {
            $use[$k]['id'] = $v;
        }
        $lists['cate'] = $lists1;
        $lists['face'] = $face;
        $lists['a'] = $s;
        $lists['b'] = $lists;
        $lists['users'] = $users;
        $lists['service'] = $service;
        $lists['usersid'] = $use;
        $lists['aid'] = $aid;
        return $lists;

    } 

    //模糊查询

    public function GoodsSelect($date){
        $gtype      = $date['gtype'];
        $keyword    = $date['keyword'];
        if($gtype==0){
            $where = "keyword like '%$keyword%'";
        }else{
            $where = "gtype = $gtype and keyword like '%$keyword%'";
        }
        $goods = M('goods');
        $count      = $goods->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,5);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        $list = $goods->where($where)->order('id DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $goodsarr['1'] = $list;
        $goodsarr['2'] = $show;
        return $goodsarr;
    }



    //添加

    public function GoodsAdd($post){

        $skt = I('post.s');
        $goods = M('goods');

        $posts['time']      = time(); 
        $posts['gname']     = $post['gname'];
        $posts['gtype']     = $post['gtype'];
        $posts['about']     = $post['about'];
        $posts['content']   = htmlspecialchars_decode($post['content']);
        $posts['price']     = $post['price'];
        $posts['state']     = $post['state'];
        $posts['keyword']   = $post['keyword'];
        //$posts['img']       = $post['img_url'][0];
//        if(empty($post['img_url'][0]) || $post['humbimg_url'][0]){
//            return false;
//            exit;
//        }else{
//            $posts['img'] = $post['img'];
//            $posts['humbimg'] = $post['humbimg'];
//        }
        if(!empty($post['img_url'][0])){
            $posts['img'] = "/data/upload/".$post['img_url'][0];
        }
        //$posts['humbimg'] = $post['humbimg_url'][0];

        if(!empty($post['humbimg_url'][0])){
            $posts['humbimg'] = "/data/upload/".$post['humbimg_url'][0];
        }
        $posts['order'] = $post['order'];
        if ($post['pid']) {
            $posts['aid']   = implode(',',$post['pid']);
        }else{
            $posts['aid']   = '0';
        }
        
        if($post['radio'] == 'true'){
            $posts['usersid'] = implode(',',$post['usersid']);
        }else{
            $posts['usersid'] = '0';
        }
        $posts['service'] = $post['service'];

        $goodsAdd = $goods->add($posts);
        
        /*

         *  一大波数据来袭

         * */

        if($skt == 1 && $goodsAdd){
            $data['gid']                = $goodsAdd;
            $data['opstype']            = $post['opstype'];
            $data['curetime']           = $post['curetime'];
            $data['healthproject']      = $post['healthproject'];
            $data['painindex']          = $post['painindex'];
            $data['riskindex']          = $post['riskindex'];
            $data['opsmaterial']        = $post['opsmaterial'];
            $data['hocustype']          = $post['hocustype'];
            $data['stitchestype']       = $post['stitchestype'];
            $data['rejuvenationname']   = $post['rejuvenationname'];
            $data['Surgeryadvantage']   = $post['Surgeryadvantage'];
            $data['Thefinaleffectoftime'] = $post['Thefinaleffectoftime'];
            $data['Theeffecttomaintaintime'] = $post['Theeffecttomaintaintime'];

            $data['treatmenttimes'] = $post['treatmenttimes'];
            $data['hospitalization'] = $post['hospitalization'];
            
            $goodsAdd = M('goods_2')->add($data);

        }

        
        $date = date('Y-m-d H:i:s', time());
        chmod("data/upload/".$date, 0777);
        
        return $goodsAdd;

    }

    //删除

    public function DelDate($date){

        $goods = M("goods");

        $id = I("get.id");

        $result = $goods->where("id=$id")->delete();

        return $result;

    }



    //修改

    public function GoodsEdit($date){

        $goods = M('goods');

        $id = $date['id'];
        $resultPro = $this->AddPro($id,$date);

        if($resultPro>0){

            $posts['gname'] = $date['gname'];

            $posts['gtype'] = $date['gtype'];

            $posts['about']  = $date['about'];

            $posts['content'] = html_entity_decode($date['content']);

            $posts['price'] = $date['price'];

            $posts['state'] = $date['state'];

            $posts['keyword'] = $date['keyword'];

            $posts['img'] = $date['img_url'][0];

            if($date['img_url'][0] == ""){

                $posts['img'] = $date['img'];

            }

            $posts['humbimg'] = $date['humbimg_url'][0];

            if($posts['humbimg'] == ""){

                $posts['humbimg'] = $date['humbimg'];

            }
            $posts['time'] = time();
            $posts['order'] = $date['order'];
                $posts['usersid'] = implode(',',$date['usersid']);
            


            chmod("data/upload/".$date, 0777);



            $result = $goods->where("id=$id")->save($posts);

            return $result;

        }else{



        }

    }





    /*

        修改产品2

     */

    public function GoodsEdit2($date){

        $goods = M('goods');
        $goods2 = M('goods_2');
        //print_R($date);die;
        $id = $date['id'];

        $resultPro = $this->AddPro($id,$date);
            $posts['gname'] = $date['gname'];

            $posts['gtype'] = $date['gtype'];

            $posts['about']  = $date['about'];

            $posts['content'] = html_entity_decode($date['content']);

            $posts['price'] = $date['price'];

            $posts['state'] = $date['state'];

            $posts['keyword'] = $date['keyword'];

            $posts['img'] = $date['img_url'][0];

            if($date['img_url'][0] == ""){

                $posts['img'] = $date['img'];

            }
            $posts['time'] = time();
            $posts['humbimg'] = $date['humbimg_url'][0];

            if($posts['humbimg'] == ""){

                $posts['humbimg'] = $date['humbimg'];

            }
            if($date['usersid']){
                $posts['usersid'] = implode(',',$date['usersid']);
            }else{
                $posts['usersid'] = '0';
            }
            
            $posts['order'] = $date['order'];

            if($date['pid'] != ''){
                $posts['aid'] = implode(',',$date['pid']);
            }else{
                $posts['aid'] = '0';
            }
            
            chmod("data/upload/".$date, 0777);

            $result = $goods->where("id=$id")->save($posts);

            if($resultPro || $result){
                $true = true;
            }

            return $true;

    }



    public function AddPro($id,$date){

        $GoodsTable = M("goods_2");

        $datas["gid"] = $id;

        $datas["opstype"] = $date['opstype'];

        $datas["curetime"] = $date['curetime'];

        $datas["healthproject"] = $date['healthproject'];

        $datas["painindex"] = $date['painindex'];

        $datas["riskindex"] = $date['riskindex'];

        $datas["opsmaterial"] = $date['opsmaterial'];

        $datas["hocustype"] = $date['hocustype'];

        $datas["stitchestype"] = $date['stitchestype'];

        $datas["rejuvenationname"] = $date['rejuvenationname'];

        $datas["Surgeryadvantage"] = $date['Surgeryadvantage'];

        $datas["Thefinaleffectoftime"] = $date['Thefinaleffectoftime'];

        $datas["Theeffecttomaintaintime"] = $date['Theeffecttomaintaintime'];

        $datas["treatmenttimes"] = $date['treatmenttimes'];

        $datas["hospitalization"] = $date['hospitalization'];

        $result = $GoodsTable->where("gid=$id")->save($datas);

        if($result){

            return 1;

        }else{
            $datas['gid'] = $id;
            $return = $GoodsTable->add($datas);
            return $return;
        }

    }



    /*

        无限极分类查询分类内容

    */

    static public function sortOut($cate,$pid=0,$level=0,$html='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'){

        $tree = array();

        foreach($cate as $v){

            if($v['pid'] == $pid){

                $v['pid'] = $level + 1;

                $v['html'] = str_repeat($html, $level);

                $tree[] = $v;

                $tree = array_merge($tree, self::sortOut($cate,$v['id'],$level+1,$html));

               }

            }

        return $tree;

    }

      public function SavePath($html,$result){

         $User = M("goods"); // 实例化User对象

        // 要修改的数据对象属性赋值

        $data['path'] = $html;

        $IF_OK = $User->where("id=$result")->save($data); // 根据条件保存修改的数据

        return $IF_OK;

      }





      // 修改排序

      public function SaveOrder($get){

            $id = $get['id'];     // 获取id

            $order['gorder'] = $get['gorder']; // 获取排序号

            $Model = M('goods');

            $data = $Model->where("id = $id")->save($order);

            return $data;

      }



      // 修改上下架

      public function editState($post){

            $table = M('goods');

            $id['id'] = $post['id'];

            $state['state'] = $post['state'];

            if($state['state'] == 1){

                $state['state'] = 0;

            }else{

                $state['state'] = 1;

            }

            $res = $table->where($id)->save($state);

            return $res;

      }



    /*

        回收站

    */

    public function recycle($post){
        $res = $this->indexsearch($post);
        $where = $res[1];
        if($where){
            $where .= " and state = 0";
        }else{
            $where = "state = 0";
        }
         
        $goods = M('goods');
        $demo = $goods->where($where)->select();
        $lens = count($demo);
        $Page       = new \Think\Page($lens,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $goods->order("gorder asc")->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        $len = count($list);
        // 分类查询
        for($i=0; $i<$len; $i++){
            $id = $list[$i]['gtype'];
            $list[$i]['type'] = implode(',', M('goods_type')->field('typename')->where("id = $id")->find());

            // 活动查询
            $bosty = explode(',',$list[$i]['aid']);
            $lens = count($bosty);
            $table = '';
            for($j=0; $j<$lens; $j++){
                $aid = $bosty[$j];
                $table .= implode(',',M('product')->where("id = $aid")->field('p_name')->find()).' ';
            }
            $list[$i]['aid'] = $table;

            // 医师查询
            $bosty = explode(',',$list[$i]['usersid']);
            $lens = count($bosty);
            $table = '';
            for($j=0; $j<$lens; $j++){
                $aid = $bosty[$j];
                $table .= implode(',',M('users')->where("id = $aid")->field('user_login')->find()).' ';
            }
            $list[$i]['usersid'] = $table;
        }
        // 分类查询
        $facearr = M("goods_type")->where("state = 1")->select();
        $arr = $this->sortOut($facearr);

        // 服务流程查询
        $service = M('service')->where("status = 0")->select();
        $goodsarr['1'] = $list; // goods数据 
        $goodsarr['2'] = $show; // 分页数据
        $goodsarr['3'] = $arr;  // 分类
        $goodsarr['4'] = $res[2];
        $goodsarr['5'] = $res[3];
        $goodsarr['6'] = $res[4];
        $goodsarr['7'] = $res[5];
        $goodsarr['8'] = $service;
        return $goodsarr;

    }



    /*

        修改下架

     */

    public function outs($id){

        $table = M('goods');

        $id = $id[id];

        $len = count($id);

        $state['state'] = '0';

        for($i=0;$i<$len;$i++){

            $res = M('goods')->where("id = $id[$i]")->save($state);

        }

        return $res;

    }   



     /*

        全选下架

     */

    public function tests($id){

        $table = M('goods');

        $id = $id[id];

        $len = count($id);
 
        $state['state'] = '1';

        for($i=0;$i<$len;$i++){

            $res = M('goods')->where("id = $id[$i]")->save($state);

        }

        return $res;

    }



    /*

        全选删除

     */

    public function DelDates($id){

        $len = count($id);

        for($i=0; $i<$len; $i++){

            $res = M('goods')->where("id = $id[$i]")->delete();

        }

        return $res;

    }

    /*
        查看详情页
     */
    public function details($id){
        $table = M("goods_2");
        $result = $table->where("gid = $id")->find();
        return $result;dump($result);
    }

    /*
        主页搜索
     */
    public function indexsearch($post){
        // 接收要查询的分类 
        $gtype = $post['gtype'];
        $gname = $post['keyword'];
        $time1 = $post['start_time']; // 开始时间
        $time2 = $post['end_time'];   // 结束时间
        $data1 = strtotime($time1); // 时间转换为时间戳
        $data2 = strtotime($time2);

        if($time1){
            $where[1] = "time > $data1";
        }
        if($time2){
            $where[1] = "time < $data2";
        }
        if($time1 && $time2){
            $where[1] = "time > $data1 and time < $data2";
        }
        if($gtype){
            $where[1] = "gtype = $gtype"; 
        }
        if($gtype && $time1){
            $where[1] = "gtype = $gtype and time > $data1";
        }
        if($gtype && $time2){ 
            $where[1] = "gtype = $gtype and time < $data2";
        }
        if($gtype && $time1 && $time2){
            $where[1] = "gtype = $gtype and time > $data1 and time < $data2";
        }
        if($gname){
            $where[1] = "gname = '$gname'";
        }
        if($gname && $time1){
            $where[1] = "gname = '$gname' and time > $data1";
        }

        if($gname && $time2){
            $where[1] = "gname = '$gname' and time < $data2";
        }
        if($gname && $time1 && $time2){
            $where[1] = "gname = '$gname' and time > $data1 and time < $data2";
        }
        if($gtype && $gname){
            $where[1] = "gname = '$gname' and gtype = '$gtype'";
        } 

        if($gtype && $gname && $time1){
            $where[1] = "gname = '$gname' and gtype = '$gtype' and time > $data1";
        }
        if($gtype && $gname && $time2){
            $where[1] = "gname = '$gname' and gtype = '$gtype' and time < $data2";
        }
        if($gtype && $gname && $time1 && $time2){
            $where[1] = "gname = '$gname' and gtype = '$gtype' and time > $data1 and time < $data2";
        }
        $where[2] = $gtype;
        $where[3] = $gname;
        $where[4] = $time1;
        $where[5] = $time2;

        return $where;
    }

    /*
        置顶
     */
    public function editstick($post){
        $id   = $post['id'];
        $stic = $post['stic'];
        if($stic == '0'){
            $stics['stick'] = '1';
        }
        if($stic == '1'){
            $stics['stick'] = '0';
        }
        $stics['time'] = time();
        $res = M('goods')->where("id = $id")->save($stics);
        return $res;
    }

}