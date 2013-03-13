<?php

namespace app\model;

class ViewerIdList {
    
    private $fp = null;
    
    public function __construct($file_path) {
        $this->fileOpen($file_path);
    }

    private function fileOpen($file_path)
    {
        if (!is_null($this->fp)) {
            return;
        }
        $this->fp = fopen($file_path, 'r');
        if (false === $this->fp) {
            throw new \Exception('fopen failed:'.$file_path);
        }
    }

    public function getPointerNow()
    {
        return ftell($this->fp);
    }
    
    public function getNext()
    {
        $n = fgets($this->fp);
        return trim($n);
    }
    
    public function seekPointerTo($pointer)
    {
        fseek($this->fp, $pointer);
    }
}
