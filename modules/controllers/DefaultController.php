<?php
namespace app\modules\controllers;
use app\modules\controllers\CommonController;
use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends CommonController
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'layout1';
        return $this->render('index');
    }
}
