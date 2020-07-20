<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "reunion".
 *
 * @property integer $id
 * @property string $consultor
 * @property integer $consultores_id
 *
 * @property Consultores $consultores
 */
class Reunion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'reunion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['consultor', 'consultores_id'], 'required'],
            [['consultores_id'], 'integer'],
            [['consultor'], 'string', 'max' => 100],
            [['consultores_id'], 'exist', 'skipOnError' => true, 'targetClass' => Consultores::className(), 'targetAttribute' => ['consultores_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'consultor' => 'Consultor',
            'consultores_id' => 'Consultores ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConsultores()
    {
        return $this->hasOne(Consultores::className(), ['id' => 'consultores_id']);
    }
}
