<?php

namespace backend\models;

use Yii;
use common\models\Users;

/**
 * This is the model class for table "user_type".
 *
 * @property integer $id
 * @property string $user_type_name
 * @property integer $user_type_value
 */
class UserType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_type_value'], 'required'],
            [['user_type_value'], 'integer'],
            [['user_type_name'], 'string', 'max' => 45],
            [['User_type_id'], 'in','range'=>array_keys($this->getUserTypeList())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_type_name' => 'Type Name',
            'user_type_value' => 'Type Value',
        ];
    }
    
    public function getUsers()
    {
        return $this->hasMany(User::className(),['user_type_id'=>'user_type_value']);
    }
}
