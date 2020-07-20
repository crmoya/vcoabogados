<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Tools;
use app\models\InvitarForm;
use app\models\Invitados;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['cargarusuario','cerrar','limpiarinvitados'],
                        'allow' => true,
                        'roles' => ['*'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    
    public function actionQuitarusuario(){
        Tools::validarConsultor();
        $request = Yii::$app->request;
        $email = $request->post('email');
        Invitados::deleteAll(['email'=>$email]);
    }
    
    
    public function actionDocuments(){
        Tools::validarConsultor();
        return $this->render('documents');
    }
    
    public function actionVco(){
        Tools::validarInvitado();
        return $this->render('vco');
    }
    
    public function actionDocs(){
        Tools::validarInvitado();
        return $this->render('docs');
    }
    
    public function actionSheets(){
        Tools::validarInvitado();
        return $this->render('sheets');
    }
    
    public function actionBroadcast(){
        Tools::validarInvitado();
        return $this->render('broadcast');
    }
    
    public function actionEscritorio(){
        Tools::validarInvitado();
        return $this->render('escritorio');
    }
    
    public function actionEstainvitado(){
        $request = Yii::$app->request;
        $email = $request->post('email');
        $invitado = Invitados::findOne(['email'=>$email]);
        if($invitado != null){
            echo 1;
        }
        else{
            echo 0;
        }
    }
    
    public function actionEstainvitadooconsultor(){
        $request = Yii::$app->request;
        $email = $request->get('email');
        $session = \Yii::$app->session;
        if(!$session->isActive){
            $session->open();
        }
        $email = $session->get('email');
        if($email == null || $email == ""){
            echo 0;
            die;
        }
        $invitado = Invitados::findOne(['email'=>$email]);
        if($invitado == null){
            $consultor = $session->get('consultor');
            if($consultor == null || $consultor == ""){
                echo 0;
                die;
            }
        }
        echo 1;
    }
    
    public function actionConsultores(){
        Tools::validarConsultor();
        return $this->redirect(['consultores/index']);
    }
    
    public function actionInvitar(){
        Tools::validarConsultor();
        $model = new InvitarForm();
        if ($model->load(Yii::$app->request->post())) {
            if($model->invitar()){
                Yii::$app->session->setFlash('exito','Participante invitado exitosamente.');//, se le ha enviado un correo electrónico.');
            }
            else{
                Yii::$app->session->setFlash('error','No pudo enviarse la invitación por correo electrónico. Verifique que el usuario no haya sido ya invitado.');
            }
            return $this->refresh();
        }
        return $this->render('invitar', [
            'model' => $model,
        ]);
    }
    
    public function actionCargarconsultor(){
        Tools::validarInvitado();
        $reunion = \app\models\Reunion::find()->one();
        $respuesta = array('email'=>$reunion->consultores->email,'nombre'=>$reunion->consultor);
        echo json_encode($respuesta);
    }
    
    public function actionCargarusuario(){
        $request = Yii::$app->request;
        $email = $request->post('email');
        $nombre = $request->post('nombre');
        $session = Yii::$app->session;
        if(!$session->isActive){
            $session->open();
        }
        if($email != ""){
            $consultor = \app\models\Consultores::findOne(['email'=>$email]);
            if($consultor != null){
                $session->set('consultor',$email);
                $session->set('nombreconsultor',$nombre);
                
                //crear la reunión
                $reunion = \app\models\Reunion::findOne(['consultor'=>$nombre]);
                if($reunion != null){
                    $reunion->delete();
                }
                $reunion = new \app\models\Reunion();
                $reunion->consultores_id = $consultor->id;
                $reunion->consultor = $nombre;
                $reunion->save();
            }
            else{
                $invitado = Invitados::findOne(['email'=>$email]);
                if($invitado != null){
                    $invitado->conectado = 1;
                    $invitado->save();
                }
            }
            $session->set('email',$email);
        }
    }
    
    public function actionCerrar(){
        $session = Yii::$app->session;
        $session->set('email',null);
        $session->set('consultor',null);
        $this->redirect(['site/index']);
    }
    
    public function actionCerrarsesion(){
        $session = Yii::$app->session;
        $email = $session->get('email');
        $invitado = Invitados::findOne(['email'=>$email]);
        if($invitado != null){
            $invitado->conectado = 0;
            $invitado->save();
        }
        $session->set('email',null);
        $session->set('consultor',null);
    }
  
    public function actionCuentaparticipantes(){
        \app\models\Tools::validarInvitado();
        $invitados = count(Invitados::findAll(['conectado'=>1]));
        echo $invitados;
    }
    
    public function actionConectarme(){
        \app\models\Tools::validarInvitado();
        $request = Yii::$app->request;
        $email = $request->get('email');
        $nombre = $request->get('nombre');
        $invitado = Invitados::findOne(['email'=>$email]);
        $invitado->conectado = 1;
        $invitado->nombre_gmail = $nombre;
        $invitado->save();
        echo "OK";
    }
    
    public function actionDesconectarinvitado(){
        \app\models\Tools::validarConsultor();
        $request = Yii::$app->request;
        $nombre = $request->get('nombre');
        $invitado = Invitados::findOne(['nombre_gmail'=>$nombre]);
        $invitado->conectado = 0;
        $invitado->save();
    }
    
    public function actionLimpiarinvitados(){
        \app\models\Tools::validarConsultor();
        \app\models\Invitados::deleteAll();
        \app\models\Reunion::deleteAll();
    }
    
    public function actionLimpiarconectados(){
        \app\models\Tools::validarConsultor();
        $invitados = \app\models\Invitados::find()->all();
        foreach($invitados as $invitado){
          $invitado->conectado = 0;
          $invitado->save();
        }
    }
        
    public function actionDesconectar(){
        \app\models\Tools::validarInvitado();
        $session = Yii::$app->session;
        if(!$session->isActive){
            $session->open();
        }
        $email = $session->get('email');
        $invitado = Invitados::findOne(['email'=>$email]);
        if($invitado != null){
            $invitado->conectado = 0;
            $invitado->save();
        }
    }
    
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    
    

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //Tools::validarInvitado();
        
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
