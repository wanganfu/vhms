<?php
/**
 * 用户信息信息DAO层基本函数生成
 */
class AdminUserDAO extends DAO{
	
	public function __construct()
	{	//加载基本db文件
		parent::__construct();
		$this->MAP_ARR 	= array(		//用户信息信息字段对照表
			"username" 		=> 'username',
			"passwd" 		=> 'passwd',
			"last_login" 	=> 'last_login',
			"last_ip" 		=> 'last_ip'
		);
		$this->MAP_TYPE = array(
			'passwd'		=>FIELD_TYPE_MD5,
			'last_login'	=>FIELD_TYPE_DATETIME
		);
		$this->_TABLE = DBPRE . 'admin_users';
	}
	/**
	 * 查询用户信息信息
	 */
	public function getUser($username)
	{
		return $this->getData($this->getFieldValue2('username',$username),'row');
	}
	/**
	 * 插入用户信息信息
	 */
	public function insertUser($arr)
	{
		return $this->insertData($arr);
	}
	public function newUser($username,$passwd)
	{
		$arr['username'] = $username;
		$arr['passwd'] = $passwd;
		return $this->insertData($arr);
	}
	/**
	 * 更新用户信息
	 */
	public function updateUser($username, $arr)
	{
		$update_str = $this->updateFields($arr,$this->MAP_ARR);
		$sql = "UPDATE ".$this->_TABLE." SET ".$update_str." WHERE ".$this->getFieldValue2('username', $username);
		return $this->executex($sql);
	}
	public function updatePassword($username,$passwd)
	{
		$sql = "UPDATE ".$this->_TABLE." SET ".$this->getFieldValue2('passwd',$passwd)." WHERE ".$this->getFieldValue2('username', $username);
		return $this->executex($sql);
	}
	/**
	 * 删除用户信息
	 */
	public function delUser($username)
	{
		$tbl = $this->_TABLE;
		if(!$tbl) {
			return false;
		}
		$sql = "DELETE FROM {$tbl} WHERE `username` = '{$username}' LIMIT 1";
		$ret = $this->execute($host, $dbname, $sql);
		if($ret || is_array($ret) && count($ret) == 1) {
			return true;
		}else {
			return false;
		}
	}
	/**
	 * 删除用户信息
	 */
	public function list_user()
	{
		return $this->getData();
	}

}
?>