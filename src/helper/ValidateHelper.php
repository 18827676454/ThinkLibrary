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

/**
 * 快捷输入验证器
 * Class ValidateHelper
 * @package think\admin\helper
 */
class ValidateHelper extends Helper
{
    /**
     * 快捷输入并验证（ 支持 规则 # 别名 ）
     * @param array $rules 验证规则（ 验证信息数组 ）
     * @param string $type 输入方式 ( post. 或 get. )
     * @return array
     */
    public function init(array $rules, $type = '')
    {
        list($data, $rule, $info) = [[], [], []];
        foreach ($rules as $name => $message) {
            if (stripos($name, '#') !== false) {
                list($name, $alias) = explode('#', $name);
            }
            if (stripos($name, '.') === false) {
                $data[$name] = empty($alias) ? $name : $alias;
            } else {
                list($_rgx) = explode(':', $name);
                list($_key, $_rule) = explode('.', $name);
                $info[$_rgx] = $message;
                $data[$_key] = empty($alias) ? $_key : $alias;
                $rule[$_key] = empty($rule[$_key]) ? $_rule : "{$rule[$_key]}|{$_rule}";
            }
        }
        foreach ($data as $key => $name) $data[$key] = input("{$type}{$name}");
        if ($this->app->validate->rule($rule)->message($info)->check($data)) {
            return $data;
        } else {
            $this->controller->error(1,$this->app->validate->getError());
        }
    }

}