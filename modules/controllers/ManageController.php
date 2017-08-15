<?php
namespace app\modules\controllers;

use yii\web\Controller;
use Yii;
use app\modules\models\Admin;
use yii\data\Pagination;
use app\modules\controllers\CommonController;

class ManageController extends CommonController {
    //点击邮件链接进入此动作
    public function actionMailchangepass() {
        $this->layout = false;
        $time = Yii::$app->request->get("timestamp");
        $adminuser = Yii::$app->request->get('adminuser');
        $token = Yii::$app->request->get('token');
        $model = new Admin;
        $myToken = $model->createToken($adminuser,$time);
        if ($myToken!=$token) {
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        if (time()-$time>300) {
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->Post();
            if ($model->emailchangePass($post)) {
                Yii::$app->session->setFlash('info','密码修改成功');
            }
        }
        $model->adminuser = $adminuser;
        return $this->render('mailchangepass',['model' => $model]);
    }

    //管理员列表
    public function actionManagers() {
        $this->layout = 'layout1';
        $model = Admin::find();
        $pageSize = Yii::$app->params['pageSize']['manage'];
        $count = $model->count();
        $pager = new Pagination(['totalCount' => $count,'pageSize' => $pageSize]);
        $managers = $model->offset($pager->offset)->limit($pager->limit)->all();
        return $this->render("managers",['managers' =>$managers,'pager' => $pager]);
    }

    //注册管理员
    public function actionReg() {
        $this->layout = 'layout1';
        $model = new Admin;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->reg($post)) {
                Yii::$app->session->setFlash('info','添加成功');
            } else {
                Yii::$app->session->setFlash('info','添加失败');
            }
            $model->adminpass = '';
            $model->repass = '';
        }
        return $this->render('reg',['model' => $model]);
    }

    //删除管理员
    public function actionDel() {
        $adminid = (int)Yii::$app->request->get("adminid");
        if (empty($adminid)) {
            $this->redirect(['manage/managers']);
        }
        $model = new Admin;
        if($model->deleteAll('adminid = :id',[':id' => $adminid])) {
            Yii::$app->session->setFlash('info','删除成功');
            $this->redirect(['manage/managers']);
        }
    }

    //更改管理员邮箱
    public function actionChangeemail() {
        $this->layout = "layout1";
        $model = Admin::find()->where('adminuser = :user',[':user' => Yii::$app->session['admin']['adminuser']])->one();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->changeemail($post)) {
                Yii::$app->session->setFlash('info','修改成功');
            }
        }
        $model->adminpass = "";
        return $this->render('changeemail',['model' => $model]);
    }

    //更改密码
    public function actionChangepass() {
        $this->layout = "layout1";
        $model = Admin::find()->where('adminuser = :user',[':user' => Yii::$app->request->cookies['admin']->value['adminuser']])->one();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->changePass($post)) {
                Yii::$app->session->setFlash('info','修改成功');
            }
        }
        $model->adminpass = "";
        $model->repass = '';
        $model->oldpass = '';
        return $this->render('changepass',['model' => $model]);
    }

}
