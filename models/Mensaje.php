<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mensaje".
 *
 * @property int $id
 * @property int $reunion_id
 * @property int $user_id
 * @property string $fecha
 * @property string $mensaje
 *
 * @property Reunion $reunion
 * @property User $user
 */
class Mensaje extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mensaje';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reunion_id', 'user_id', 'fecha', 'mensaje'], 'required'],
            [['reunion_id', 'user_id'], 'integer'],
            [['fecha'], 'safe'],
            [['mensaje'], 'string'],
            [['reunion_id', 'user_id', 'fecha'], 'unique', 'targetAttribute' => ['reunion_id', 'user_id', 'fecha']],
            [['reunion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Reunion::className(), 'targetAttribute' => ['reunion_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reunion_id' => 'Reunion ID',
            'user_id' => 'User ID',
            'fecha' => 'Fecha',
            'mensaje' => 'Mensaje',
        ];
    }

    /**
     * Gets query for [[Reunion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReunion()
    {
        return $this->hasOne(Reunion::className(), ['id' => 'reunion_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
