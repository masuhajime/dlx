<?php

namespace app\model;

class FileLineList {
    
    private $fp = null;
    private $pointer_file = null;
    
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
    
    public function getPointerFromFile($pointer_file)
    {
        $this->pointer_file = $pointer_file;
        if (file_exists($pointer_file)) {
            $pointer = intval(file_get_contents($pointer_file));
        } else {
            $pointer = 0;
        }
        $this->seekPointerTo($pointer);
    }
    
    public function savePointerToFile()
    {
        if (is_null($this->pointer_file)) {
            throw new \RuntimeException("pointer save file not found.");
        }
        file_put_contents($this->pointer_file, $this->getPointerNow());
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
