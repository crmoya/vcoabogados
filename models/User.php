<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string|null $access_token
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'username', 'auth_key', 'password_hash'], 'required'],
            [['id'], 'integer'],
            [['access_token'], 'string', 'max' => 60],
            [['username', 'password_hash'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
        ];
    }

    public static function getParticipantes(){
        return User::find()->join("INNER JOIN","auth_assignment","user.id = auth_assignment.user_id")->where(['item_name'=>'participante'])->all();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        ];
    }

    public static function findByUsername($username) {
        return static::findOne(['username' => $username]);
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public static function getUsername($id) {
        $user = static::findOne(['id' => $id]);
        return (empty($user)) ? "" : $user->username;
    }
}
