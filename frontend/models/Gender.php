<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "gender".
 *
 * @property integer $id
 * @property string $gender_name
 */
class Gender extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gender';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gender_name'], 'string', 'max' => 45],
            [['gender_name'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gender_name' => 'Gender',
        ];
    }
    
    //relation to Profile Table
    public function getProfiles()
    {
        return $this-hasMany(Profile::className(), ['gender_id'=>'id']);
    }
    
    
}
