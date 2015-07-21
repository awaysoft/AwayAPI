<?php
/**
 * 这是一个Model的例子
 */


class TestModel extends Model{
    public function getList() {
        return $this->query("select * from test");
    }
} 