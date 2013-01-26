<?php
// var bossData = {"bossID":"204","name":"\u30ac\u30fc\u30b4\u30a4\u30eb","img":"204","rare":"1","approachLv":"0","fieldType":"0","defaultHP":"0","exp":"0","type":"3","dropSummon1":"4000104","dropSummon2":"4005104","dropSummon3":"4010104","dropSummon4":"4015104","encountFlag":"1","id":"5497470","userID":"290104","bossType":"1","bossHP":"1000","bossDefeatCount":"0","bossCheckID":"811df62e9da6b4d685361fd9ad671f7db5727a5fec9a33de5a1b2fb4e842bab02c077331","continuityFlag":"1","message":[["\u30de\u30bf\u4eba\u9593\u30ab\u3002\n\u30aa\u524d\u30e9\u30cf\u81ea\u5206\u30bf\u30c1\u30ce\u90fd\u5408\u30c0\u30b1\u30f2\u8003\u30a8\n\u529b\u30f2\u8cb8\u30bb\u30c8\u30a4\u30a6\u30ce\u30ab\uff1f","\u307e\u305f\u30cb\u30f3\u30b2\u30f3\u2026\u30a2\u30ac\u30c3\uff01\u30c3\u304f\u3058\u304c\uff01\uff01\n\u306f\u3052\u3058\u307e\u3063\u305f\u30ac\u30b2\u30ae\u30c3\u30ae\u30b0\u30ac\u30ae\u30b4\u30b0"],["\u4f55\u6545\u30c0\uff1f\uff1f\n\u4eba\u9593\u30c8\u30cf\u3001\u4e0d\u601d\u8b70\u30ca\u751f\u30ad\u8005\u30c0\u30ca\u3002\n\u826f\u30a4\u30c0\u30ed\u30a6\uff01\u6211\u30ce\u529b\u30f2\u8cb8\u30bd\u30a6\u3002","\u30cb\u30f3\u30b2\u30f3\u2026\u30ac\u30c3\u30b4\u30c3\uff01\n\u826f\u3044\u30ae\u30c3\u30b0\u30c3\u30b4\uff01\u4ed8\u3044\u3066\u30b4\u30b2\u30ae\u30e3\u30b4\uff01"],["\u50b2\u6162\u30ca\u4eba\u9593\u30e1\u2026\u2026\n\u6c7a\u30b7\u30c6\u9032\u5316\u30f2\u30b7\u30ca\u30a4\u751f\u30ad\u8005\u30e8\u2026\u2026","\u3050\u304c\u3063\uff01\uff01\n\u304c\u304c\u304e\u3054\u3050\u304c\u3093\u3052\u3076\u3076\u30fc\u30fc\uff01\uff01"]]};
// "id":"5497470"
// ,"bossType":"1",
namespace app\model;

class FieldBoss {
    private $id;
    private $name = null;
    private $rarity = null;
    
    public function __construct($id) {
        $this->id = $id;
    }
    
    public static function createInstanceFromArray(array $data)
    {
        if (!isset($data['id'])) {
            Logger::warning('invalid data', __LINE__, __FILE__);
            throw new \RuntimeException('field boss invalid data array');
        }
        $fe = new self($data['id']);
        if (isset($data['name'])) {
            $fe->setName($data['name']);
        }
        if (isset($data['rare'])) {
            $fe->setRarity($data['rare']);
        }
        return $fe;
    }
    
    public function __toString()
    {
        return "boss id:{$this->id} name:{$this->name} rare:{$this->rarity}";
    }
    
    public function toString() {return $this.null;}
    
    public function process()
    {
        \app\helper\DlxAccesser::fieldBossProcess($this);
    }
    
    public function getId() {return $this->id;}
    public function setName($string) {$this->name = $string;}
    public function getName() {return $this->name;}
    public function setRarity($int) {$this->rarity = intval($int);}
    public function getRarity() {return $this->rarity;}
}
