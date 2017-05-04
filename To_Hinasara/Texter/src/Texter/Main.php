<?php

/*
 * Texter, the display FloatingTextPerticle plugin for PocketMine-MP
 * Copyright (C) 2017 fuyutsuki <https://twitter.com/y_fyi>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Texter;

# Base
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

# Server
use pocketmine\Server;

# Level
use pocketmine\level\Level;

#Entity
use pocketmine\entity\Entity;
use pocketmine\entity\Item as ItemEntity;

# Player
use pocketmine\Player;

#Item
use pocketmine\item\Item;

# Event
use pocketmine\event\player\PlayerJoinEvent;

#Network
use pocketmine\network\protocol\AddEntityPacket;
use pocketmine\network\protocol\RemoveEntityPacket;

# Utils
use pocketmine\utils\UUID;
use pocketmine\utils\TextFormat as Color;

class Main extends PluginBase implements Listener{
  const NAME = 'Texter',
        //NOTE: このバージョンを変えた場合、正常な動作をしない場合があります
        VERSION = '_education';

  /**
   * 浮き文字を追加します
   *
   * @param Player $player
   * @param array $pos
   * @param string $title
   * @param string $text
   * @param string $ownername
   */
  public function addFtp(Player $player, array $pos, string $title, string $text){
    $level = $player->getLevel();
    $levelname = $level->getName();
    $this->entityId = Entity::$entityCount++;
    $pk = new AddEntityPacket();
    $pk->eid = $this->entityId;
    $pk->type = ItemEntity::NETWORK_ID;
    $pk->x = $pos[0];
    $pk->y = $pos[1];
    $pk->z = $pos[2];
    $pk->speedX = 0;
    $pk->speedY = 0;
    $pk->speedZ = 0;
    $pk->yaw = 0;
    $pk->pitch = 0;
    $pk->item = 0;
    $pk->meta = 0;
    $flags = 0;
    $flags |= 1 << Entity::DATA_FLAG_INVISIBLE;
    $flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;
    $flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;
    $flags |= 1 << Entity::DATA_FLAG_IMMOBILE;
    $pk->metadata = [
      Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
      Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title . ($text !== "" ? "\n" . $text : "")],
    ];

    //全員に送信
    $players = $level->getPlayers();
    foreach ($players as $pl) {
        $pl->dataPacket($pk);
    }
    return $this->entityId;
  }

  /**
   * 指定IDの浮き文字を削除します
   *
   * @param Player $player
   * @param string $id
   */
  public function removeFtp(Player $player, string $id){
    $pk = new RemoveEntityPacket();
    $pk->eid = $id;
    $level = $player->getLevel();
    $levelname = $level->getName();
    $players = $level->getPlayers();//Levelにいる人を取得
    foreach ($players as $pl) {
      $pl->dataPacket($pk);
    }
  }

  /****************************************************************************/
  /**
   * PMMPPluginBase APIs
   */

  public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this,$this);
    $this->getLogger()->info(Color::GREEN.self::NAME." ".self::VERSION." が有効化されました");
  }

  public function onJoin(PlayerJoinEvent $e){
    //前準備
    $player = $e->getPlayer();
    $pos = [
      128,//x座標
      60, //y座標
      128 //z座標
    ];
    $title = "タイトル";
    $text = "1行目\n二行目";
    $this->addFtp($player, $pos, $title, $text);//addFtpに処理を移す 引数を関数に合わせてセットする(順番も同じようにセットする)
  }

  public function onDisable(){
    $this->getLogger()->info(Color::RED.self::NAME." が無効化されました");
  }
}
