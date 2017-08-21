<?php
namespace app\modules\controllers;
use yii\web\Controller;
use app\models\User;
use app\models\profile;
use yii\data\Pagination;
use app\modules\controllers\CommonController;
use Yii;
class UserController extends CommonController {
    //加入新用户
    public function actionReg() {
        $this->layout = 'layout1';
        $model = new User;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->reg($post)) {
                Yii::$app->session->setFlash('info','添加成功');
            } else {
                Yii::$app->session->setFlash('info','添加失败');
            }

        }
        $model->userpass = '';
        $model->repass = '';
        return $this->render('reg',['model' => $model]);
    }
    //用户列表
    public function actionUsers() {
        $this->layout = "layout1";
        //联表查询 profile与model中的getprofile后缀一致即可
        $model = User::find()->joinWith('profile');
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['user'];
        $pager = new Pagination(['totalCount' => $count,'pageSize' => $pageSize]);
        $users = $model->offset($pager->offset)->limit($pager->limit)->all();
        //遍历users的值user会发现关联查询的字段在user中的所联表名对象下的属性中
        return $this->render('users',['users' => $users,'pager' => $pager]);
    }
    //删除用户
    public function actionDel() {
        try {
            $userid = Yii::$app->request->get('userid');
            if (empty($userid)) {
                throw new \Exception();
            }
            $trans = Yii::$app->db->beginTransaction();
            if ($obj = profile::find()->where("userid = :id",[':id' => $userid])->one()) {
                $res = profile::deleteAll('userid = :id',[':id' => $userid]);
                if (empty($res)) {
                    throw new \Exception();
                }
            }
            if (!User::deleteAll('userid=:id',[':id' => $userid])) {
                throw new \Exception();
            }
            $trans->commit();
        } catch(\Exception $e) {
            if (Yii::$app->db->getTransaction()) {
                $trans->rollback();
            }
        }
        $this->redirect(['user/users']);
    }
}
