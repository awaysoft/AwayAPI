<?php
/**
 * Created by PhpStorm.
 * User: Tom
 * Date: 15-7-21
 * Time: 下午3:54
 */

class TestPlugin extends Plugin {
    protected $interfaces = [
        'appControllerLoad' => 'appControllerLoad'
    ];

    public function appControllerLoad() {
        $TestModel = new TestModel();
        $list = $TestModel->getList();
        json_return($list);
        test();
    }
}