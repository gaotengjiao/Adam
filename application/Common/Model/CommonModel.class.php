<?php

/* * 
 * 公共模型
 */

namespace Common\Model;
use Think\Model;

class CommonModel extends Model {

    /**
     * 删除表
     */
    final public function drop_table($tablename) {
        $tablename = C("DB_PREFIX") . $tablename;
        return $this->query("DROP TABLE $tablename");
    }

    /**
     * 读取全部表名
     */
    final public function list_tables() {
        $tables = array();
        $data = $this->query("SHOW TABLES");
        foreach ($data as $k => $v) {
            $tables[] = $v['tables_in_' . strtolower(C("DB_NAME"))];
        }
        return $tables;
    }

    /**
     * 检查表是否存在
     * $table 不带表前缀
     */
    final public function table_exists($table) {
        $tables = $this->list_tables();
        return in_array(C("DB_PREFIX") . $table, $tables) ? true : false;
    }

    /**
     * 获取表字段
     * $table 不带表前缀
     */
    final public function get_fields($table) {
        $fields = array();
        $table = C("DB_PREFIX") . $table;
        $data = $this->query("SHOW COLUMNS FROM $table");
        foreach ($data as $v) {
            $fields[$v['Field']] = $v['Type'];
        }
        return $fields;
    }

    /**
     * 检查字段是否存在
     * $table 不带表前缀
     */
    final public function field_exists($table, $field) {
        $fields = $this->get_fields($table);
        return array_key_exists($field, $fields);
    }

    protected function _before_write(&$data) {

    }

    /*
    生成订单编号
    */
    public function GenerateReservationNumber(){
        $datetime = date("YmdHis");
        $rand = rand(1000000,9999999);
        $number = $datetime.$rand;
        return $number;
    }

    public function UserToken($Uid,$Token){
        $UserTable = M("user");
        $result = $UserTable->where("uid = $Uid and token ='$Token'")->find();
        if(empty($result)){
            return SONEERROR();exit;
        }else{
            return SONEOK($result);exit;
        }
    }
    /*
     * 获取当前日期属于周几
     * */
    public function   getWeek($strtime) {
        $datearr = explode("-",$strtime);     //将传来的时间使用“-”分割成数组
        $year = $datearr[0];       //获取年份
        $month = sprintf('%02d',$datearr[1]);  //获取月份
        $day = sprintf('%02d',$datearr[2]);      //获取日期
        $hour = $minute = $second = 0;   //默认时分秒均为0
        $dayofweek = mktime($hour,$minute,$second,$month,$day,$year);    //将时间转换成时间戳
        $shuchu = date("w",$dayofweek);      //获取星期值
        $weekarray=array("周日","周一","周二","周三","周四","周五","周六");
        return $weekarray[$shuchu];
    }

    /*
     * 验证用户的账单准确性
     * $Uid     用户ID
     * $BillNum 账单编号
     * */
        public function BillAccuracy($Uid,$BillNum)
        {

        }
    }

