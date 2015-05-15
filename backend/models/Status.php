<?php

namespace app\models;
//namespace backend\models
use common\models\User;

use Yii;

/**
 * This is the model class for table "status".
 *
 * @property integer $id
 * @property string $status_name
 * @property integer $status_value
 */
class Status extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status_name', 'status_value'], 'required'],
            [['status_value'], 'integer'],
            [['status_name'], 'string', 'max' => 45],
            ['status_id', 'default','value' => self::STATUS_ACTIVE],
            [['status_id'],'in','range'=>array_keys($this->getStatusList())]
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_name' => 'Status Name',
            'status_value' => 'Status Value',
        ];
    }
    
    public function getUsers()
    {
        return $this->hasMany(User::className(),['status_id'=>'status_value']);
        
    }
}
