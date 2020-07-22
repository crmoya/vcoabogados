<?php

namespace app\controllers;

use app\models\Reunion;
use app\models\WaitingRoomForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ConsultoresController implements the CRUD actions for Consultores model.
 */
class VcoController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'only' => ['room'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['room'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionWaitingRoom()
    {
        if(Yii::$app->user->can("abogado")){
            $model = new WaitingRoomForm();
            if ($model->load(Yii::$app->request->post())) {
                $model->abogado = Yii::$app->user->identity->id;
                $reunion = Reunion::find()->where(['abogado_id'=>Yii::$app->user->identity->id,'activa'=>1])->one();
                if(!isset($reunion)){
                    $reunion = new Reunion();
                    $reunion->abogado_id = $model->abogado;
                    $reunion->participante_id = $model->participante;
                    $reunion->fecha = date("Y-m-d H:i:s");
                    $reunion->activa = 1;
                    $reunion->save();
                }

                $tipo = "abogado";
                return $this->render('room', [
                    'tipo' => $tipo,
                    'reunion' => $reunion,
                ]);
                return $this->render('vco/room',['reunion'=>$reunion]);
            } 
            return $this->render('waiting-room',['model' => $model]);
        }
        
        if(Yii::$app->user->can("participante")){
            $reunion = Reunion::find()->where(['participante_id'=>Yii::$app->user->identity->id,'activa'=>1])->one();
            $model = new WaitingRoomForm();
            if(isset($reunion)){
                if (Yii::$app->request->post()) {
                    $model->participante = $reunion->participante_id;
                    $model->abogado = $reunion->abogado_id;
                    $tipo = "participante";
                    return $this->render('room', [
                        'tipo' => $tipo,
                        'reunion' => $reunion,
                    ]);
                } 
                return $this->render('guest-room',['model' => $model,'reunion'=>$reunion]);
            }
            return $this->render('error');
        }
    }

}
