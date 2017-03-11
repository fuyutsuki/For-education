<?php

// 必要なuse
use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\entity\Item as ItemEntity;
use pocketmine\network\protocol\AddEntityPacket;

  /**
   * 浮き文字を追加するよ
   *
   * @param Playerオブジェクト $player
   * @param array(配列) $pos
   *                    |- [0] => x座標
   *                    |- [1] => y座標
   *                    \- [2] => z座標
   * @param string(文字列) $title
   * @param string(文字列) $text
   * @param string(文字列) $ownername
   */
  /*public*/ function addFtp(Player $player, array $pos, string $title, string $text, string $ownername){
    /* 前準備 /*/
    $level = $player->getLevel(); //プレイヤーのいるワールドを取得
    $this->entityId = Entity::$entityCount++; //エンティティ(浮き文字)のid (eid)を一つ増やす

    /* パケットオブジェクト生成 */
    $pk = new AddEntityPacket();//AddEntityPacketという機能を持った箱(名前は$pk)を作成
    /*
       ________________
      /     [pk]      /|
     /               / |   < AddEntityPacketをすることができるよ！
     |￣￣￣￣￣￣￣￣| /
     |               |/     この中の〇〇〇(要素)をセットするときには
     ￣￣￣￣￣￣￣￣￣        $pk->〇〇〇 = ... と書くよ
    */
    $pk->eid = $this->entityId;//$pkの中のeidっていうものをセットするよ
    $pk->type = ItemEntity::NETWORK_ID;//エンティティのネットワークID
    // ↑ $pk->type = 64; でも良い
    $pk->x = $pos[0];//x座標
    $pk->y = $pos[1];//y座標
    $pk->z = $pos[2];//z座標
    $pk->speedX = 0;//0で良い
    $pk->speedY = 0;//0で良い (入れた場合にはその分x,y,zの向きに移動する)
    $pk->speedZ = 0;//0で良い
    $pk->yaw = 0;//FloatingTextなので必要ない
    $pk->pitch = 0;//上と同じなので略
    $pk->item = 0;/* エンティティのアイテム(0で良い)
      もしなにかアイテムを表示したい場合は、Item::get(1 [ID], 0 [meta], 1 [個数]); で石*/
    $pk->meta = 0;//meta値
    $flags = 0;//初期化
    //pocketmine\entity\Entity.php内に DATA_FLAG_....という定数がいっぱいあるので探して見るべし
    $flags |= 1 << Entity::DATA_FLAG_INVISIBLE;//INVISIBLE = エンティティ自体をみえないように
    $flags |= 1 << Entity::DATA_FLAG_CAN_SHOW_NAMETAG;//CAN_SHOW_NAMETAG = ネームタグをカーソルを合わせたとき表示可能に
    $flags |= 1 << Entity::DATA_FLAG_ALWAYS_SHOW_NAMETAG;//ALWAYS_SHOW_NAMETAG = ネームタグを常に表示
    $flags |= 1 << Entity::DATA_FLAG_IMMOBILE;//IMMOBILE = 移動しないように
    $pk->metadata = [//metadata 詳しくは pocketmine\entity\Entity.phpの protected $dataProperties ($this->dataPropertiesと同じ意味)
      Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
      Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title . ($text !== "" ? "\n" . $text : "")],
    ];

    /* パケット送信処理 */

    /*
    ＊全員にパケットを送る場合
    $players = $level->getPlayers();//ワールドにいるプレイヤーを配列で取得
      |- [乱数] => プレイヤーオブジェクト
      |- [乱数] => プレイヤーオブジェクト
      |- [ .......(いる分だけ続く)
    foreach ($players as $pl) {// foreachで一人一人に処理
      $pl->dataPacket($pk);//$pkをプレイヤーに送信
    }
    */

    /*
    ＊プレイヤー個人に送る場合(そのプレイヤーにしか見えなくする場合)
    $pl->dataPacket($pk);//$pkをプレイヤーに送信
    */
  }
