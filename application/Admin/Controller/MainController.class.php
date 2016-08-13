<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MainController extends AdminbaseController {
	
    public function index(){
    	
        //print_r($_SESSION['name']);die;
    	$mysql= M()->query("select VERSION() as version");
    	$mysql=$mysql[0]['version'];
    	$mysql=empty($mysql)?L('UNKNOWN'):$mysql;
        $usercount = count(M('user')->select());
        $Time = date("Y-m-d");
        $YADD = count(M("member")->where("starttime = '$Time'")->select());
        $Admin = count(M('users')->select());
        $AdminDoctor = count(M('users')->where("is_doctor = 1")->select());
        $AdminWWW = count(M("users")->where("is_doctor = 0")->select());
    	$info = array(
    			L('OPERATING_SYSTEM') => $usercount.L("COUNT"),
                L('Y_ADD') => $usercount.L("COUNT"),
    			L('OPERATING_ENVIRONMENT') => $Admin.L("COUNT"),
    			L('PHP_RUN_MODE') => $AdminDoctor.L("COUNT"),
    			L('MYSQL_VERSION') =>$AdminWWW.L("COUNT"),
    			L('PROGRAM_VERSION') =>  L('BANBEN'). "&nbsp;&nbsp;&nbsp; [<a href='http://www.buruwo.com' target='_blank'>S-ONE</a>]",
    			L('UPLOAD_MAX_FILESIZE') => ini_get('upload_max_filesize'),
    			L('MAX_EXECUTION_TIME') => ini_get('max_execution_time') . "s",
    			L('DISK_FREE_SPACE') => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
    	);
    	$this->assign('server_info', $info);
    	$this->display();
    }
}