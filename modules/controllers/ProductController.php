<?php
namespace app\modules\controllers;
use yii\web\Controller;
use app\models\Product;
use app\models\Category;
use Yii;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use Qiniu\Storage\BucketManager;
use yii\data\Pagination;
use app\modules\controllers\CommonController;
class ProductController extends CommonController {
    //商品列表
    public function actionList() {
        $this->layout = 'layout1';
        $model = Product::find();
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['product'];
        $pager = new Pagination(['totalCount' => $count,'pageSize' => $pageSize]);
        $products = $model->offset($pager->offset)->limit($pager->limit)->all();
        return $this->render('list',['products' => $products,'pager' => $pager]);
    }
    //商品添加
    public function actionAdd() {
        $this->layout = 'layout1';
        $model = new Product;
        $cate = new Category;
        $list = $cate->getOptions();
        unset($list[0]);
        if (Yii::$app->request->isPost) {
            $post= Yii::$app->request->post();
            //var_dump($post);
            //print_r($_FILES);die;
            $pics = $this->upload();
            if (!$pics) {
                $model->addError('cover','封面不能为空');
            } else {
                $post['Product']['cover'] = $pics['cover'];
                $post['Product']['pics'] = $pics['pics'];
            }
            if ($pics && $model->add($post)) {
                Yii::$app->session->setFlash('info','添加成功');
            }
        }
        return $this->render('add',['model' => $model,'opts' => $list]);
    }

    //上传图片
    private function upload() {
        require "qiniu/autoload.php";
        if ($_FILES['Product']['error']['cover'] > 0) {
            return false;
        }
        $accessKey = 'FLamVr6NXXkvy00gcC-4LWkO2Bwfa6KZxXAhe8Jt';
        $secretKey = 'dSHR2Hzu_2fc36VrzXDG0OfzqUpKubo9c_RJsEpm';
        $auth = new Auth($accessKey, $secretKey);
        // 空间名
        $bucket = 'pengpenghuang';
        // 生成上传Token
        $token = $auth->uploadToken($bucket);

        $filePath = $_FILES['Product']['tmp_name']['cover'];
        // 上传到七牛后保存的文件名
        $key = uniqid();
        // 初始化 UploadManager 对象并进行文件的上传
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
        var_dump($err);
        } else {
        $cover =  'http://on1v47dqg.bkt.clouddn.com/'.$ret['key'];
        }
        if ($_FILES['Product']['error']['pics'] > 0) {
            return false;
        }
        $filePath = $_FILES['Product']['tmp_name']['pics'];
        // 上传到七牛后保存的文件名
        $key = uniqid();
        list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
        if ($err !== null) {
        var_dump($err);
        } else {
        $pics =
        'http://on1v47dqg.bkt.clouddn.com/'.$ret['key'];
        }
        return ['cover' => $cover,'pics' => $pics];
    }
    //删除
    public function actionMod() {
        $this->layout = 'layout1';
        $productid = Yii::$app->request->get('productid');
        $model = Product::findone($productid);
        //接收到修改数据
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $arr = $this->removepics($model,$post);
            if ($arr) {
                $model = $arr[0];
                $post = $arr[1];
                if($model->add($post)) {
                    Yii::$app->session->setFlash('info','修改成功');
                }else {
                    Yii::$app->session->setFlash('info','修改失败');
                }
            }else {
                Yii::$app->session->setFlash('info','修改失败');
            }
        }
        $cate = new Category;
        $list = $cate->getOptions();
        unset($list[0]);
        Yii::$app->session->setFlash('cover',$model->cover);
        Yii::$app->session->setFlash('pics',$model->pics);
        return $this->render('mod',['model' => $model,'opts' => $list]);
    }

    //删除图片
    public function removepics($model,$post) {
        $arr = [];
        require "qiniu/autoload.php";
        $accessKey = 'FLamVr6NXXkvy00gcC-4LWkO2Bwfa6KZxXAhe8Jt';
        $secretKey = 'dSHR2Hzu_2fc36VrzXDG0OfzqUpKubo9c_RJsEpm';
        $auth = new Auth($accessKey, $secretKey);
        // 空间名
        $bucket = 'pengpenghuang';

        $cover = $_FILES['Product']['error']['cover'];
        $pics = $_FILES['Product']['error']['pics'];
        if ($cover == 4 && $pics == 4) {
            $post['Product']['cover'] = $model->cover;
            $post['Product']['pics'] = $model->pics;
            $arr[] = $model;
            $arr[] =$post;
            return $arr;
        }
        if ($cover == 4 && $pics == 0) {
            $post['Product']['cover'] = $model->cover;
            $keypics = $model->pics;
            // 初始化BucketManager
            $bucketMgr = new BucketManager($auth);
            //删除原图
            $err = $bucketMgr->delete($bucket, $keypics);
            //上传新图
            $token = $auth->uploadToken($bucket);
            $filePath = $_FILES['Product']['tmp_name']['pics'];
            // 上传到七牛后保存的文件名
            $key = uniqid();
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            $post['Product']['pics'] = 'http://on1v47dqg.bkt.clouddn.com/'.$ret['key'];
            $arr[] = $model;
            $arr[] =$post;
            return $arr;
        }
        if ($cover == 0 && $pics == 4) {
            $post['Product']['pics'] = $model->pics;
            $keycover = $model->cover;
            // 初始化BucketManager
            $bucketMgr = new BucketManager($auth);
            //删除原图
            $err = $bucketMgr->delete($bucket, $keycover);
            //上传新图
            $token = $auth->uploadToken($bucket);
            $filePath = $_FILES['Product']['tmp_name']['cover'];
            // 上传到七牛后保存的文件名
            $key = uniqid();
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            $post['Product']['cover'] = 'http://on1v47dqg.bkt.clouddn.com/'.$ret['key'];
            $arr[] = $model;
            $arr[] =$post;
            return $arr;
        }
        if ($cover == 0 && $pics == 0) {
            $keycover = $model->cover;
            $keypics = $model->pics;
            // 初始化BucketManager
            $bucketMgr = new BucketManager($auth);
            //删除原图
            $err = $bucketMgr->delete($bucket, $keycover);
            $err = $bucketMgr->delete($bucket, $keypics);
            //上传新图
            $token = $auth->uploadToken($bucket);
            $filePathcover = $_FILES['Product']['tmp_name']['cover'];
            $filePathpics = $_FILES['Product']['tmp_name']['pics'];
            // 上传到七牛后保存的文件名
            $keycover = uniqid();
            $keypics = uniqid();
            // 初始化 UploadManager 对象并进行文件的上传
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传
            list($retcover, $errcover) = $uploadMgr->putFile($token, $keycover, $filePathcover);
            list($retpics, $errpics) = $uploadMgr->putFile($token, $keypics, $filePathpics);
            $post['Product']['cover'] = 'http://on1v47dqg.bkt.clouddn.com/'.$retcover['key'];
            $post['Product']['pics'] = 'http://on1v47dqg.bkt.clouddn.com/'.$retpics['key'];
            $arr[] = $model;
            $arr[] =$post;
            return $arr;
        }
        return false;
    }
    //删除
    public function actionDel() {
        $productid = Yii::$app->request->get('productid');
        $product = Product::findone($productid);
        $keycover = substr($product->cover,-13);
        $keypics = substr($product->pics,-13);
        require "qiniu/autoload.php";
        $accessKey = 'FLamVr6NXXkvy00gcC-4LWkO2Bwfa6KZxXAhe8Jt';
        $secretKey = 'dSHR2Hzu_2fc36VrzXDG0OfzqUpKubo9c_RJsEpm';
        $auth = new Auth($accessKey, $secretKey);
        // 空间名
        $bucket = 'pengpenghuang';
        // 初始化BucketManager
        $bucketMgr = new BucketManager($auth);

        $err = $bucketMgr->delete($bucket, $keycover);
        $err = $bucketMgr->delete($bucket, $keypics);
        Product::deleteAll('productid = :id',[':id' => $productid]);
        return $this->redirect(['product/list']);
    }
    //上架
    public function actionOn() {
        $productid = Yii::$app->request->get('productid');
        Product::updateAll(['ison' => '1'],'productid = :id',[':id' => $productid]);
        return $this->redirect(['product/list']);
    }
    //下架
    public function actionOff() {
        $productid = Yii::$app->request->get('productid');
        Product::updateAll(['ison' => '0'],'productid = :id',[':id' => $productid]);
        return $this->redirect(['product/list']);
    }

}
