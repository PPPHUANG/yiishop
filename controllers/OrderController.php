<?php
namespace app\controllers;
use yii\web\controller;
use app\models\User;
use app\models\Order;
use app\models\OrderDetail;
use app\models\Cart;
use app\models\Product;
use app\models\Address;
use app\controllers\CommonController;
use Yii;
class OrderController extends CommonController {
    public $layout = 'layout1';
    //收银台
    public function actionCheck() {
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/auth']);
        }
        $orderid = Yii::$app->request->get('orderid');
        $status = Order::find()->where('orderid = :oid',[':oid' => $orderid])->one()->status;
        if ($status != Order::CREATEORDER && $status != Order::CHECKORDER) {
            return $this->redirect(['order/index']);
        }
        $loginname = Yii::$app->session['loginname'];
        $userid = User::find()->where('username = :name',[':name' => $loginname])->one()->userid;
        $addresses = Address::find()->where('userid = :uid',[':uid' => $userid])->asArray()->all();
        $details = OrderDetail::find()->where('orderid = :oid',[':oid' => $orderid])->asArray()->all();
        $data = [];
        foreach ($details as $detail) {
            $model = Product::find()->where('productid = :pid',[':pid' => $detail['productid']])->one();
            $detail['title'] = $model->title;
            $detail['cover'] = $model->cover;
            $data[] = $detail;
        }
        $express = Yii::$app->params['express'];
        $expressPrice = Yii::$app->params['expressPrice'];
        return $this->render('check',['express' => $express,'expressPrice' => $expressPrice,'addresses' => $addresses,'products' => $data]);
    }
    //订单中心页面
    public function actionIndex() {
        $this->layout = 'layout2';
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/auth']);
        }
        $loginname = Yii::$app->session['loginname'];
        $userid = User::find()->where('username = :name',[':name' => $loginname])->one()->userid;
        $orders = Order::getProducts($userid);
        //var_dump($orders[0]->products);die;
        return $this->render('index',['orders' => $orders]);
    }
    //添加订单
    public function actionAdd() {
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/auth']);
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (Yii::$app->request->isPost) {
                $post = Yii::$app->request->post();
                $ordermodel = new Order;
                $ordermodel->scenario = "add";
                $usermodel = User::find()->where('username = :name',[':name' => Yii::$app->session['loginname']])->one();
                if (!$usermodel) {
                    throw new \Exception();
                }
                $userid = $usermodel->userid;
                $ordermodel->userid = $userid;
                $ordermodel->status = Order::CREATEORDER;
                $ordermodel->createtime = time();
                if (!$ordermodel->save()) {
                    throw new \Exception();
                }
                $orderid = $ordermodel->getPrimaryKey();
                foreach ($post['OrderDetail'] as $product) {
                    $model = new OrderDetail;
                    $product['orderid'] = $orderid;
                    $product['createtime'] = time();
                    $date['OrderDetail'] = $product;
                    if(!$model->add($date)) {
                        throw new \Exception();
                    }
                    Cart::deleteAll('productid = :pid and userid = :uid',[':pid' => $product['productid'],':uid' => $userid]);
                    product::updateAllCounters(['num' => -$product['productnum']],'productid = :pid',[':pid' => $product['productid']]);
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            //echo 777;
            $transaction->rollback();
            return $this->redirect(['cart/index']);
        }
        return $this->redirect(['order/check','orderid' => $orderid]);
    }
    //确认订单
    public function actionConfirm() {
        //需要更改的字段
        try {
            if (Yii::$app->session['isLogin'] != 1) {
                return $this->redirect(['member/auth']);
            }
            if (!Yii::$app->request->isPost) {
                echo "1";
                throw new \Exception();
            }
            $post = Yii::$app->request->post();
            $loginname = Yii::$app->session['loginname'];
            $usermodel = User::find()->where('username = :name',[':name' => $loginname])->one();
            if (empty($usermodel)) {
                echo "2";
                throw new \Exception();
            }
            $userid = $usermodel->userid;
            $model = Order::find()->where('orderid = :oid or userid = :uid',[':oid' => $post['orderid'],':uid' => $userid])->one();
            if (empty($model)) {
                echo "3";
                throw new \Exception();
            }
            $model->scenario = 'update';
            $post['status'] = Order::CHECKORDER;
            $details = OrderDetail::find()->where('orderid = :oid',[':oid' => $post['orderid']])->all();
            $amount = 0;
            foreach ($details as $detail) {
                $amount +=$detail->productnum*$detail->price;
            }
            if ($amount <= 0) {
                echo "4";
                throw new \Exception();
            }
            $express = Yii::$app->params['expressPrice'][$post['expressid']];
            if ($express < 0) {
                echo "5";
                throw new \Exception();
            }
            $amount += $express;
            $post['amount'] = $amount;
            $data['Order'] = $post;
            //$model->load($data);
            //var_dump($model);die;
            // if (empty($post['addressid'])) {
            //     return $this->redirect(['order/pay','orderid' => $post['orderid'],'paymethod' => $post['paymethod']]);
            // }
            if ($model->load($data) && $model->save()) {
                //var_dump($post);
                return $this->redirect(['order/pay','orderid' => $post['orderid'],'paymethod' => $post['paymethod']]);
            }
        } catch (\Exception $e) {
           return $this->redirect(['index/index']);
           echo "订单出错";
        }
    }
    //在线支付
    public function actionPay() {
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/auth']);
        }
        $orderid = Yii::$app->request->get('orderid');
        $paymethod = Yii::$app->request->get('paymethod');
        $result = Order::updateAll(['status' => Order::PAYSUCCESS],'orderid = :oid',[':oid' => $orderid]);
        if ($result) {
            return $this->redirect(['index/index']);
        }      
    }
    //查询物流信息
    public function actionGetexpress() {
        $expressno = Yii::$app->request->get('expressno');
        $res = Express::search($expressno);
        echo $res;
        exit;
    }
    //确认收货
    public function actionReceived() {
        $orderid = Yii::$app->request->get('orderid');
        $order = Order::find()->where('orderid = :oid',[':oid' => $orderid])->one();
        if (!empty($order) && $order->status == Order::SENDED) {
            $order->status = Order::RECEIVED;
            $order->save();
        }
        return $this->redirect(['order/index']);
    }
}
