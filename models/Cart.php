<?php
namespace app\models;
use yii\db\ActiveRecord;
class Cart extends ActiveRecord {
    public static function tableName() {
        return "{{%cart}}";
    }
    public function rules() {
        return [
            [['productid','productnum','userid','price'],'required'],
            ['createtime','safe'],
        ];
    }
}
