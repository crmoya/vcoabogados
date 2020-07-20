<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class InvitarForm extends Model
{
    public $nombre;
    public $email;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['nombre', 'email'], 'required'],
            // email has to be a valid email address
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @param string $email the target email address
     * @return bool whether the model passes validation
     */
    public function invitar()
    {        
        $nombre = $this->nombre;
        $email = $this->email."@gmail.com";
        
        $consultor = Tools::getConsultor();
        
        $invitado = Invitados::findOne(['email'=>$email]);
        
        if($invitado==null){
            $invitado = new Invitados();
            $invitado->nombre = $nombre;
            $invitado->email = $email;
            $invitado->save();
        }
        else{
            return false;
        }
        
        if ($this->validate()) {
            /*Yii::$app->mailer->compose('invitacion',[
                                                        'nombre'=>$nombre,
                                                        'consultorNombre'=>$consultor['nombre'],
                                                        'consultorEmail'=>$consultor['email'],
                                                    ])
                ->setTo($email)
                ->setFrom([$consultor['email'] => $consultor['nombre']])
                ->setSubject("Invitación a VideoConferencia Guiresse.com WebVCO")
                ->send();
            */
            return true;
        }
        return false;
    }
}
