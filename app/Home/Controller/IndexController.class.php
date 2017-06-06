<?php

// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015-2016 http://www.rainfer.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: rainfer <81818832@qq.com>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Think\Verify;
use Home\Controller\HomebaseController;

class IndexController extends HomebaseController {

    // 选择模块
    public function index() {
        $this->display();
    }

}
