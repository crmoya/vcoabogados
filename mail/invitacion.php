<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <p>
        Estimado(a) <?=$nombre?><br/>
        Ha sido invitado(a) a participar en una video conferencia en el sitio Guiresse.com<br/>
        Para aceptar esta invitación y unirse a la video conferencia, por favor haga click sobre
        el siguiente link</br><br/>
        <a class='btn btn-default' href='<?=Url::home('https')?>'>Unirme a la video conferencia</a>

    </p>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


