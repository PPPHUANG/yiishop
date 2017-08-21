<?php
namespace app\controllers;
use yii\web\Controller;
use app\models\User;
use app\controllers\CommonController;
use Yii;
class MemberController extends CommonController {
    public function actionAuth() {
        $this->layout = 'layout2';
        $model = new User;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            //var_dump($post);die;
            if ($model->login($post)) {
                //Yii::$app->request->referrer上次访问地址
                //goback()是跳转回上一次访问的地址，如果传入参数则会跳转参数指定地址。
                return $this->goBack(Yii::$app->request->referrer);
            }
        }
        return $this->render('auth',['model' => $model]);
    }
    //邮件注册
    public function actionReg() {
        $this->layout = 'layout2';
        $model = new User;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->regByMile($post)) {
                Yii::$app->session->setFlash('info','电子邮件发送成功');
            } else {
                Yii::$app->session->setFlash('info','注册失败');
            }
        }
        $model->loginname = $model->username;
        return $this->render('auth',['model' => $model]);
    }
    //登出
    public function actionLogout() {
        Yii::$app->session->remove('loginname');
        Yii::$app->session->remove('isLogin');
        if (!isset(Yii::$app->session['isLogin'])) {
            return $this->goBack(Yii::$app->request->referrer);
        }
    }
}
