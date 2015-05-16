<?php

namespace common\models;

Class ValueHelpers
{
	public static function getRoleValue ($role_name)
	{
		$connection = \Yii::$app->db;
		$sql="SELECT role_value FROM role WHERE role_name=:role_name";
		$command=$connection->createCommand($sql);
		$command=$bindValue(":role_name", $role_name);
		$result=$command->queryOne();
		return $result['role_value'];
		
	
	}
	
	public static function getStatusValue($status_name)
	{
		$connection = \Yii::$app->db;
		$sql= "SELECT status_value FROM status WHERE status_name=:status_name";
		$command=$connection->createCommand($sql);
		$command=$bindValue(":status_name", $status_name);
		$result=$command->queryOne();
		return $result['status_value'];
		
	}
	
	public static function getUserTypeValue($user_type_name)
	{
		$connection= \Yii::$app->db;
		$sql="SELECT user_type_value FROM user_type WHERE user_type_name:=user_type_name";
		$command=$connection->createCommand($sql);
		$commadn=$bindValue(":role_name", $role_name);
		$result=$command->queryOne();
		return $result['user_type_value']
	}
	
}
