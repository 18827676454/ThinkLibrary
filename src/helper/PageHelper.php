<?php

// +----------------------------------------------------------------------
// | Library for ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2019 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 官方网站: http://demo.thinkadmin.top
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | gitee 仓库地址 ：https://gitee.com/zoujingli/ThinkLibrary
// | github 仓库地址 ：https://github.com/zoujingli/ThinkLibrary
// +----------------------------------------------------------------------

namespace think\admin\helper;

use think\admin\Helper;
use think\db\Query;

/**
 * 列表处理管理器
 * Class PageHelper
 * @package think\admin\helper
 */
class PageHelper extends Helper
{
    /**
     * 是否启用分页
     * @var boolean
     */
    protected $page;

    /**
     * 集合分页记录数
     * @var integer
     */
    protected $total;

    /**
     * 集合每页记录数
     * @var integer
     */
    protected $limit;

    /**
     * 是否渲染模板
     * @var boolean
     */
    protected $display;

    /**
     * 逻辑器初始化
     * @param string|Query $dbQuery
     * @param boolean $page 是否启用分页
     * @param boolean $display 是否渲染模板
     * @param boolean $total 集合分页记录数
     * @param integer $limit 集合每页记录数
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function init($dbQuery, $page = true, $display = false, $total = false, $limit = 0)

    {
        $this->page = $page;
        $this->total = $total;
        $this->limit = $limit;
        $this->display = $display;
        $this->query = $this->buildQuery($dbQuery);
        // 数据列表排序处理
        if ($this->app->request->isPost()) {
            $post = $this->app->request->post();
            $sort = intval(isset($post['sort']) ? $post['sort'] : 0);
            unset($post['action'], $post['sort']);

        }
        // 未配置 order 规则时自动按 sort 字段排序
        if (!$this->query->getOptions('order') && method_exists($this->query, 'getTableFields')) {
            if (in_array('sort', $this->query->getTableFields())) $this->query->order('sort desc');
        }
        // 列表分页及结果集处理
        if ($this->page) {
            // 分页每页显示记录数
            $limit = intval($this->app->request->get('pageSize', cookie('page-limit')));
            if ($this->limit > 0){
                $limit = $this->limit;
            }else{
                $limit = 999999999;
            }
            list($options, $query) = [[], $this->app->request->get()];
            $paginate = $this->query->paginate(['list_rows' => $limit, 'query' => $query], $this->total);
            $result = ['page' => ['limit' => intval($limit), 'total' => intval($paginate->total()), 'pages' => intval($paginate->lastPage()), 'current' => intval($paginate->currentPage())], 'list' => $paginate->items()];

        } else {
            $result = ['list' => $this->query->select()->toArray()];
        }
        if (false !== $this->controller->callback('_page_filter', $result['list']) ) {
        }
        return $result;
    }

}
