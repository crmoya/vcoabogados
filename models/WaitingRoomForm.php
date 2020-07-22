<?php
namespace app\models;

use yii\base\Model;

/**
 * Login form
 */
class WaitingRoomForm extends Model
{
    public $participante;
    public $abogado;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['participante','abogado'], 'required'],
        ];
    }
    
    public function attributeLabels() {
        return [
            'participante' => 'Usuario',
            'abogado' => 'Abogado',
        ];
    }

}
