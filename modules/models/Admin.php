<?php
namespace app\modules\models;
use yii\db\ActiveRecord;
use Yii;
class Admin extends ActiveRecord {
    public $rememberMe = true;
    public $repass;
    public $oldpass;
    public static function tableName() {
        return '{{%admin}}';
    }

    public function attributelabels() {
        return [
            'adminuser' => '管理员账号',
            'adminemail' => '管理员邮箱',
            'adminpass' => '管理员密码',
            'repass' => '确认密码',
            'oldpass' => '旧密码'
        ];
    }

    public function rules() {
        return [
            ['adminuser', 'required', 'message' => '管理员账号不能为空','on' => ['login','seekpass','emailchangePass','adminadd','changeemail','changePass']],
            ['adminuser','unique','message' => '用户名已被注册','on'=>['adminadd']],
            ['adminpass', 'required', 'message' => '管理员密码不能为空','on' => ['login','emailchangePass','adminadd','changeemail','changePass']],
            ['rememberMe', 'boolean','on' => ['login']],
            ['adminpass', 'validatePass','on' => ['login','changeemail']],
            ['adminemail','required','message' => '电子邮箱不能为空','on' => ['seekpass','adminadd','changeemail']],
            ['adminemail','email','message' => '电子邮箱格式不正确','on' => ['seekpass','adminadd','changeemail']],
            ['adminemail','unique','message' => '电子邮箱已被注册','on'=>['adminadd','changeemail']],
            ['adminemail','validateEmail','on' => ['seekpass']],
            ['repass','required','message' => '确认密码不能为空' ,'on' => ['emailchangePass','adminadd','changePass']],
            ['repass','compare','compareAttribute' => 'adminpass','message' => '两次密码不一致','on' => ['emailchangePass','adminadd','changePass']],
            ['oldpass', 'validatePassupd','on' => ['changePass']],
            ['oldpass','required','message' => '旧密码不能为空','on' => ['changePass']],
        ];
    }
    //登录改邮箱时密码验证
    public function validatePass() {
        if(!$this->hasErrors()) {
            $data = self::find()->where('adminuser=:user and adminpass = :pass',[':user'=>$this->adminuser,':pass'=>md5($this->adminpass)])->one();
            if(is_null($data)) {
                $this->addError('adminpass','用户名或密码错误');
            }
        }
    }
    //用户更改密码 旧密码验证
    public function validatePassupd() {
        if(!$this->hasErrors()) {
            $data = self::find()->where('adminuser=:user and adminpass = :pass',[':user'=>$this->adminuser,':pass'=>md5($this->oldpass)])->one();
            //var_dump($data);die;
            if(is_null($data)) {
                $this->addError('oldpass','密码错误');
            }
        }
    }
    public function validateEmail() {
        if (!$this->hasErrors()) {
            $data = self::find()->where('adminuser=:user and adminemail = :email',[':user'=>$this->adminuser,':email'=>$this->adminemail])->one();
            if (is_null($data)) {
                $this->addError("adminemail",'管理员邮箱不匹配');
            }
        }
    }
    public function login($data) {
        $this->scenario = 'login';
        if($this->load($data)&&$this->validate()) {
            $lifetime = $this->rememberMe?24*3600:0;
            $cookies = Yii::$app->response->cookies;
            $cookies->add(new \yii\web\Cookie([
                "name"  => 'admin',
                'value' => [
                                'adminuser' => $this->adminuser,
                                'isLogin' => 1,
                            ],
                'expire' => time()+$lifetime,
            ]));
            //$cookies = Yii::$app->request->cookies;
            //$a = $cookies->getValue('admin',false);
            //var_dump($a);die;
            // $a = session_get_cookie_params();
            // var_dump($a);die;
            // $session['admin'] = [
            //     'adminuser' => $this->adminuser,
            //     'isLogin' => 1,
            // ];
            $this->updateAll(
                ['logintime' => time(),
                'loginip' => ip2long(Yii::$app->request->userIP)],
                'adminuser=:user',
                [':user'=>$this->adminuser]
            );
            return true;
        }
        return false;
    }
    //接收用户名跟邮箱后发送邮件
    public function seekPass($data) {
        $this->scenario = 'seekpass';
        //载入数据并验证数据
        if ($this->load($data) && $this->validate()) {
            //通过邮件验证做点有意义的事
            $time = time();
            $token = $this->createToken($data['Admin']['adminuser'],$time);
            $mailer = Yii::$app->mailer->compose('seekpass',['adminuser' => $data['Admin']['adminuser'],'time' => $time,'token' => $token]);
            $mailer->setFrom(['969001599@qq.com' => '黄鹏']);
            $mailer->setTo($data['Admin']['adminemail']);
            $mailer->setSubject('易货小铺-找回密码');
            if ($mailer->send()) {
                return true;
            }
        }
        return false;
    }
    //产生token
    public function createToken($adminuser,$time) {
        return md5(md5($adminuser).base64_encode(Yii::$app->request->userIP).md5($time));
    }

    //邮件更改密码
    public function emailchangePass($data) {
        $this->scenario = 'emailchangePass';
        if ($this->load($data) && $this->validate()) {
            return (bool)$this->updateAll(['adminpass' => md5($this->adminpass)],'adminuser = :user',[':user' => $this->adminuser]);
        }
        return false;
    }

    //更改密码
    public function changePass($data) {
        $this->scenario = 'changePass';
        if ($this->load($data) && $this->validate()) {
            return (bool)$this->updateAll(['adminpass' => md5($this->adminpass)],'adminuser = :user',[':user' => $this->adminuser]);
        }
        return false;
    }

    //注册管理员
    public function reg($data) {
        $this->scenario = 'adminadd';
        if ($this->load($data) && $this->validate()) {
            $this->adminpass = md5($this->adminpass);
            if ($this->save(false)) {
                return true;
            }
            return false;
        }
        return false;
    }

    //修改邮箱
    public function changeemail($data) {
        $this->scenario = "changeemail";
        if ($this->load($data) && $this->validate()) {
            return (bool)$this->updateAll(['adminemail' => $this->adminemail],'adminuser = :user',[':user' => $this->adminuser]);
        }
        return false;
    }
}
