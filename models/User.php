<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

use app\validators\RutValidator;

class User extends ActiveRecord implements IdentityInterface
{

    public $password_repeat;

    public static function tableName()
    {
        return 'user';
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

     /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        return static::findOne(['username' => $username]);
    }

    /**
     * {@inheritdoc}
     * @param \Lcobucci\JWT\Token $token
     * @return static|null
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['id' => $token->getClaim('uid')]);
    }

    /**
     * Finds user by string token
     *
     * @param string $token
     * @return static|null
     */
    public static function findIdentityByStringToken($stringtoken)
    {
        $token = Yii::$app->jwt->getParser()->parse((string) $stringtoken); // Parses from a string
        return static::findOne(['id' => $token->getClaim('uid')]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    
    public function attributeLabels()
    {
        return [
            'username' => 'RUT',
            'name' => 'Nombre',
            'password_repeat' => 'Repetir clave',
            'password' => 'Clave',
        ];
    }

    public function rules()
    {
        return [
            [['username','password','password_repeat','name','rol'], 'required'],
            [['username'], RutValidator::class],
            [['username'], 'string', 'max' => 15],
            [['name'], 'string', 'max' => 200],
            ['username','unique'],
            ['password', 'compare', 'compareAttribute' => 'password_repeat'],
        ];
    }

}