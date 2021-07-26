<?php

namespace Luriuker;

//Base
use pocketmine\plugin\PluginBase as Luriuker;
use function array_diff;
use function scandir; 
//Essencial
use pocketmine\{Server, Player};
//Evento
use pocketmine\event\{Event, Listener};
use pocketmine\event\player\{PlayerInteractEvent, PlayerJoinEvent, PlayerItemHeldEvent, 
PlayerChatEvent, PlayerRespawnEvent, PlayerQuitEvent, PlayerDeathEvent, PlayerLoginEvent};
use pocketmine\network\mcpe\protocol\types\DeviceOS;
use pocketmine\network\mcpe\protocol\types\InputMode;
//level
use pocketmine\level\Level;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
//Player reconhecimento Tag
use pocketmine\network\mcpe\protocol\{LoginPacket, ProtocolInfo, DataPacket};
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\utils\UUID;
//Sons
use pocketmine\level\sound\{AnvilUseSound,
GhastShootSound};
//Entidades
use pocketmine\entity\Entity;
use pocketmine\event\entity\{EntityDamageByEntityEvent, EntityDamageEvent, 
EntitySpawnEvent, EntityRegainHealthEvent};

class Main extends Luriuker implements Listener{
   
   public $devicer = [];
   public $device;
   public $Device;
   
   public function onEnable(){
      $this->getServer()->getPluginManager()->registerEvents($this, $this);
      }
 //Always spawn
   public function onPlayerLogin(PlayerLoginEvent $event){
		$event->getPlayer()->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
	}
 //Join
   public function onJoin(PlayerJoinEvent $ev){
    $pl = $ev->getPlayer();
    $this->giveTag($pl);
    $name = $pl->getName();
    $ev->setJoinMessage("Seja Bem-vindo $name");
    $pl->getLevel()->addSound(new GhastShootSound(new Vector3($pl->getX(), $pl->getY(), $pl->getZ())));
  }
 //Respawn
  public function onRes(PlayerRespawnEvent $ev){
    $pl = $ev->getPlayer();
    $pl->teleport($this->getServer()->getDefaultLevel()->getSafeSpawn());
    $this->giveTag($pl);
  }
 //Quit
  public function onQuit(PlayerQuitEvent $ev){
    $pl = $ev->getPlayer();
    $this->giveTag($pl);
    $name = $pl->getName();
    $ev->setQuitMessage("Que pena :( o $name saiu");
  }
  //Item ID
  public function getItemId(PlayerItemHeldEvent $ev){
        $pl = $ev->getPlayer();
        if($pl->isSurvival() or $pl->isCreative() or $pl->isOp()){
	$pl->sendPopup("§cID§r§f: §f[§g".$ev->getItem()->getId()."§f:§r§g".$ev->getItem()->getDamage()."§f]\n\n");
	  }
       }
   //Check Plataforma 
   public function getDevice(DataPacketReceiveEvent $ev): void{
        $player = $ev->getPlayer();
	$packet = $ev->getPacket();
	if($packet instanceof LoginPacket){
	$this->getLogger()->debug("Criando sessão de jogadores");
	$login = $packet->clientData["DeviceOS"];
	$devicer = array("Uɴᴋɴᴏᴡɴ", "Aɴᴅʀᴏɪᴅ", "ɪOS", "ᴍᴀᴄOS", "FɪʀᴇOS", "GᴇᴀʀVR", "HᴏʟᴏLᴇɴꜱ", "Wɪɴᴅᴏᴡꜱ_10", "Wɪɴᴅᴏᴡꜱ", "Dᴇᴅɪᴄᴀᴛᴇᴅ", "Oʀʙɪꜱ", "Nx", "Pʟᴀʏꜱᴛᴀᴛɪᴏɴ_4", "Mᴀᴄ", "Wɪɴᴅᴏᴡꜱ_32 Eᴅᴜᴄᴀʟ_ᴠᴇʀꜱɪᴏɴ");
	$this->device[$packet->username] = ["OS" => $devicer[$login]];
		}
   //return true;
	} 
    
    //Tag Player
   public function giveTag($player) : void{
	$player->setNameTagVisible();
	$pp = $player->getPlayer();
	$os = $this->getDevice[$player->getName()]["OS"];
	$pp->setScoreTag("§fH§cP§7: §4".$pp->getHealth()."§f/§f".$pp->getMaxHealth()."§6Ping§7: §2".$pp->getPing()."\n $os");
	}

   public function onEntityRegainHealth(EntityRegainHealthEvent $event) : void {
        if($event->getEntity() instanceof Player) {
        $pl = $event->getEntity();
        $this->giveTag($pl); 
       }
    }
   
   public function onDamage(EntityDamageByEntityEvent $event) {
	if($event->getEntity() instanceof Player) {
        $pl = $event->getEntity();
        $this->giveTag($pl);
       } 
     }
   //Anti-fire
   public function onEntityDamage(EntityDamageEvent $event) {
    if($event->getEntity() instanceof Player) {
      if(in_array($event->getCause(),array(EntityDamageEvent::CAUSE_FIRE,EntityDamageEvent::CAUSE_FIRE_TICK,EntityDamageEvent::CAUSE_LAVA))) {
        $event->setCancelled();
      }
        $pl = $event->getEntity();
        $this->giveTag($pl);
    }
  }
 
 


}
