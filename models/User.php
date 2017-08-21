<?php
namespace app\models;
use yii\db\ActiveRecord;
use Yii;
class User extends ActiveRecord {
    public $repass;
    public $loginname;
    public $rememberMe = true;
    public static function tableName() {
        return '{{%user}}';
    }

    public function attributelabels() {
        return [
                'username' => '用户名',
                'useremail' => '电子邮箱',
                'userpass' => '用户密码',
                'repass' => '确认密码',
                'loginname' => '登录名称',
        ];
    }
    public function rules() {
        return [
            ['username','required','message' => '用户名不能为空','on' => ['reg','regByMile']],
            ['username','unique','message' => '用户名已被注册','on' => ['reg','regByMile']],
            ['userpass','required','message' => '密码不能为空','on' => ['reg','regByMile','login']],
            ['useremail','required','message' => '电子邮箱不能为空','on' => ['reg','regByMile']],
            ['repass','required','message' => '确认密码不能为空','on' => ['reg']],
            ['repass','compare','compareAttribute' => 'userpass','message' => '密码不一致','on' => ['reg']],
            ['useremail','email','message' => '电子邮箱格式不正确','on' => ['reg','regByMile']],
            ['loginname','required','message' => '用户名不能为空','on' =>'login'],

        ];
    }
    //加入新用户
    public function reg($data,$scenarior='reg') {
        $this->scenario = $scenarior;
        if ($this->load($data) && $this->validate()) {
            $this->userpass = md5($this->userpass);
            $this->createtime = time();
            if ($this->save(false)) {
                return true;
            }
            return false;
        }
        return false;
    }

    //登录
    public function login($data) {
        $this->scenario = 'login';
        if ($this->load($data) && $this->validate()) {
            $lifetime = $this->rememberMe ? 24*3600 : 0;
            $session = Yii::$app->session;
            session_set_cookie_params($lifetime);
            $session['loginname'] = $this->loginname;
            $session['isLogin'] = 1;
            return (bool)$session['isLogin'];
        }
        return false;
    }
    //联表查询
    public function getProfile() {
        return $this->hasOne(Profile::classname(),['userid' => 'userid']);
    }
    //电子邮件创建会员
    public function regByMile($data) {
        $this->scenario = 'regByMile';
        $this->username = uniqid();
        $this->userpass = uniqid();
        if ($this->load($data) && $this->validate()) {
            $mailer = Yii::$app->mailer->compose('createuser',['username' =>$this->username,'userpass' => $this->userpass]);
            $mailer->setFrom('969001599@qq.com');
            $mailer->setTo($this->useremail);
            $mailer->setSubject('易货小铺-新建用户');
            if ($this->reg($data,'regByMile')) {
                if ($mailer->send()) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }
}
