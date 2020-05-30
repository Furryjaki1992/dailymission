<?php

namespace lovetwice1012\daily;

use pocketmine\event\Listener;
use pocketmine\event\PlayerJoinEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\block\{Wood, Wood2, DiamondOre, Diamond, Iron, IronOre, Gold, GoldOre, Emerald, EmeraldOre, Stone, Cobblestone, Redstone, RedstoneOre, Coal, CoalOre, Lapis, LapisOre};
use pocketmine\entity\{Animal, Monster};

class EventListener implements Listener
{
    public $data;
    public $plugin;
    public $money;
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        
        $this->load();
        $this->plugin = $this;
        
        
    }
    
    public function load()
    {
        date_default_timezone_set('Asia/Tokyo');
        $this->data = new Config($this->getDataFolder() . "Data.yml", Config::YAML);
        $this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
    }
    public function isToday()
    {
        if ($this->data->exists("date") && $this->data->get("date") == date("Y/m/d")) {
            return true;
        } else {
            $this->data->set("date", date("Y/m/d"));
            $Bdata = $this->config->getAll();
            foreach ($Bdata as $key => $value) {
                if ($key != "date" && $key != "nowmission") {
                    
                    
                    $this->data->set($key, 0);
                    
                }
                
            }
            $rand  = rand(1, 4);
            $rand2 = rand(1, 64);
            
            
            $Ajob = "";
            $this->data->set($key, 0);
            switch ($rand) {
                case 1:
                    $Ajob = "wood";
                case 2:
                    $Ajob = "mine";
                case 3:
                    $Ajob = "build";
                
                case 4:
                    $Ajob = "kill";
                    
                    
                    
                    
            }
            
            $this->data->set("nowmission", $Ajob);
            $this->data->set("checkpoint", $rand2);
            
            
            
            return true;
        }
    }
    public function isNowmission($type)
    {
        if ($this->data->get("nowmission") == $type) {
            return true;
        } else {
            return false;
        }
    }
    public function checkProgress($player)
    {
        if ($this->data->get("ckeckpoint") <= $this->data->get($player->getName())) {
            return true;
        } else {
            return false;
        }
        
    }
    public function addProgress($player, $type)
    {
        isToday();
        if (isNowmission()) {
            if ($this->data->exists($player->getName())) {
                $this->data->set($player->getName(), $this->data->get($player->getName()) + 1);
                
            } else {
                $this->data->set($player->getName(), 1);
                
                
            }
            $this->data->save();
            
            if (checkProgress($player)) {
                //ここにデイリーミッションクリアしたことを通知するコード入れてね
                $player->addTitle("デイリーミッションクリア！25万円プレゼント！", "明日もがんばってね！");
                $this->money->addMoney($player, "250000);
            }
            
            
        }else{
        $player->sendTip("今日のデイリーミッションは".$this->data->get("nowmission")."を".$this->data->get("ckeckpoint")."回することです。");
        }
        
    }
    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $block  = $event->getBlock();
        if ($block instanceof Wood or $block instanceof Wood2) {
            
                $this->plugin->addProgress($player, "wood");
                
            
        } elseif ($block instanceof Diamond or $block instanceof DiamondOre or $block instanceof Iron or $block instanceof IronOre or $block instanceof Gold or $block instanceof GoldOre or $block instanceof Emerald or $block instanceof EmeraldOre or $block instanceof Stone or $block instanceof Cobblestone or $block instanceof Redstone or $block instanceof RedstoneOre or $block instanceof Coal or $block instanceof CoalOre or $block instanceof Lapis or $block instanceof LapisOre) {
            
                $this->plugin->addProgress($player, "mine");
                
            
        }
    }
    
    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
       
            $this->plugin->addProgress($player, "build");
            
        
    }
    public function onKill(PlayerDeathEvent $event)
    {
        $entity = $event->getEntity();
        $cause  = $entity->getLastDamageCause();
        if ($cause instanceof EntityDamageByEntityEvent) {
            $player = $cause->getDamager();
            if ($player instanceof Player) {
                if ($entity instanceof Player) {
                    $this->plugin->addProgress($player, "kill");
                    
                }
            }
        }
    }
    
}
