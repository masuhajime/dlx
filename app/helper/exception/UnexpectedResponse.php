<?php

namespace app\helper\exception;

class UnexpectedResponse extends \RuntimeException{
    public function __construct($m, $code = 0, $prev = null) {
        parent::__construct($m, $code, $prev);
    }
}