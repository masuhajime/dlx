<?php

namespace app\helper;
//use app\helper\Logger;

class Logger {
    
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    //const LEVEL_NOTICE = 'notice';//正常に起こりうる状態だが出過ぎると疑ったほうがいい
    const LEVEL_WARNING = 'warning';//予期していない動作だが動く状態
    const LEVEL_ALERT = 'alert';
    
    private static $log_level = array(
        self::LEVEL_DEBUG => true,
        self::LEVEL_INFO => true,
        self::LEVEL_WARNING => true,
        self::LEVEL_ALERT => true,
    );
    
    private static $header = '';

    public static function setLogLevel($level)
    {
        self::$log_level = array(
            self::LEVEL_DEBUG => false,
            self::LEVEL_INFO => false,
            self::LEVEL_WARNING => false,
            self::LEVEL_ALERT => false,);
        switch ($level) {
            case self::LEVEL_DEBUG: self::$log_level[self::LEVEL_DEBUG] = true;
            case self::LEVEL_INFO : self::$log_level[self::LEVEL_INFO]  = true;
            case self::LEVEL_WARNING : self::$log_level[self::LEVEL_WARNING]  = true;
            case self::LEVEL_ALERT: self::$log_level[self::LEVEL_ALERT] = true;
        }
    }
    
    private function __construct(){}
    
    public static function debug($message, $line = null, $file = null)
    {
        self::_echo($message, self::LEVEL_DEBUG, $line, $file);
    }
    
    public static function info($message, $line = null, $file = null)
    {
        self::_echo($message, self::LEVEL_INFO, $line, $file);
    }
    
    public static function warning($message, $line = null, $file = null)
    {
        self::_echo($message, self::LEVEL_WARNING, $line, $file);
    }
    
    public static function alert($message, $line = null, $file = null)
    {
        self::_echo($message, self::LEVEL_ALERT, $line, $file);
    }
    
    private static function _echo($message, $level, $line, $file)
    {
        if (false === self::$log_level[$level]) {
            return;
        }
        if (!is_string($message)) {
            $message = var_export($message);
        }
        $header = self::makeMessageHeader(time(), $level, $line, $file);
        echo $header.$message.PHP_EOL;
    }

    public static function setHeader($header)
    {
        self::$header = $header;
    }
    
    private static function makeMessageHeader($unixtimestamp, $level, $line, $file)
    {
        $date = date('Y-m-d H:i:s', $unixtimestamp);
        $level = sprintf('%7s', $level);
        $header = (0 < strlen(self::$header)) ? ' '.self::$header : '';
        $line = is_null($line) ? '' : ':'.$line.'';
        $file = is_null($file) ? '' : ' '.basename($file);
        
        return "[{$date}] [{$level}]{$header}{$file}{$line} ";
    }
}
