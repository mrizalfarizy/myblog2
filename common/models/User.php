<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Security;
use backend\models\Role;
use backend\models\UserType;
use backend\models\Status;
use frontend\models\Profile;


/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    //const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        //return '{{%user}}';
          return 'user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            //TimestampBehavior::className(),
            'timestamp'=>[
              'class'=> 'yii\behaviors\TimestampBehavior',
              'attributes'=>[
                  ActiveRecord::EVENT_BEFORE_INSERT=>['created_at','updated_at'],
                  ActiveRecord::EVENT_BEFORE_UPDATE=>['updated_at'],
              ],  
              'value'=>new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['status_id', 'default', 'value' => self::STATUS_ACTIVE],
            [['status'], 'in', 'range' => array_keys($this->getStatusList())],
            //['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            
            ['role_id','default', 'value'=>10],
            [['role_id'], 'in', 'range' => array_keys($this->getRoleList())],
            
            ['user_type_id','default', 'value'=>10],
            [['user_type_id'], 'in', 'range' => array_keys($this->getUserTypeList())],
            
            ['username','filter', 'filter'=>'trim'],
            ['username', 'required'],
            ['username', 'unique'],
            ['username','string','min'=>2,'max'=>255],
            
            ['email','filter','filter'=>'trim'],
            ['email','required'],
            ['email','email'],
            ['email','unique'],
        ];
    }
    
    public function attributeLabels()
    {
        return [
          'roleName'=>Yii::t('app','Role'),
          'statusName'=>Yii::t('app','status'),
          'profileId'=>Yii::t('app','Profile'),  
          'profileLink'=>Yii::t('app','Profile'),
          'userLink'=>Yii::t('app','User'),
          'username'=>Yii::t('app','User'),
          'UserTypeName'=>Yii::t('app','User Type'),
          'userTypeId'=>Yii::t('app','User Type'),
          'userIdLink'=>Yii::t('app','ID'),  
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status_id' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key'=>$token]);
        //throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status_id' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        /*if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }*/
        
        $expire=Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp=(int) end($parts);
        if ($timestamp+$expire<time()){
            //token expired
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status_id' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    
    public function getUsername()
    {
        return $this->username;
    }
    
   /* 
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id'=>'id']);
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
        ];
    }*/

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
    
    //relation Role table
    Public function getRole()
    {
        return $this->hasOne(Role::className(),['role_value'=>'role_id']);
        
    }
    
    Public function getRoleName()
    {
        return $this->role ? $this->role->role_name : '- no role -';
        
    }
    
    public static function getRoleList()
    {
        $droptions = Role::find()->asArray()->all();
        return Arrayhelper::map($droptions,'role_value', 'role_name');
        
    }
    
    //relation status  table
    public function getStatus()
    {
        return $this->hasOne(Status::className(),['status_value'=>'status_id']);
    }
    
    public function getStatusName()
    {
        return $this->status ? $this->status->status_name : '- no status -';
    }
    
    public static function getStatusList()
    {
        $dropoptions = Status::find()->asArray()->all();
        return Arrayhelper::map($dropoptions,'status_value','status_name');
    }

    //relation usertype Table
    public function getUserType()
    {
        return $this->hasOne(UserType::className(),['user_type_value','user_type_id']);
    }
    
    public function getUserTypeName()
    {
       return $this->userType ? $this->userType->user_type_name : '- No Type Name - ' ;
    }
    
    public static function getUserTypeList()
    {
        $dropoptions = UserType::find()->asArray()->all();
        return Arrayhelper::map($dropoptions,'user_type_value','user_type_name');
    }

    public function getUserTypeId()
    {
        return $this->userType ? $this->userType->id : 'none';
    }
    
    public function getUserIdLink()
    {
        $url=Url::to(['user/update','id'=>$this->UserId]);
        $options = [];
        return Html::a($this->id,$url,$options);
    }
    
    public function getUserLink()
    {
        $url=Url::to(['user/view','id'=>$this->Id]);
        $options = [];
        return Html::a($this->username,$url,$options);
    }
    
    //relation to profile
    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id','id']);
    }
    
    public function getProfileId()
    {
        return $this->profile ? $this->profile->id : 'none';
    }
    
    public function getProfileLink()
    {
        $url=Url::to(['profile/view','id'=>$this->profileId]);
        $options=[];
        return Html::a($this->profile ? 'profile':'none',$url,$options);
    }


}



