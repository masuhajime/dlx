<?php

namespace app\model;

class OwnerPlayer extends Player{
    private $stamina;
    private $milk_num;


    private static $instance = null;

    public function __construct() {
        parent::__construct();
    }
    
    /**
     * @return \app\model\OwnerPlayer
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getStamina()
    {
        $this->stamina = \app\helper\DlxAccesser::getStamina();
        return $this->stamina;
    }
    
    public function setMilkNum($num)
    {
        $this->milk_num = $num;
    }
    
    public function useMilk()
    {
        \app\helper\DlxAccesser::useMilk();
    }
}
