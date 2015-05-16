<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\Users;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "profile".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $birthdate
 * @property integer $gender_id
 * @property string $created_at
 * @property string $updated_at
 */
class Profile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'gender_id','birthdate'], 'required'],
            [['user_id', 'gender_id'], 'integer'],
            [['first_name', 'last_name'], 'string','max'=>45],
            [['birthdate', 'created_at', 'updated_at'], 'safe'],
            [['birthdate'],'date','format'=>'d-m-Y'],
            [['gender_id'],'in','range'=>array_keys($this-getGenderList())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            //'user_id' => 'User ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'birthdate' => 'Birthdate',
            'gender_id' => 'Gender ID',
            'created_at' => 'Member Since',
            'updated_at' => 'Last Update',
            'genderName'=> Yii::t('app','Gender'),
            'UserLink' => Yii::t('app','User'),
            'profileIdLink' => Yii::t('app','profile'),
        ];
    }
    
    public function behaviors()
    {
        return [
            'timestamp'=>[
            'class'=>'yii\behaviors\TimestampBehavior',
            'attributes'=>[
              ActiveRecord::EVENT_BEFORE_INSERT=>['created_at','updated_at'],
              ActiveRecord::EVENT_BEFORE_UPDATE=>['updated_at'],   
            ],
            'value'=>new Expression('NOW()'),    
            ],
        ];
    }
    
    //relation to gender table
    public function getGender()
    {
        return $this->hasOne(Gender::className(),['id'=>'gender_id']);
    }
    
    public function getGenderName()
    {
        return $this->gender->gender_name;
    }
    
    public static function getGenderList()
    {
        $dropoptions = Gender::find()->asArray()->all();
        return Arrayhelper::map($dropoptions,'id','gender_name');
    }
    
    //relation User table 
    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }
    
    public function getUsername()
    {
        return $this->user->username;
    }
    
    public function getUserId()
    {
        return $this->user ? $this->user->id:'none';
    }
    
    public function getUserLink()
    {
        $url=Url::to(['user/view','id'=>$this->UserId]);
        $options = [];
        return Html::a($this->getUserName(),$url,$options);
    }
    
    public function getProfileIdLink()
    {
        $url=Url::to(['profile/update','id'=>$this->Id]);
        $options = [];
        return Html::a($this->id,$url,$options);
    }
    
}
