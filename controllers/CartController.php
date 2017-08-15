<?php
namespace app\controllers;
use app\controllers\CommonController;
use app\models\User;
use app\models\Cart;
use app\models\Product;
use Yii;
class CartController extends CommonController {
    public $layout = 'layout1';
    //购物车
    public function actionIndex() {
        if (Yii::$app->session['isLogin'] != 1) {
            $this->redirect(['member/auth']);
        }
        $userid = User::find()->where('username = :name',[':name' => Yii::$app->session['loginname']])->one()->userid;
        $cart = Cart::find()->where('userid = :uid',[':uid' => $userid])->asArray()->all();
        $data = [];
        foreach ($cart as $k => $pro) {
            $product = Product::find()->where('productid = :pid',[':pid' => $pro['productid']])->one();
            $data[$k]['cover'] = $product->cover;
            $data[$k]['title'] = $product->title;
            $data[$k]['productnum'] = $pro['productnum'];
            $data[$k]['price'] = $pro['price'];
            $data[$k]['productid'] = $pro['productid'];
            $data[$k]['cartid'] = $pro['cartid'];
        }
        return $this->render('index',['data' => $data]);
    }
    //购物车添加商品
    public function actionAdd() {
        if (Yii::$app->session['isLogin'] != 1) {
            return $this->redirect(['member/auth']);
        }
        $userid = User::find()->where('username = :name',[':name' => Yii::$app->session['loginname']])->one()->userid;
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $num = Yii::$app->request->post()['productnum'];
            $data['Cart'] = $post;
            $data['Cart']['userid'] = $userid;
        }
        if (Yii::$app->request->isGet) {
            $productid = Yii::$app->request->get('productid');
            $model = Product::find()->where('productid = :pid',[':pid' => $productid])->one();
            $price = $model->issale ? $model->saleprice : $model->price;
            $num = 1;
            $data['Cart'] = ['productid' => $productid,'productnum' => $num,'price' => $price,'userid' => $userid];
        }
        if (!$model = Cart::find()->where('productid = :pid and userid = :uid',[':pid' => $data['Cart']['productid'],':uid' => $data['Cart']['userid']])->one()) {
            $model = new Cart;
        } else {
            $data['Cart']['productnum'] = $model->productnum + $num;
        }
        $data['Cart']['createtime'] = time();
        $model->load($data);
        $model->save();
        return $this->redirect(['cart/index']);
    }

    //删除
    public function actionDel() {
        $cartid = Yii::$app->request->get('cartid');
        Cart::deleteAll('cartid = :cid',[':cid' => $cartid]);
        return $this->redirect(['cart/index']);
    }
    //更改数量
    public function actionMod() {
        $cartid = Yii::$app->request->get('cartid');
        $productnum = Yii::$app->request->get('productnum');
        Cart::updateAll(['productnum' => $productnum],'cartid = :cid',[':cid' => $cartid]);
    }
    //搜索
    public function actionSearch() {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($post['cate'] == 'all') {
                $model = Product::findBySql("select * from shop_product where title like '%{$post['keyword']}%'")->all();
                //var_dump($model);die; 
                return $this->render('search',['model' => $model]);
            }else {
                $model = Product::findBySql("select * from shop_product where title like '%{$post['keyword']}%'")->joinWith('parentid')->where('parentid = :pid',[':pid' => 14])->all();
                return $this->render('search',['model' => $model]);
            }
        } else {
            return $this->redirect(['index/index']);
        }
    }


}
