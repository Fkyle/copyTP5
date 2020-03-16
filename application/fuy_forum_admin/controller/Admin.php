<?php
namespace app\fuy_forum_admin\controller;
use think\Controller;
class Admin extends Controller
{
  public function acheck()
  {
    // $this->success('验证成功','index/index');
    if (!session('?aname')) {
      $this->error('请先登陆', 'admin/alogin');
    }
  }
  public function aLogin()
  {
    return view();
  }

  public function aDoLogin()
  {
    $auser = input('aUser');
    $apasswd = input('aPasswd');
    $re = db('admin')
        ->where('admin',$auser)
        ->where('apa',$apasswd)
        ->find();
      if($re == null){
        $this->error('登录失败',url('admin/alogin'));
      }
      else{
        session('aname', $auser);
        session('apa',$apasswd);
        $this->success('登录成功'.session('aname'),url('deal/mesdeal'));
      }
    return view();
  }
  public function aPa(){
    return view();
  }
  public function aDoPa(){
    $apasswd = input('apasswd');
    $anpasswd = input('anpasswd');
    $aname = session('aname');
    $re = db('admin')
          ->where('apa',$apasswd)
          ->where('admin',$aname)
          ->setField('apa',$anpasswd);
      if($re == 1){
        $this->success('密码修改成功', url('admin/aDoOut'));
      }
      else{

        $this->error('密码修改失败', url('admin/apa'));
      }
    return view();
  }
  public function aDoOut(){
    $this->acheck();
    session('admin',null);
    $this->success("管理员注销成功", "admin/alogin");
  }
}
?>