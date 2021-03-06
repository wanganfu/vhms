<?php
needRole('admin');
class UserControl extends Control {
	
	public function __construct()
	{
		parent::__construct();
	}
	public function __destruct()
	{
		parent::__destruct();
	}
	
	public function pageUsers()
	{
		$page = intval($_REQUEST['page']);
		if($page<=0){
			$page = 1;
		}
		$page_count = 25;
		$count = 0;
		$list = daocall('user','pageUsers',array($page,$page_count,&$count));

		$total_page = ceil($count/$page_count);
		if($page>=$total_page){
			$page = $total_page;
		}
		$agents = daocall('agent','selectList',array());
		foreach($agents as $agent) {
			for($i=0;$i<count($list);$i++) {
				if($agent['id'] == $list[$i]['agent_id']) {
					$list[$i]['agent_name'] = $agent['name'];
				}
			}
		}
		
		$this->_tpl->assign('count',$count);
		$this->_tpl->assign('total_page',$total_page);
		$this->_tpl->assign('page',$page);
		$this->_tpl->assign('page_count',$page_count);
		$this->_tpl->assign('list',$list);
		$this->_tpl->display('user/userslist.html');	
		
	}
	public function changUserAgent()
	{
		$username = $_REQUEST['username'];
		$log = array('operate_object'=>'username='.$_REQUEST['username'],'admin'=>getRole('admin'),'operate'=>$_REQUEST['a']);
		apicall('operatelog','operatelogAdd',array($log));
		if(daocall('user','changUser',array($username,$_REQUEST))) {
			header("Location: ?c=user&a=listUser&username=".$username);
		}else{
			$this->_tpl->assign('msg',"更改失败");
			return $this->_tpl->fetch('msg.html');
		}
	}
	public function editForm()
	{
		$user = daocall('user', 'getUser', array($_REQUEST['username']));
		if(!$user){
			return trigger_error("找不到该用户");
		}
		$this->_tpl->assign('user',$user);
		$this->_tpl->display('user/useredit.html');
	}
	public function editMoneyForm()
	{
		$user = daocall('user', 'getUser', array($_REQUEST['username']));
		if(!$user){
			return trigger_error("找不到该用户");
		}
		$this->_tpl->assign('user',$user);
		$this->_tpl->display('user/moneyedit.html');
	}
	public function add(){
		$this->_tpl->display('user/useradd.html');
	}
	public function edit()
	{
		daocall('user','updateUser',array($_REQUEST['username'],$_REQUEST['name'],$_REQUEST['email'],$_REQUEST['id']));
		return $this->listUser();
	}
	public function editMoney()
	{
		$money = 100 * intval($_REQUEST['money']);
		
		if($money>0){
			daocall('user','addMoney', array($_REQUEST['username'],abs($money)));
			/*100管理员操作*/
			$id = daocall('moneyin','add',array($_REQUEST['username'],$money,100));
		}else{
			daocall('user','decMoney', array($_REQUEST['username'],abs($money)));
			$id = daocall('moneyin','add',array($_REQUEST['username'],$_REQUEST['money'],100));
		}
		$log = array('operate_object'=>'username='.$_REQUEST['username'],'mem'=>'meony='.$_REQUEST['money'], 'admin'=>getRole('admin'),'operate'=>$_REQUEST['a']);
		apicall('operatelog','operatelogAdd',array($log));
		
		$this->listUser();
	}
	public function check(){
		$check = daocall('user','getUser',array($_POST['username']));
		if($check) echo 1;
		else echo 0;
		exit;
	}
	public function insert(){
		$data = $_POST;
		//$data['homedir'] = $this->getHomedir($data['username']);
		$data['passwd'] = md5($data['passwd']);
		$data['regtime'] = "now()";
		$uid = apicall('user','insertUser',array($data));
		header("Location: ?c=user&a=listUser");
	}
	public function delete(){
		$arr = $_GET;
		$ret = daocall('user','delUser',array($arr['username']));
		if($ret){
			if($retval == 0){
				header("Location: ?c=user&a=listUser");
			}
			echo 'del whm host and bak host';
		}		
	}
	public function info(){
		$arr = $_GET;
		$ret = daocall('user','getUser',array($arr['username']));
		if($ret){
			$this->_tpl->assign('row',$ret);
			$this->_tpl->display('user/userinfo.html');
		}
		
	}
	public function randPassword()
	{
		$passwd = getRandPasswd();
		if(daocall('user','updatePassword',array($_REQUEST['username'],$passwd))){
			$msg = "新密码是: ".$passwd;
		}else{
			$msg = "重设密码出错";
		}
		$log = array('operate_object'=>'username='.$_REQUEST['username'], 'admin'=>getRole('admin'),'operate'=>$_REQUEST['a']);
		apicall('operatelog','operatelogAdd',array($log));
		
		$this->_tpl->assign('msg',$msg);
		return $this->listUser();		
	}
	public function listUser(){
		$username = $_REQUEST['username'];
		if($username!=""){
			$this->_tpl->assign('username',$username);
			$list = daocall('user','listUser',array($username));
			if(!$list){
				$this->_tpl->assign("msg","没有找到用户:".$username);
			}
		}
		$agents = daocall('agent','selectList',array());
		$this->_tpl->assign('agents',$agents);
		$this->_tpl->assign('sum',count($list));
		$this->_tpl->assign('list',$list);
		$this->_tpl->display('user/userlist.html');

	}
	public function impLogin()
	{
		registerRole('user',$_REQUEST['username']);
		header("Location: /?c=user&a=index");
		die();
	}
}
?>