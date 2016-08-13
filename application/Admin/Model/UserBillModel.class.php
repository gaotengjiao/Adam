<?php
/*
	账单Model类
 */
namespace Common\Model;

use Common\Model\CommonModel;

class UserBillModel extends CommonModel {
	/*
		分期还款表自动执行修改数据
	 */
	public function __construct() {
		// 查询分期还款表
		$table = M('installments_repayment_relation');
		$result = $table->where("ispay = 0")->select();
		$len = count($result);
		// 当前时间
		$time = time();
		for($i=0; $i<$len; $i++){ 
			// 获取当前id
			$id = $result[$i]['id'];
			// 获取当前时间
			$repaymentdate = $result[$i]['repaymentdate'];
			$time2 = strtotime($repaymentdate);
			// 获取账单分期关系表id
			$billid = $result[$i]['installmentsid'];
			// 获取账单id
			$bill = implode(',',M('bill_installments_relations')->field('billid')->where("id = $billid")->find());

			// 获取订单号
			$repaymentnum = $result[$i]['repaymentnum'];

			// 如果现在时间大于还款时间，则逾期，逾期状态改为1，逾期表加字段
			if($time > $time2){
				$data['isoverdue'] = 1;
				$res = $table->where("id = $id")->save($data);
				if($res){
					// 逾期账单编号
					$date['lateFeesNum'] = $repaymentnum;
					// 逾期账单时间
					$date['lateFeesDate'] = $repaymentdate;
					// 逾期状态为未付清
					$date['lateFeesStatus'] = 0;
					// 账单表id
					$date['billId'] = $bill;

					// 查询逾期表中有没有逾期账单
					$res1 = M('late_fees_bill')->where("lateFeesNum = $repaymentnum")->find();

					// 如果有，则查有还钱状态，如果没换，查还款表有无数据
					if(!empty($res1)){
						// 不等于一 则没有付清
						if($res1['latefeesstatus'] != 1){
							// 根据订单号查询用户还款表有没有还款
							$res3 = M('user_repayment')->where("repaymentNum = $repaymentnum")->find();
							// 如果存在则说明已付款
							if($res3){
								$data1['letefeesstatus'] = 1; 
								M('late_fees_bill')->where("lateFeesNum = $repaymentnum")->save($data1);
							}
						}
					// 如果没有，则添加进去。然后再执行上面操作	
					}else{
						M('late_fees_bill')->add($date);
					}
				}
			}
		}
		// 自动增加下一期数据
		$des = M()->table('__BILL_INSTALLMENTS_RELATIONS__ b, __INSTALLMENT__ m')->where('b.installmentsnum = m.id')->field('b.*, m.periods')->select();
		$len = count($des);
		for($i=0; $i<$len; $i++){
			// 获取账单表id
			$billid = $des[$i]['billid'];
			// 获取用户的id
			$userid = implode(',',M('user_bill')->where("id = $billid")->field('userId')->find());

			// 获取一共有多少期
			$periods = $des[$i]['periods'];
			// 开始第一期时间
			$date = $des[$i]['installmentsdate'];
			$time1 = strtotime($date);
	 		// 时间毫秒
	 		$microt = $this->microtime_float();
	 		$time3 = $userid.$microt;
			// 查询id值
			$bid = $des[$i]['id'];
			// 查询现在一共又多少期
			$nums = M('installments_repayment_relation')->where("installmentsid = $bid")->count();
			// 如果现在期数少于一共的期数
			if($nums >= 1 && $nums < $periods){
				// 获取最后一期的信息
				$result = M('installments_repayment_relation')->where("installmentsid = $bid and repaymentCount = $nums")->find();
				// 获取最后一期的时间
				$listtime = $result['repaymentdate'];
				$listtime = strtotime($listtime);
				// 一个月后的时间
	 			$time4 = date("Y-m-d 00:00:00",strtotime("+1months",$listtime));
				if($listtime < $time){
					// 本期期数
					$datas['repaymentCount'] = $nums +1;
					// 账单分期关系表
					$datas['installmentsId'] = $bid;
					// 还款时间
					$datas['repaymentDate']	= $time4;
					// 还款编号
					$datas['repaymentNum'] = $time3;
					// 执行添加
					M('installments_repayment_relation')->add($datas);
				}
			}elseif($nums == 0){
				// 一个月后的时间
	 			$time2 = date("Y-m-d 00:00:00",strtotime("+1months",$time1));
				// 本期期数
				$data['repaymentCount'] = 1;
				// 账单分期关系表
				$data['installmentsId'] = $bid;
				// 还款时间
				$data['repaymentDate']	= $time2;
				// 还款编号
				$data['repaymentNum'] = $time3;
				// 执行添加
				M('installments_repayment_relation')->add($data);
			}
		}
	}

	// 查询所有信息
	public function Sel($search){
	 	$lonk = "u.userid = m.uid";
	 	$resd = $this->searchtime($search,$lonk);
 		$where 	= $resd[1];
 		$red[3] = $resd[2]; // 编号。手机号
 		$red[4] = $resd[3]; // 开始时间
 		$red[5] = $resd[4]; // 结束时间
 		// 现在时间
		$time = time();
	 	$table = M('user_bill');
	 	$count = count($table->table('__USER_BILL__ u, __MEMBER__ m')->where($where)->select());
	 	$page  = new \Think\Page($count, 10);
	 	$show  = $page->show();
	 	$res = $table->table('__USER_BILL__ u, __MEMBER__ m')->limit($page->firstRow.','.$page->listRows)->where($where)->field('u.*, m.iphone')->select(); 
	 	$len = count($res);
		for($i=0; $i<$len; $i++){
			// 接收账单的金额
			$maney   = $res[$i]['billamount'];

			$billnum = $res[$i]['id'];
			
			if(!empty($billnum)){
				// 查询账单分期表
				$reds = M('bill_installments_relations')->field('id, installmentsNum')->where("billid = $billnum")->find();
			}

			// 分期期数表id
			$installmetsnum = $reds['installmentsnum'];
			// 账单分期关系表id
			$relationsid = $reds['id'];
			if(!empty($installmetsnum)){
				$tabl = M('installment')->field('periods,rate')->where("id = $installmetsnum")->find();
			}

			// 查询分几个月
			$mius = $tabl['periods'];

			// 每期要还的钱
			$maney   = sprintf("%.2f",$maney/$mius);
			
			// 查询利率
			$rate = $tabl['rate'];
			// 不分期
			if($mius == 1){ 
				// 不分期也为1期
				$res[$i]['periods'] = 1;
				// 如果等于1，则不分期
				$res[$i]['isstallments'] = '不分期';
				if(!empty($relationsid)){
					// 查询分期账单表
					$repay = M('installments_repayment_relation')->where("installmentsid = $relationsid")->find();
				}
				// 未支付
				if($repay['ispay'] == 0){
					// 未逾期
					if($repay['isoverdue'] == 0){
						$time1 = strtotime($repay['repaymentdate']);
						$timediff = $time1-$time;
						$days = intval($timediff/86400);
						if($days == 0){
							$days = 1;
						}
						$res[$i]['status'] = '<font color="blue">距还款日'.$days.'天</font>';
					// 逾期
					}elseif($repay['isoverdue'] == 1){
						// 获取订单编号
						$nus = $repay['repaymentnum'];
						if(!empty($nus)){
							$resd = M('late_fees_bill')->where("lateFeesNum = $nus")->find();
						}
						
						// 如果未付清
						if($resd['latefeesstatus'] == 0){
							// 逾期账单开始日期
							$time1 = strtotime($resd['latefeesdate']);
							$timediff = $time-$time1;
							$days = intval($timediff/86400);
							if($days == 0){
								$days = 1;
							}
							$res[$i]['status'] = '<font color="red">已逾期'.$days.'天</font>';
							$heads = $this->calculate($maney, $rate, $relation = 200 , $days);
							
							$res[$i]['maney'] = $heads.' 元';
						}elseif($resd['latefeesstatus'] == 1){
							$res[$i]['status'] = '<font color="blue">已付清</font>';
						}
					}
				}else{
					$res[$i]['status'] = '<font color="blue">已付清</font>';
				}
			// 分期
			}else{
				$rentals = 0;
				if(!empty($relationsid)){
					// 查询分期还款关系表
					$counts = M('installments_repayment_relation')->where("installmentsid = $relationsid")->count();
				}
				// 获取分多少期
				$res[$i]['periods'] = $counts;
				$n = 0;
				for($j=1; $j<$counts+1; $j++){
					if(!empty($relationsid)){
						// 查询分期还款关系表
						$demo = M('installments_repayment_relation')->order('repaymentCount DESC')->field('repaymentnum, repaymentDate, ispay')->where("installmentsid = $relationsid and repaymentCount = $j")->find();
					}
					// 订单编号
					$repayment = $demo['repaymentnum'];
					// 还款时间
					$timesa    = $demo['repaymentdate']; // 2016-07-31 16:23:52
					$time4	   = strtotime($timesa);
						
					// 如果为一 已还清
					if($demo['ispay'] == 1){
						$n +=1;
						$res[$i]['status'] = '<font color="blue">本期已还清</font>';
					// 为零则未支付
					}elseif($demo['ispay'] == 0){

						// 如果现在时间大于还款时间
						if($time > $time4){
							//计算天数
							$timediff = $time-$time4;
							$days = intval($timediff/86400);
							if($days == 0){
								$days = 1;
							}
							$res[$i]['status'] = '<font color="red">已逾期'.$days.'天';
							// 一共要支付的逾期金额
							$ren = $this->calculate($maney, $rate, $relation, $days);
							// 逾期金额
							$skt = $ren - $maney;
							// 一共预期的金额
							$skt1 += $ren;
							$table1 = M('user_interest')->where("overdueid = $repayment")->find();
							$data['overdueid'] 	= $repayment;
							$data['money']		+= $skt;
							$data['endtime']	= date("Y-m-d H:i:s", time());
							if($table1 == ''){
								M('user_interest')->add($data);
							}else{
								M('user_interest')->where("overdueid = $repayment")->save($data);
							}
							$res[$i]['maney'] = $skt1.' 元';
							$rentals += $ren;
						}else{
							//计算天数
							$timediff = $time4-$time;
							$days = intval($timediff/86400);
							if($days == 0){
								$days = 1;
							}
							$res[$i]['status'] = '<font color="blue">距还款日'.$days.'天</font>';
							$rentals += $maney;
							$res[$i]['maney'] = $rentals.' 元';
						}
					// 如果未支付。 逾期
					}else if($demo['ispay'] == 0 && $demo['isoverdue'] == 1){
						// 查询逾期表
						$demo2 = M('late_fees_bill')->where("lateFeesNum = $repayment")->find();
						// 查询逾期状态
						$status2 = $demo2['latefeesstatus'];
					}
				}
				$res[$i]['isstallments'] = '共'.$mius.'期，已付'.$n.'期';
			}
		}
		$red[1] = $res;
		$red[2] = $show;
 	 	return $red;
	}

	// 查询
	public function Selfind($id){
		$table = M('user_bill');
		$res = $table->table('__USER_BILL__ u, __MEMBER__ m')->where("u.userid = m.uid and u.id = $id")->field('u.*, m.iphone')->find();
		// 账单分期关系表
		$dat = M('bill_installments_relations')->where("billid = $id")->find();
		// 账单分期关系表id
		$relationid = $dat['id'];
		// 获取分期id
		$installmentsnumid = $dat['installmentsnumid'];
		if(!empty($installmentsnumid)){
			// 查询分期期数表
			$das = M('installment')->where("id = $installmentsnumid")->find();
		}

		// 分多少期
		$periods = $das['periods'];
		if(!empty($relationid)){
			// 查询已进行多少期
			$number = count(M('installments_repayment_relation')->where("installmentsid = $relationid and ispay = 1")->select());
			// 当前时间戳
			$time = time();
			// 逾期次数
			$billnum = count(M('installments_repayment_relation')->where("installmentsid = $relationid and isoverdue = 1")->select());
			if($billnum != 0){
				//输出逾期次数
				$res['bill']   	= '<font color="red">已逾期'.$billnum.'次</font> ';
			}else{
				$res['bill'] 	=  '<font color="blue">未逾期</font> ';
			}
			
			// 查询当前进行到第几期
			$req = M('installments_repayment_relation')->where("installmentsid = $relationid")->field('repaymentcount,repaymentDate')->order('repaymentCount desc')->find();
		}
		// 本期的还款时间
		$underway = $req['repaymentdate'];
		$underway = strtotime($underway);
		// 如果最后一期的时间小于今天，已逾期
		if($underway > $time){
			$timediff = $underway-$time;
			$days = intval($timediff/86400);
			if($days == 0){
				$days = 1;
			}
			// 本期还款状态
			$res['billstatus'] = '<font color="blue">本期还款剩余时间'.$days.'天</font>';
		}else{
			// 本期还款状态
			$res['billstatus'] = '<font color="red">正在逾期</font>';
		}
		

		// 本期期数
		$deak = $req['repaymentcount'];
		// 查询分期关系表
		if(!empty($relationid)){
			$ref = M('installments_repayment_relation')->where("installmentsid = $relationid and isoverdue = 1")->select();
		}
		
		$len = count($ref);
		for($i=0; $i<$len; $i++){
			// 查询编号
			$repaymentnum = $ref[$i]['repaymentnum'];
			if(!empty($repaymentnum)){
				// 查询用户逾期表
				$reg = M('late_fees_bill')->where("lateFeesNum = $repaymentnum")->find();
			}
			if($reg['latefeesstatus'] == 0){
				// 第几次逾期
				$repayment = $ref[$i]['repaymentcount'];
				// 等于零。未付清，获开始日期
				$time1 = $reg['latefeesdate'];
				$time2 = strtotime($time1);
				
				$timediff = $time-$time2;
				$days = intval($timediff/86400);
				if($days == 0){
					$days = 1;
				}
				// 逾期状态
				$res['bill'] .= '第'.$repayment.'期逾期'.$days.'天(未还清) &nbsp';
			}elseif($reg['latefeesstatus'] == 1){
				// 查询还款时间
				$time1 = $reg['latefeesdate'];
				$time2 = strtotime($time1);
				// 查询还款编号
				$nums = $reg['latefeesnum'];
				if(!empty($nums)){
					// 查询还款的月份
					$numdes = implode(',',M('installments_repayment_relation')->where("repaymentNum = $nums")->field('repaymentcount')->order('repaymentCount desc')->find());
					// 查询还款日期
					$time3 = implode(',',M('user_repayment')->where("repaymentNum = $nums")->field('repaymentdate')->find());
				}
			
				$time3 = strtotime($time3);
				$timediff = $time3-$time2;
				$dayf = intval($timediff/86400);
				if($dayf == 0){
					$dayf = 1;
				}
				// 逾期还清状态
				$res['bill'] .= '第'.$numdes.'期逾期'.$dayf.'天 &nbsp';
			}
		}

		if($periods == 1){
			$periods = '不分';
		}
		// 账单状态
		$res['status'] = '<b>'.$periods.'期，已还'.$number.'期，当前第'.$deak.'期</b>';
	
		return $res;
	}
	// 逾期账单
	public function overduebill($search){
 		// 现在时间
		$time = time();
	 	$table = M('user_bill');
	 	$count = count($table->table('__USER_BILL__ u, __MEMBER__ m, __BILL_INSTALLMENTS_RELATIONS__ t,__INSTALLMENT__ i, __INSTALLMENTS_REPAYMENT_RELATION__ d')->where('u.userid = m.uid and t.billid = u.id and t.id = d.installmentsid and i.id = t.installmentsnum and d.ispay = 0 and d.isoverdue = 1')->field('u.*, m.iphone')->select());
	 	$page  = new \Think\Page($count, 10);
	 	$show  = $page->show();
	 	$resda = $table->table('__USER_BILL__ u, __MEMBER__ m,__INSTALLMENT__ i,__BILL_INSTALLMENTS_RELATIONS__ t, __INSTALLMENTS_REPAYMENT_RELATION__ d')->limit($page->firstRow.','.$page->listRows)->where('u.userid = m.uid and t.billid = u.id and t.id = d.installmentsid and i.id = t.installmentsnum and d.ispay = 0 and d.isoverdue = 1')->field('u.*, m.iphone, t.id as tid, d.repaymentNum, i.rate, i.periods, d.repaymentdate, d.repaymentcount')->select();
	 	// 获取现在时间
	 	$time = time();
	 	// 查询条数
	 	$len = count($resda);
	 	for($i=0; $i<$len; $i++){
	 		// 获取订单编号
	 		$repaymentnum = $resda[$i]['repaymentnum'];
	 		// 总钱数
	 		$maney 	= $resda[$i]['billamount'];
	 		// 利率
	 		$rate 	= $resda[$i]['rate'];
	 		// 分多少期
	 		$mius= $resda[$i]['periods'];
	 		// 每期要还的钱
			$maney   = sprintf("%.2f",$maney/$mius);
			// 不分期 为一期
	 		if($mius == 1){
	 			$resda[$i]['isstallments'] = '不分期';
	 			// 逾期表单
	 			$rea = M('late_fees_bill')->where("lateFeesNum = $repaymentnum")->find();
	 			// 获取账单状态
	 			$state = $rea['latefeesstatus'];
	 			// 获取预期账单时间
	 			$repaytime = $rea['latefeesdate'];
	 			$time1 = strtotime($repaytime);
	 			
	 			// 如果转态等于付清
	 			if($state == 1){
	 				// 等于一表示已经付清
	 				$resda[$i]['status'] = '<font color="blue">已付清</font>';
	 			// 否则	
	 			}else{
	 				$timediff = $time-$time1;
					$days = intval($timediff/86400);
					if($days == 0){
						$days = 1;
					}
					$resda[$i]['status'] = '<font color="red">已逾期'.$days.'天</font>';
	 			}
	 		// 分期	
	 		}else{
	 			// 根据订单查询出一共有多少期
	 			// 逾期表单
	 			$rea = M('late_fees_bill')->where("lateFeesNum = $repaymentnum")->find();
	 			// 获取预期账单时间
	 			$repaytime = $rea['latefeesdate'];
	 			$time1 = strtotime($repaytime);
	 			if($rea['latefeesstatus'] == 1){
	 				$resda[$i]['status'] = '<font color="blue">已付清</font>';
	 			}else{
					$timediff = $time-$time1;
					$days = intval($timediff/86400);
					if($days == 0){
						$days = 1;
					}
					$resda[$i]['status'] = '<font color="red">已逾期'.$days.'天</font>';
	 			}
	 			$resda[$i]['isstallments'] = '共'.$mius.'期';
	 		}
	 	}

	 	$red[1] = $resda;
	 	return $red;
	}
	// 分期账单
	public function cyclebilling($search){
		$lonk = "u.userid = m.uid and t.billid = u.id and i.id = t.installmentsNum and i.periods > 1";
	 	$resd = $this->searchtime($search,$lonk);
 		$where 	= $resd[1];
 		$red[3] = $resd[2]; // 编号。手机号
 		$red[4] = $resd[3]; // 开始时间
 		$red[5] = $resd[4]; // 结束时间
 		// 现在时间
		$time = time();
	 	$table = M('user_bill');
	 	$count = count($table->table('__USER_BILL__ u, __MEMBER__ m, __BILL_INSTALLMENTS_RELATIONS__ t, __INSTALLMENT__ i')->where($where)->field('u.*, m.iphone')->select());
	 	$page  = new \Think\Page($count, 10);
	 	$show  = $page->show();
	 	$res = $table->table('__USER_BILL__ u, __MEMBER__ m, __BILL_INSTALLMENTS_RELATIONS__ t, __INSTALLMENT__ i')->limit($page->firstRow.','.$page->listRows)->where($where)->field('u.*, m.iphone, i.periods, i.rate, t.id as tid')->select();
	 	$len = count($res);
		for($i=0; $i<$len; $i++){
			// 总金钱
			$maney = $res[$i]['billamount'];
			// 总期数
			$mius = $res[$i]['periods'];
			// 每期还款
			$maney   = sprintf("%.2f",$maney/$mius);
			// 利率
			$rate = $res[$i]['rate'];

			$rentals = 0;
			$relationsid = $res[$i]['tid'];
			$counts = M('installments_repayment_relation')->where("installmentsid = $relationsid")->count();

			// 当前进行得期数
			$res[$i]['periods'] = $counts;
			$n = 0;
			for($j=1; $j<$counts+1; $j++){
				$demo = M('installments_repayment_relation')->order('repaymentCount DESC')->field('repaymentnum, repaymentDate, ispay')->where("installmentsid = $relationsid and repaymentCount = $j")->find();
				// 订单编号
				$repayment = $demo['repaymentnum'];
				// 还款时间
				$timesa    = $demo['repaymentdate']; // 2016-07-31 16:23:52
				$time4	   = strtotime($timesa);
								

				if($demo['ispay'] == 1){
					$n +=1;
					$res[$i]['status'] = '<font color="blue">本期已还清</font>';
				}elseif($demo['ispay'] == 0){
					// 如果现在时间大于还款时间
					if($time > $time4){
						//计算天数
						$timediff = $time-$time4;
						$days = intval($timediff/86400);
						if($days == 0){
							$days = 1;
						}
						$res[$i]['status'] = '<font color="red">已逾期'.$days.'天';
						// 一共要支付的逾期金额
						$ren = $this->calculate($maney, $rate, $relation = 200, $days);
						
						// 逾期金额
						$skt = $ren - $maney;
						$table1 = M('user_interest')->where("overdueid = $repayment")->find();
						$data['overdueid'] 	= $repayment;
						$data['money']		= $skt;
						$data['endtime']	= date("Y-m-d H:i:s", time());
						if($table1 == ''){
							M('user_interest')->add($data);
						}else{
							M('user_interest')->where("overdueid = $repayment")->save($data);
						}
						$res[$i]['maney'] = $ren.' 元';
						$rentals += $ren;
					}else{
						//计算天数
						$timediff = $time4-$time;
						$days = intval($timediff/86400);
						if($days == 0){
							$days = 1;
						}
						$res[$i]['status'] = '<font color="blue">距还款日'.$days.'天</font>';
						$rentals += $maney;
						$res[$i]['maney'] = $rentals.' 元';
					}
				}
			}
				$res[$i]['isstallments'] = '共'.$mius.'期，已付'.$n.'期';
		}
		$red[1] = $res;
		$red[2] = $show;
 	 	return $red;
	}


	/*
		搜索
	 */
	public function searchtime($search,$lonk){
		$iphone = $search['search'];
		$time1  = $search['start_time'];
		$time2 	= $search['end_time'];
		$sear[1] = $lonk;
		if($iphone){
			$sear[1] .= " and u.billNum = $iphone OR m.iphone = $iphone";
			$sear[2] = $iphone;
		}

		if($time1){
			$sear[1] .= " and u.billDate >= '$time1'";
			$sear[3] = $time1;
		}

		if($time2){
			$sear[1] .= " and u.billDate <= '$time2'";
			$sear[4] = $time2;
		}

		if($iphone && $time1){
			$sear[1] .= " and u.billDate >= '$time1' and u.billNum = $iphone OR m.iphone = $iphone";
			$sear[2] = $iphone;
			$sear[3] = $time1;
		}

		if($iphone && $time1 && $time2){
			$sear[1] .= " and u.billDate >= '$time1' and u.billDate <= '$time2' and u.billNum = $iphone OR m.iphone = $iphone";
			$sear[2] = $iphone;
			$sear[3] = $time1;
			$sear[4] = $time2;
		}
		return $sear;
	}

	/**
	 *	逾期
	 *	$maney  	=  应还金额
	 *	$rate   	=  每天利率
	 *	$relation 	=  滞纳金
	 *	$day 		=  时间（天）
	 **/
	public function calculate($maney, $rate, $relation, $days){
		$relation = $maney/0.2;
		if($days == 0){
			$days = 1;
		}

		if($days == 1){
			$rental = $maney + $relation;
		}

		if($days > 1 && $days <= 30){
			$maney = $maney + $relation;
			$rental = sprintf("%.2f",$maney * pow(1+$rate/100, $days-1));
		}

		if($days > 30){
			$maney = $maney + $relation;
			$day = $days-30;
			$rental = sprintf("%.2f",$maney * pow(1+$rate/100, 30-1));
			if($day == 1){
				$rental = $rental + $relation;
			}elseif($day > 1){
				$maney = $rental + $relation;
				$rental = sprintf("%.2f",$maney * pow(1+$rate/100, day-1));
			}
		}

		return $rental;
	}

	/*
		毫秒
	 */
	public 	function microtime_float()
	{
    	list($usec, $sec) = explode(" ", microtime());
    	return ((float)$usec + (float)$sec)*10000;
	}
}