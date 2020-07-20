<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;

use yii\helpers\Url;

/**
 * Description of Tools
 *
 * @author crist
 */
class Tools {
    
    public static function getConsultor(){
        $session = \Yii::$app->session;
        if(!$session->isActive){
            $session->open();
        }
        $consultor = ['email'=>$session->get('consultor'),'nombre'=>$session->get('nombreconsultor')];
        return $consultor;
    }
    
    public static function validarConsultor(){
        $session = \Yii::$app->session;
        if(!$session->isActive){
            $session->open();
        }
        $consultor = $session->get('consultor');
        if($consultor == null || $consultor == ""){
            echo "ERROR: NO HA INICIADO SESIÓN COMO CONSULTOR, POR FAVOR INICIE SESIÓN Y REINTENTE.";
            die;
        }
    }
    
    public static function esConsultor(){
        $session = \Yii::$app->session;
        if(!$session->isActive){
            $session->open();
        }
        $email = $session->get('email');
        $consultor = Consultores::findOne(['email'=>$email]);
        if($consultor == null){
            return false;
        }
        return true;
    }
    
    public static function validarInvitado(){
        $session = \Yii::$app->session;
        if(!$session->isActive){
            $session->open();
        }
        $email = $session->get('email');
        if($email != null){
            $invitado = Invitados::findOne(['email'=>$email]);
            if($invitado == null){
                $consultor = Consultores::findOne(['email'=>$email]);
                if($consultor == null){
                    echo "ERROR: ACTUALMENTE NO ESTÁ INVITADO A ESTA REUNIÓN, POR FAVOR INICIE SESIÓN Y REINTENTE.";
                    die;
                }
            }
        }
        else{
            echo "ERROR: ACTUALMENTE NO ESTÁ INVITADO A ESTA REUNIÓN, POR FAVOR INICIE SESIÓN Y REINTENTE.";
            die;
        }
    }
    
    public static function validarConectado(){
        $session = \Yii::$app->session;
        if(!$session->isActive){
            $session->open();
        }
        $email = $session->get('email');
        if($email == null || $email == ""){
            echo "NO CONECTADO";
            die;
        }
    }
    
    public static function estaConectado(){
        $session = \Yii::$app->session;
        if(!$session->isActive){
            $session->open();
        }
        $email = $session->get('email');
        if($email != null && $email != ""){
            return true;
        }
        return false;
    }
}
