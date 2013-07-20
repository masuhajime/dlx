<?php

namespace app\model;

class HeaderStatus {
    /* {
"remainTime":0,
"isBattle":0,
"bbsCount":0,
"bbsBlink":0,
"useCharm":true,
"charmEndTime":"2013-04-18 22:37:03",
"nowtime":1366292003,
"endtime":0
} */
    private $remainTime = 0;
    private $isBattle = false;
    
    public function __construct(Array $data_array) {
        $required_params = array("remainTime", "isBattle");
        foreach ($required_params as $p) {
            if (!isset($data_array[$p])) throw new \RuntimeException("param required: {$p}");
            $this->{$p} = $data_array[$p];
        }
    }
    
    public function getRemainTime(){return $this->remainTime;}
    public function isBattle(){return $this->isBattle;}
}
