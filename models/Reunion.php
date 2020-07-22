<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reunion".
 *
 * @property int $id
 * @property int $abogado_id
 * @property int $participante_id
 * @property string $fecha
 * @property int $activa
 *
 * @property Mensaje[] $mensajes
 * @property User $abogado
 * @property User $participante
 */
class Reunion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reunion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['abogado_id', 'participante_id', 'fecha', 'activa'], 'required'],
            [['abogado_id', 'participante_id', 'activa'], 'integer'],
            [['fecha'], 'safe'],
            [['participante_id', 'abogado_id'], 'unique', 'targetAttribute' => ['participante_id', 'abogado_id']],
            [['abogado_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['abogado_id' => 'id']],
            [['participante_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['participante_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'abogado_id' => 'Abogado ID',
            'participante_id' => 'Participante ID',
            'fecha' => 'Fecha',
            'activa' => 'Activa',
        ];
    }

    /**
     * Gets query for [[Mensajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMensajes()
    {
        return $this->hasMany(Mensaje::className(), ['reunion_id' => 'id']);
    }

    /**
     * Gets query for [[Abogado]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAbogado()
    {
        return $this->hasOne(User::className(), ['id' => 'abogado_id']);
    }

    /**
     * Gets query for [[Participante]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipante()
    {
        return $this->hasOne(User::className(), ['id' => 'participante_id']);
    }
}
