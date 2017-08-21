<?php
namespace app\modules\controllers;
use yii\web\Controller;
use yii\data\Pagination;
use app\models\User;
use app\models\Address;
use app\models\Order;
use app\modules\controllers\CommonController;
use Yii;
class OrderController extends CommonController {
    public function actionList() {
        $this->layout = "layout1";
        $model = Order::find();
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['order'];
        $pager = new Pagination(['totalCount' => $count,'pageSize' => $pageSize]);
        $data = $model->offset($pager->offset)->limit($pager->limit)->all();
        $data = Order::getDetail($data);
        return $this->render('list',['pager' =>$pager,'orders' => $data]);
    }
    //查看订单详情
    public function actionDetail() {
        $this->layout = 'layout1';
        $orderid = Yii::$app->request->get('orderid');
        $order = Order::find()->where('orderid = :oid',[':oid' => $orderid])->one();
        $data = Order::getData($order);
        return $this->render('detail',['order' => $data]);
    }
    //发货
    public function actionSend() {
        $this->layout = 'layout1';
        $orderid = (int)Yii::$app->request->get('orderid');
        $model = Order::find()->where('orderid = :oid',[':oid' => $orderid])->one();
        $model->scenario = 'send';
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $model->status = Order::SENDED;
            if ($model->load($post) && $model->save()) {
                Yii::$app->session->setFlash('info','发货成功');
            }
        }
        return $this->render('send',['model' => $model]);
    }
}
