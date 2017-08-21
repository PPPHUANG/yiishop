<?php
namespace app\modules\controllers;
use yii\web\Controller;
use app\modules\models\Admin;
use Yii;
class PublicController extends controller {
    //登录
    public function actionLogin() {
        $this->layout = false;
        $model = new Admin;
        if(Yii::$app->request->ispost) {
            $post = Yii::$app->request->post();
            if ($model->login($post)) {
                $this->redirect(['default/index']);
                Yii::$app->end();
            }
        }
        return $this->render('login',['model'=>$model]);
    }
    //登出
    public function actionLogout() {
        //删除cookie
        $cookies = Yii::$app->response->cookies;
        $cookies->remove('admin');
        $cookies = Yii::$app->request->cookies;
        if(!$cookies->has('admin')){
            //跳转到登录页面
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        //登出失败返回后台首页
        $this->goback();
    }
    //点击找回密码
    public function actionSeekpassword() {
        $this->layout = false;
        $model = new Admin;
        if (Yii::$app->request->isPost) {
            //接收账号跟邮箱
            $post = Yii::$app->request->post();
            if($model->seekPass($post)) {
                //它一旦在某个请求中设置后，只会在下次请求中有效，然后该数据就会自动被删除
                Yii::$app->session->setFlash('info','电子邮件已发送成功,请查收');
            }
        }
        return $this->render('seekpassword',['model'=>$model]);
    }
}
