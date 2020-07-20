<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invitados".
 *
 * @property integer $id
 * @property string $email
 * @property string $nombre
 * @property integer $conectado
 */
class Invitados extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invitados';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'nombre'], 'required'],
            [['conectado'], 'integer'],
            [['email', 'nombre'], 'string', 'max' => 100],
            [['email'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'nombre' => 'Nombre',
            'conectado' => 'Conectado',
        ];
    }
}
