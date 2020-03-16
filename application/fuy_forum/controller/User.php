<?php
namespace app\fuy_forum\controller;
use think\Controller;
class User extends Controller 
{
    public function showList()
    {
        $list = db('save')->order('sname desc')->select();
        return $list;
    }
    //渲染登陆方法
    public function login()
    {
    	return view("",["save"=>$this->showlist()]);
    }
    //1703010210-处理登陆的方法
    public function doLogin()
    {
        // return view();
        //获取表单的用户名和密码
        $uName = input('userName');
        $uPa = md5(input('userPa'));
        // 组织链式操作，执行登陆验证查询
        $re = db('user')
            ->where('unick',$uName)
            ->where('upa',$uPa)
            ->find();
        // 利用if语句，判断查询结果，组织跳转
            if ($re == null) {
                $this->error("登录失败",url('fuy_forum/user/login'));
            }
            else{
                session('name',$uName);
                session('look',$re["uimg"]);
                $this->success("登录成功".session('name'),url('fuy_forum/Index/index'));
            }
       
    }
    // 联系我们方法
    public function contact()
    {
    	return view("",["save"=>$this->showlist()]);
    }
    // 注册方法
    public function register()
    {
    	return view("",["save"=>$this->showlist()]);
    }
    // 处理注册的方法
    public function doRegister(){
        
        // return view();
        // $this->success("注册成功!",url('User/login'));
        config("database.username","change");
        config("database.password","666666");
        $data = [
            'unick'=>input('reName'),
            'upa'=>md5(input('rePa')),
            'utel'=>input('reTel'),
            'uemail'=>input('reEmail'),
            //  插入数据user.png===>头像img已经有了路劲
            'uimg'=>'user.png'
        ];
        $re = db('user')
        ->where('unick',input('reName'))
        ->find();
        if ($re == null) {
            $re=db('user')->insert($data);
            $this->success('注册成功'.input('reName'),url('fuy_forum/user/login'));
        }
        else{
            // session('name','unick')
            $this->error("注册失败".input('reName')."已经被注册过了");
        }
    }
    // 展示用户信息的方法，助理函数
    public function showPara(){
        $db = config("database");
        $app =  config("app_debug");
        // var_dump($db);
        // echo $app;
        return view("User/secret",["user"=> $db["username"],"passwd"=>$db["password"]]);
    }
    
}
