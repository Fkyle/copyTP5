<?php

namespace app\fuy_forum\controller;

use think\Controller;

class Index extends Controller
{
    public  function showList()
    {
        $list = db('save')->order('sname desc')->select();
        return $list;
    }
    // 验证
    public function check()
    {
        // $this->success('验证成功','index/index');
        if (!session('?name')) {
            $this->error('请先登陆', 'index/index');
        }
    }
    // 首页
    public function index()
    {
        // 1703010210-查询板块 
        $re = db("save")->order("sname desc")->select();
        $re_carousel = db('carousel')->where('cpause','')->select();
        // dump($re_carousel);
        $re_mes_top = db('mes')
            ->order('mcreateat desc')
            ->limit(10)
            ->select();
        return view("", ["save" => $this->showlist(), 'topMes' => $re_mes_top, 're' => $re, 'reCarousel' => $re_carousel]);
    }
    // 列表详细页面
    public function detail($mid = 0)
    {
        if ($mid == 0) {
            $this->error('URL地址错误');
        } else {
            $re_err = db('mes')->where('mid', $mid)->find();
            if ($re_err) {
                // 帖子的查询。查询mid为1的帖子
                $re_mes = db()
                    ->view('mes', 'mcontent,mtitle,unick,mcreateat')
                    ->view('user', 'uimg', 'mes.unick=user.unick', 'left')
                    ->view('save', 'sid,sname', 'mes.sid=save.sid')
                    ->where('mid', $mid)
                    ->find();
            } else {
                $this->error('URL地址错误');
            }
        }
        // dump($re_mes);
        // 查询mid为1的帖子的所有回复
        $re_res = db()
            ->view('res', 'rcontent,rcreateat,unick,mid')
            ->view('user', 'uimg', 'res.unick=user.unick')
            ->where('mid', $mid)
            ->paginate(3);
        // dump($re_res);
        $page = $re_res->render();
        return view("", ["save" => $this->showlist(), "reMes" => $re_mes, 'reRes' => $re_res, 'pages' => $page]);
    }
    // 回复处理页面
    public function doRes()
    {
        //1验证登陆状态
        $this->check();
        // 1703010210-回复数据准备数据-----修改sql
        // 2动态配置参数
        config("database.username", "change");
        config("database.password", "666666");
        // 3获取帖子信息，固定帖子1
        $data = [
            "rcontent" => input('resContent'),
            "unick" => session('name'),
            "rcreateat" => time(),
            'mid' => input('mid')
        ];
        // 4写入回复
        // 执行回复查询操作
        $re = db('res')->where('mid', input('mid'))->insert($data);
        // 5判断病提示跳转
        if ($re == 1) {
            $this->success('回复成功', url("index/detail", ['mid' => input('mid')]));
        } else {
            $this->error('回复失败');
        };
        return view();
    }
    // 更新头像
    public function me()
    {
        $this->check();
        return view("", ["save" => $this->showlist()]);
    }
    // 1703010210执行更新头像
    public function upMe()
    {
        $this->check();
        // 动态配置change账号
        config("database.username", "change");
        config("database.password", "666666");
        // 保存当前用户头像信息
        $re = db('user')->where('unick', session('name'))->find();
        $nowImg = $re['uimg'];
        // 提取文件对象
        $file = request()->file('uImg');
        // 保存|移动到指定目录===>public/static/img/portrait
        $info = $file->validate(['size' => 102400, 'ext' => 'jpg,png,gif'])->move(ROOT_PATH . 'public/static/img/portrait/');
        // 判断上传是否成功
        if ($info) {
            // 上传成功==getSaveName==>保存时间+加密的图片&&&&getFilename只是加密图片
            $cImg = $info->getSaveName();
            echo ($cImg);
            // 更新当前用户的字段uimg
            // 更新某个字段的信息setField
            $re = db('user')->where('unick', session('name'))->setField("uimg", $cImg);
            // $savePath = $info->getSaveName();
            // 判断更新结果
            if ($re == 1) {
                if ($nowImg != "user.png") {
                    unset($info);
                    unlink(ROOT_PATH . 'public/static/img/portrait/' . $nowImg);
                }
                // 更新头像后，更改登陆时的session值
                session('look', $cImg);
                $this->success('更新成功');
            } else {
                $this->error('更新失败', 'index/me');
            }
        } else {
            echo $file->getError();
        }
        return view();
    }
    // 发表
    public function post($sid = 3)
    {
        $this->check();
        return view("", ["save" => $this->showlist()]);
    }
    // 处理发表
    public function doPost($sid = 3)
    {
        $this->check();
        config("database.username", "change");
        config("database.password", "666666");
        // 1703010210-执行发帖处理
        // data建对应的是字段名字，方便后来插入处理
        $data = [
            'mtitle' => input('title'),
            'mcontent' => input('content'),
            'unick' => session('name'),
            'mcreateat' => time(),
            'sid' => $sid
        ];
        // 写入帖子
        $re = db('mes')->insert($data);
        // 成功失败处理
        if ($re == 1) {
            $this->success('发帖成功', url('fuy_forum/index/view', ['sid' => $sid]));
        } else {
            $this->error('发帖失败', 'fuy_forum/index/post');
        };
        return view();
    }
    // 列表详细页
    public function view($sid = 0)
    {
        $thisSec = null;
        // 查询板块===>下拉列表
        $list = db("save")->order("sname desc")->select();
        if ($sid == 0) {
            //当sid为0时展示所有帖子，并把标题设为全部
            $re_mes = db()
                ->view('mes', 'mcontent,mtitle,unick,mcreateat,sid,mid')
                ->view('user', 'uimg', 'mes.unick=user.unick')
                ->view('save', 'sname', 'mes.sid=save.sid')
                ->paginate(3);
            $thisSec = '全部';
            // dump($re_mes);
        } else {
            // 如果sid不存在与数据库，则跳转回上一页
            $re_err = db('save')->where('sid', $sid)->find();
            // dump($re_err);
            if ($re_err) {
                $re_mes = db()
                    ->view('mes', 'mcontent,mtitle,unick,mcreateat,sid,mid')
                    ->view('user', 'uimg', 'mes.unick=user.unick')
                    ->view('save', 'sname', 'mes.sid=save.sid')
                    ->where('sid', $sid)
                    ->paginate(3);
                foreach ($re_mes as $key => $value) {
                    if ($value['sid'] == $sid) {
                        $thisSec = $value['sname'];
                        break;
                    }
                }
            } else {
                $this->error('URL地址错误');
            }
        }
        // 1703010210-傅月--指定查询原贴信息
        $re = db("mes")->where("sid", 1)->order("mcreateat desc , mid")->select();
        $page = $re_mes->render();
        return view("", ["mes" => $re, "save" => $list, 'reList' => $re_mes, 'pages' => $page, 'thisSec2' => $thisSec]);
    }
    public function jump()
    {
        // user/showPara =>user控制器下的跳转
        $this->error("即将跳转", "User/showPara");
    }
    // 1703010210注销
    public function logout()
    {
        $this->check();
        session('name', null);
        session('look', null);
        $this->success('注销成功', 'fuy_forum/index/index');
    }
    public function changepa()
    {
        return view("", ['save' => $this->showList()]);
    }
    public function doChangePa()
    {
        $this->check();
        config("database.username", "change");
        config("database.password", "666666");
        $oldpa = md5(input('oldpa'));
        $newpa = md5(input('newpa'));
        $name = session('name');
        $re = db('user')
            ->where('upa', $oldpa)
            ->where('unick', $name)
            ->setField('upa', $newpa);
        if ($re == 1) {
            $this->success('密码修改成功', url('index/logout'));
        } else {
            $this->error('密码修改失败', url('index/changepa'));
        }
    }
}
