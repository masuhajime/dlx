<?php

class Utils {
    static public function createNewViewerID()
    {
        $o = array('u','v','w','x','y','z','0','1','2','3','4','5','6','7','8','9',);
        $r = '';
        for($i=0; $i<40; $i++) {
            $r .= $o[array_rand($o)];
        }
        return $r;
    }
}