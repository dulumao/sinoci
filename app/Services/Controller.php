<?php

namespace App\Services;

class Controller
{

    public function _remap($func, array $args)
    {
        // 排除不存在的方法
        method_exists(app(), $func) OR show_404();

        // 获取程序执行结果
        $output = call_user_func_array([app(), $func], $args);

        // 返回请求结果
        return app()->output->set_output($output);
    }

    public function __get($name)
    {
        // 修复 cache 类库
        if ($name === 'cache') {
            app()->load->driver('cache', ['adapter' => 'redis', 'backup' => 'file']);
            return app()->cache;
        }

        // 加载 CI 类库
        if (in_array($name, ['agent', 'cart', 'email', 'encryption', 'form_validation', 'image_lib', 'session', 'unit', 'upload'])) {
            app()->load->library(array_get(['agent' => 'user_agent', 'unit' => 'unit_test'], $name, $name));
            return app()->$name;
        }

        // 加载系统类库
        return load_class($name === 'load' ? 'Loader' : is_loaded()[$name], 'core');
    }

}
