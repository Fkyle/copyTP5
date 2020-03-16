<?php
namespace app\fuy_forum_admin\controller;
use think\Controller;
class Adv extends Controller
{
  public function boxDeal(){
    $re = db('carousel')->select();
    $color = db('carousel')
            ->where('cid',session('colorCid'))
            ->where('cpause',1)
            ->find();
    // 数组转字符串
    // $getColor= implode(',', $color);
    return view("",['re'=>$re]);
  }
  public function upBox(){
    // 动态配置change账号
    config("database.username", "change");
    config("database.password", "666666");
    $catitle = input('catitle');
    $caback = input('caback');
    $camid = input('camid');
    // 提取文件对象
    $file = request()->file('cImg');
    // 保存|移动到指定目录===>public/static/img/portrait
    $info = $file->validate(['size' => 10240000, 'ext' => 'jpg,png,gif'])->move(ROOT_PATH . 'public/static/img/adv/');
    // 判断上传是否成功
    if ($info) {
      // 上传成功==getSaveName==>保存时间+加密的图片&&&&getFilename只是加密图片
      $createImg = $info->getSaveName();
      echo ($createImg);
      $data=[
        'cimg'=> $createImg,
        'ctitle'=>$catitle,
        'cback'=>$caback,
        'mid'=>$camid
        ];
      // 更新当前用户的字段uimg
      // 更新某个字段的信息setField
      $re = db('carousel')->insert($data);
      // $savePath = $info->getSaveName();
      // 判断更新结果
      if ($re == 1) {
        // 更新头像后，更改登陆时的session值
        session('clook',$createImg);
        $this->success('更新成功');
      } else {
        $this->error('更新失败', 'adv/boxDeal');
      }
    } else {
      echo $file->getError();
    }
    return view();
  }
  public function carouselDel(){
    $delCarousel = input('cid');
    $del = db('carousel')
      ->where('cid',$delCarousel)
      ->delete();
    if($del){
      $this->success('轮播图删除成功');
    }
    else{
      $this->error('轮播图删除成功');
    }
  }
  public function carouselPause(){
    $pauseCarousel = input('cid');
    session('colorCid',$pauseCarousel);
    $pause = db('carousel')
          ->where('cid', $pauseCarousel)
          ->where('cpause','')
          ->find();
    $data=[
      'cpause'=>1,
      'color'=>1
    ];
    if($pause){
      $pauseImg = db('carousel')
          ->where('cid', $pauseCarousel)
          ->setField($data);
      $this->success('暂停成功'+$pauseImg);
    }
    else{
      echo($pause+'失败');
    }
    
  }
  public function carouselStart(){
    $startCarousel = input('cid');
    $start = db('carousel')
      ->where('cid', $startCarousel)
      ->where('cpause', 1)
      ->find();
    $data = [
      'cpause' => '',
      'color' => ''
    ];
    if($start){
      $stratImg = db('carousel')
          ->where('cid', $startCarousel)
          ->setField($data);
      $this->success('审核通过');
    }
  }
  public function editImg(){
    $re = db('carousel')->where('cid',input('cid'))->find();
    return view("",['re'=>$re]);
  }
  public function doEditImg(){
    $getCid = input('cid');
    $data = [
      'ctitle' => input("retitle"),
      'cback' => input("reback"),
      'mid' => input("remid")
    ];
    $re = db('carousel')->where('cid', $getCid)->setField($data);
    if($re ==1){
      $this->success('修改信息成功'+ $getCid,url('adv/boxdeal'));
    }
    else{
      $this->error('修改信息失败');
    }
    return view();
  }
}
