<?php

namespace app\fuy_forum_admin\controller;

use think\Controller;

class Deal extends Controller
{
  public function mesdeal($sid = 1)
  {
    $Sname = '暂未定义';
    $re = db('save')
      ->select();
    if ($sid == 0) {
      $search = input('search');
      $mes = db('mes')
        ->where('mtitle', 'like', '%' . $search . '%')
        ->paginate(3);
      $page1 = $mes->render();
    } else {
      $mes = db('mes')
        ->view('mes', 'mtitle,mid,mcontent,unick,mcreateat,sid,mid')
        ->view('save', 'sname', 'mes.sid = save.sid')
        ->where('sid', $sid)
        ->paginate(3);
      $page1 = $mes->render();
      foreach ($mes as $key => $value) {
        if ($value['sid'] == $sid) {
          $Sname = $value['sname'];
          break;
        }
      }
    }
    return view("", ['re' => $re, 'mes' => $mes, 'page' => $page1, 'sname' => $Sname]);
  }
  public function adetail($mid = 0)
  {
    if ($mid == 0) {
      $this->error("URL地址错误");
    } else {
      $re_err = db('mes')->where('mid', $mid)->find();
      if ($re_err) {
        $re_mes = db()
          ->view('mes', 'mtitle,mcontent,unick,mcreateat,sid,mid')
          // ->view('user', 'mes.unick=user.unick')
          ->view('save', 'sname', 'mes.sid=save.sid')
          ->where('mid', $mid)
          ->select();
      } else {
        $this->error('URL地址错误');
      }
    }
    // dump($re_mes);
    $re_res = db()
      ->view('res', 'rcontent,rcreateat,unick,mid,rid')
      // ->view('user','res.unick=user.unick')
      ->where('mid', $mid)
      ->paginate(3);
    $page = $re_res->render();
    // dump($re_res);
    return view("", ["reMes" => $re_mes, "reRes" => $re_res, 'page' => $page]);
  }
  public function mesdel()
  {
    $delMid = input('mid');
    $del = db('mes')
      ->where('mid', $delMid)
      ->delete();
    if ($del) {
      $this->success('帖子删除成功', url("deal/mesdeal"));
    } else {
      $this->error('帖子删除失败', url("deal/mesdeal"));
    };
  }
  public function resdel()
  {
    $delRes = input('rid');
    $del = db('res')
      ->where('rid', $delRes)
      ->delete();
    if ($del) {
      $this->success('回复删除成功');
    } else {
      $this->error('回复删除失败');
    }
  }
  public function secdeal()
  {
   
    $sid = input('sid');
    if($sid==0){
      $search = input('search');
      $save = db('save')
        ->where('sname', 'like', '%' . $search . '%')
        ->select();
    }
    else{
       $save = db('save')->select();
    }
    return view("", ['save'=>$save]);
  }
  public function secEdit()
  {
    $sid = input('sid');
    $getSave = db('save')
      ->where('sid', $sid)
      ->find();
    return view("", ['getSave' => $getSave]);
  }
  public function doSecEdit()
  {
    $sid = input('sid');
    $data = [
      'sname' => input('newSname'),
      'sremark' => input('newSremark')
    ];
    $re = db('save')
      ->where('sid', $sid)
      ->setField($data);
    if ($re) {
      $this->success('修改信息成功', url('deal/secdeal'));
    } else {
      $this->error('修改信息失败', url('deal/secEdit'));
    }
    return view("");
  }
  public function secDelete()
  {
    $sid = input('sid');
    // 删除板块
    $delSave = db('save')
      ->where('sid', $sid)
      ->delete();
    if($delSave){
      $this->success('删除板块成功', url('deal/secdeal'));
    }
    else{
      $this->error('删除板块失败', url('deal/secdeal'));
    }
  }
  public function addBox(){
    return view();
  }
  public function doAddBox(){
    $data=[
      'sname'=>input('sname'),
      'sremark'=>input('sremark')
    ];
    $re = db('save')
      ->where('sname','<>',input('sname'))
      ->where('sremark','<>',input('sremark'))
      ->insert($data);
    if ($re) {
      $this->success('添加板块成功', url('deal/secdeal'));
    } else {
      $this->error('添加板块失败', url('deal/secdeal'));
    }
  }
  public function userDeal(){
    $uid = input('uid');
    if ($uid == 0) {
      $search = input('search');
      $user = db('user')
        ->where('unick', 'like', '%' . $search . '%')
        ->select();
    } else {
      $user = db('user')->select();
    }
    return view("", ['user' => $user]);
  }
  public function userEdit(){
    return view();
  }
}
