<?php
/*
 * 这是一个控制器的Demo
 */

class IndexController extends Controller {

    public function index () {
        $this->returnJson("Hello World!");
    }
}