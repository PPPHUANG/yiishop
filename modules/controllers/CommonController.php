<?php
namespace app\modules\controllers;
use yii\web\Controller;
use Yii;
class CommonController extends Controller {
    public function init() {
        $admin = isset(Yii::$app->request->cookies['admin']) ? Yii::$app->request->cookies['admin']->value : ['isLogin' => 0];
        if ($admin['isLogin'] != 1) {
            return $this->redirect(['/admin/public/login']);
        }
    }
}
