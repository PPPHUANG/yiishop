<?php
namespace app\controllers;
use Yii;
use app\controllers\CommonController;
use app\models\User;
use app\models\Address;
class AddressController extends CommonController {
    public function actionAdd() {
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/auth']);
        }
        $loginname = Yii::$app->session['loginname'];
        $userid = User::find()->where('username = :name',[':name' => $loginname])->one()->userid;
        if (Yii::$app->request->isPost) {
            //echo "string";die;
            $post = Yii::$app->request->post();
            $post['userid'] = $userid;
            $post['address'] = $post['address1'].$post['address2'];
            $data['Address'] = $post;
            $model = new Address;
            $model->load($data);
            $model->save();
        }
        return $this->redirect($_SERVER['HTTP_REFERER']);
    }

    public function actionDel() {
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/auth']);
        }
        $loginname = Yii::$app->session['loginname'];
        $userid = User::find()->where('username = :name',[':name' => $loginname])->one()->userid;
        $addressid = Yii::$app->request->get('addressid');
        if (!Address::find()->where('userid = :uid and addressid = :aid',[':uid' => $userid,':aid' => $addressid])->one()) {
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
        Address::deleteAll('addressid = :aid',[':aid' => $addressid]);
        $this->redirect($_SERVER['HTTP_REFERER']);
    }
}
