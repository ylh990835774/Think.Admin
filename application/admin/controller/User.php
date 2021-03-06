<?php

// +----------------------------------------------------------------------
// | Think.Admin
// +----------------------------------------------------------------------
// | 版权所有 2016~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://think.ctolog.com
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/Think.Admin
// +----------------------------------------------------------------------

namespace app\admin\controller;

use controller\BasicAdmin;
use library\Data;
use think\Db;

/**
 * 系统用户管理控制器
 * Class User
 * @package app\admin\controller
 * @author Anyon <zoujingli@qq.com>
 * @date 2017/02/15 18:12
 */
class User extends BasicAdmin {

    /**
     * 指定当前数据表
     * @var string
     */
    protected $table = 'SystemUser';

    public function index() {
        $this->title = '用户管理';
        $db = Db::name($this->table)->where('is_deleted', '0');
        parent::_list($db);
    }

    /**
     * 用户添加
     */
    public function add() {
        return $this->_form($this->table, 'form');
    }

    /**
     * 用户编辑
     */
    public function edit() {
        return $this->add();
    }

    /**
     * 用户密码修改
     */
    public function pass() {
        if (in_array('10000', explode(',', $this->request->post('id')))) {
            $this->error('系统超级账号禁止操作！');
        }
        if ($this->request->isGet()) {
            return $this->_form($this->table, 'pass');
        }
        $data = $this->request->post();
        if ($data['password'] !== $data['repassword']) {
            $this->error('两次输入的密码不一致！');
        }
        if (Data::save($this->table, ['id' => $data['id'], 'password' => md5($data['password'])], 'id')) {
            $this->success('密码修改成功，下次请使用新密码登录！', '');
        } else {
            $this->error('密码修改失败，请稍候再试！');
        }
    }

    /**
     * 表单数据默认处理
     * @param type $data
     */
    public function _form_filter(&$data) {
        if ($this->request->isPost()) {
            if (isset($data['id'])) {
                unset($data['username']);
            } elseif (Db::name($this->table)->where('username', $data['username'])->find()) {
                $this->error('用户账号已经存在，请使用其它账号！');
            }
        }
    }

    /**
     * 删除用户
     */
    public function del() {
        if (in_array('10000', explode(',', $this->request->post('id')))) {
            $this->error('系统超级账号禁止删除！');
        }
        if (Data::update($this->table)) {
            $this->success("用户删除成功！", '');
        } else {
            $this->error("用户删除失败，请稍候再试！");
        }
    }

    /**
     * 用户禁用
     */
    public function forbid() {
        if (in_array('10000', explode(',', $this->request->post('id')))) {
            $this->error('系统超级账号禁止操作！');
        }
        if (Data::update($this->table)) {
            $this->success("用户禁用成功！", '');
        } else {
            $this->error("用户禁用失败，请稍候再试！");
        }
    }

    /**
     * 用户禁用
     */
    public function resume() {
        if (Data::update($this->table)) {
            $this->success("用户启用成功！", '');
        } else {
            $this->error("用户启用失败，请稍候再试！");
        }
    }

}
