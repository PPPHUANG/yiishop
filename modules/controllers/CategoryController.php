<?php
namespace app\modules\controllers;
use yii\web\Controller;
use app\models\Category;
use app\modules\controllers\CommonController;
use Yii;

class CategoryController extends CommonController {
    //分类列表
    public function actionList() {
        $this->layout = "layout1";
        $model = new Category;
        $cates = $model->getTreeList();
        return $this->render('cates',['cates' => $cates]);
    }
    //添加分类   无限级分类
    public function actionAdd() {
        $this->layout = "layout1";
        $model = new Category;
        $list =$model->getOptions();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->add($post)) {
                Yii::$app->session->setFlash('info','添加成功');
            }
        }
        return $this->render('add',['model' => $model,'list' => $list]);
    }
    //分类修改
    public function actionMod() {
        $this->layout = "layout1";
        $cateid = Yii::$app->request->get('cateid');
        $model = Category::find()->where('cateid = :id',[':id' =>$cateid])->one();
        $list = $model-> getOptions();
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($model->load($post) && $model->save()) {
                Yii::$app->session->setFlash('info','修改成功');
            }
        }
        return $this->render('mod',['model' => $model,'list' => $list]);
    }
    //分类删除
    public function actionDel() {
        try{
            $cateid = Yii::$app->request->get('cateid');
            if (empty($cateid)) {
                throw new \Exception("参数错误");
            }
            if(Category::find()->where('parentid = :id',[':id' =>$cateid])->one()) {
                throw new \Exception("该分类下有子类,不允许删除");
            }
            if (!Category::deleteAll('cateid = :id',[':id' => $cateid])) {
                throw new \Exception("删除失败");
            }
        } catch (\Exception $e) {
             Yii::$app->session->setFlash('info', $e->getMessage());
        }
        return $this->redirect(['category/list']);
    }
}
