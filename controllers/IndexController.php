<?php
namespace app\controllers;

use app\controllers\CommonController;
use app\models\Product;

class IndexController extends CommonController
{
    public function actionIndex() {
         $this->layout = 'layout1';
         $data['tui'] = Product::find()->where("istui = '1' and ison = '1'")->orderby('createtime desc')->limit(4)->all();
          $data['new'] = Product::find()->where("ison = '1'")->orderby('createtime desc')->limit(4)->all();
           $data['hot'] = Product::find()->where("ison = '1' and ison = '1'")->orderby('createtime desc')->limit(4)->all();
            $data['all'] = Product::find()->where("ison = '1'")->orderby('createtime desc')->limit(7)->all();
            //var_dump($data['all']);die;
         return $this->render('index',['data' => $data]);
        //return $this->renderPartial('index');不使用布局

    }
}
