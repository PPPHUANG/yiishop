<?php
namespace app\models;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;

class Category extends ActiveRecord {
    public static function tableName() {
        return '{{%category}}';
    }
    public function attributelabels() {
        return [
            'title' => '分类名称',
            'parentid' => '父类名称',
        ];
    }

    public function rules() {
        return [
            ['parentid','required','message' => '上级分类不能为空'],
            ['title','required','message' => '标题不能为空'],
            ['createtime','safe'],
        ];
    }
    //添加分类
    public function add($data) {
        $data['Category']['createtime'] = time();
        if ($this->load($data) && $this->save()) {
            return true;
        }
        return false;
    }

    //获得cate数据
    public function getData() {
        $cates = self::find()->all();
        $cates = ArrayHelper::toArray($cates);//得到二维数组[0=>['a'=>'b']]
        return $cates;
    }
    //无线分类排序
    public function getTree($cates,$pid = 0) {
        $tree = [];
        foreach ($cates as $cate) {
            if ($cate['parentid'] == $pid) {
                $tree[] = $cate;
                $tree = array_merge($tree,$this->getTree($cates,$cate['cateid']));
            }
        }
        return $tree;
    }
    //添加前缀
    public function setPrefix($data,$p='|-----'){
        $tree = [];                                                     //存放数据
        $num = 1;                                                       //记录层级
        $prefix = [0 => 1];                                             //存放各个parentid 对应的层级
        while ($val = current($data)) {                                 //获得第一个值 然后往下循环
            $key = key($data);                                          //第一个值得下标
            if ($key > 0) {                                             //第二次循环
                if ($data[$key - 1]['parentid'] != $val['parentid']) {  //当上级id改变  即层级变深
                    $num ++;                                            //当前分类层级加一
                    }
                }
                if (array_key_exists($val['parentid'],$prefix)) {       //若当前父类的层级在prefix中存有时
                    $num = $prefix[$val['parentid']];                   //当前分类层级
                }
                $val['title'] = str_repeat($p,$num).$val['title'];      //追加前缀
                $prefix[$val['parentid']] = $num;                       //把上级id对应的层级记录在数组
                $tree[] = $val;
                next($data);                                            //下移指针
        }
        return $tree;
    }
    //下拉列表无线分类入口数据
    public function getOptions() {
        $data = $this->getData();                                       //获得数据
        $tree = $this->getTree($data);                                  //无限级分类排序
        $tree = $this->setPrefix($tree);                                //添加前缀
        $options = ['添加顶级分类'];                                     //顶级分类
        foreach ($tree as $cate) {
            $options[$cate['cateid']] = $cate['title'];                 //cateid对应title值
        }
        return $options;
    }
    //分类列表
    public function getTreeList() {
        $data = $this->getData();
        $tree = $this->getTree($data);
        return $tree = $this->setPrefix($tree);
    }

    public static function getMenu() {
        $top = self::find()->where('parentid = :pid',[':pid' => 0])->limit(11)->orderby('createtime asc')->asArray()->all();
        $data = [];
        foreach ($top as $k => $cate) {
            $cate['children'] =self::find()->where('parentid = :pid',[':pid' => $cate['cateid']])->limit(10)->asArray()->all();
            $data[$k] = $cate;
        }
        return $data;
    }
}
