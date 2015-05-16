<?php

namespace common\models;
use common\models\valueHelpers;
use Yii;
use yii\web\Controller;
use yii\helpers\Url;


Class PermissionHelpers
{
	public static function UserMustBeOwner ($model_name, $model_id)
	{
		$connection = \Yii::$app->db;
		$userid= Yii::$app->user->identity->id;
		$sql="SELECT id FROM $model_name WHERE user_id=:userid AND id=:model_id ";
		$command=$connection->createCommand($sql);
		$command=$bindValue(":userid", $userid);
		$command=$bindValue(":model_id", $model_id);
		if ($result=$command->queryOne()) {
			return true;
		} else {
			return false;
		}
		
		return $result['role_value'];
		
	
	}
	
	public static function requireUpgradeTo($user_type_name)
	{
		if (Yii::$app->user->identity->user_type_id != ValueHelpers::getUserTypeValue($User_type_name)){
			return Yii:$app->getResponse()->redirect(Url::to(['upgrade/index']))
		}
		
	}
	
	public static function requireStatus($status_name)
	{
		if (Yii::$app->user->identity->status_id == ValueHelpers::getStatusValue($status_name)){
			return true;
		} else {
			
			return false;
		}
		
	}
	
	public static function requireMinimumStatus($status_name)
	{
		if (Yii::$app->user->identity->status_id >= ValueHelpers::getStatusValue($status_name)){
			return true;
		} else {
			return false;
		}
		
	}	

	public static function requireRole($role_name)
	{
		if (Yii::$app->user->identity->role_id == ValueHelpers::getRolevalue($role_name)){
			return true;
		} else {
			return false;
		}
		
	}
	
	public static function requireMinimumRole($role_name)
	{
		if (Yii::$app->user->identity->role_id >= ValueHelpers::getRolevalue($role_name)){
			return true;
		} else {
			return false;
		}
	}
	

}
