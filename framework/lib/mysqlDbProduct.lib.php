<?php
class MysqlDbProduct extends DbProduct
{
	public function add($uid,$passwd)
	{
		$user = DbProduct::getUser($uid);
		$sqls = array(
			"CREATE USER '".$user."'@'%' IDENTIFIED BY '".$passwd."'",
			"CREATE DATABASE IF NOT EXISTS `".$user."`",
			"GRANT ALL PRIVILEGES ON `".$user."` . * TO '".$user."'@'%'"
			);
			return $this->query($sqls);
	}
	public function remove($uid)
	{
		$user = DbProduct::getUser($uid);
		$sqls = array(
			"DROP USER '".$user."'@'%'",
			"DROP DATABASE '".$user."'"
			);
			return $this->query($user);
	}
	public function password($uid,$passwd)
	{
		$user = DbProduct::getUser($uid);
		return $this->query($node,array("SET PASSWORD FOR '".$user."'@'%' = PASSWORD( '".$passwd."' )"));		
	}
	public function used($uid)
	{
		$user = DbProduct::getUser($uid);
		$sql = "SELECT sum(Data_length ) + sum( Index_length ) FROM information_schema.`TABLES` WHERE TABLE_SCHEMA = '".$user."'";
		$result = $this->pdo->query($sql);
		if(!$result){
			return 0;
		}
		//echo $sql;
		$row = $result->fetch();
		//print_r($row);
		return $row[0]/1048576;
	}
	private function query(array $sqls)
	{
		for($i=0;$i<count($sqls);$i++){
			$this->pdo->exec($sqls[$i]);
		}
	}
}
?>