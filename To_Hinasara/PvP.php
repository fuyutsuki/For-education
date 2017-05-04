<?php
namespace test;

//必要なuse
use pocketmine\plugin\PluginBase;//これがないとプラグインにならない
use pocketmine\event\Listener;//イベントに反応する

use pocketmine\event\player\PlayerJoinEvent;//入った時のイベント
use pocketmine\event\server\DataPacketReceiveEvent;//パケットを受信したときのイベント

use pocketmine\network\protocol\InteractPacket;//何かエンティティをタップしたとき発生するパケット

/**
 * Example Code
 */
class PvP/*ファイル名*/ extends PluginBase/*ここでPluginBaseをuseしてないとエラー*/ implements Listener /*ここでListenerをuseしてないとエラー*/{

  public $flag = [];//$this->flag = Array()

  public function onEnable(){
    $this->getServer()->getPluginManager()->registerEvents($this,$this);//イベントが動作するように
  }

  public function onJoin(PlayerJoinEvent $event){
    $player = $event->getPlayer();
    $name = $player->getName();
    $this->flag[$name] = false;//入ってきた人を申し込まれていない状態に
    //print_r($this->flag); //中身を見てみる
    /*
      ここで$this->flag(連想配列)をprint_r($this->flag);で中身を見てみると
      Array(
        ["名前"] => false
      )
      となる。
        [] 内にあるのがキーで、$this->flag["名前"] とすると、値 false を取り出せる。

        (例えば: "名前"という扉の中に入っているのがfalse)
     */
  }

  public function onPacketReceive(DataPacketReceiveEvent $event){
    $packet/*普通は$pk と略す場合が多い*/ = $event->getPacket();
    if ($packet instanceof InteractPacket and//タップしたパケットか
        (int)$packet->action === InteractPacket::ACTION_LEFT_CLICK) {//タップしたのか
      $player = $event->getPlayer();
      $name = $player->getName();//NOTE: 申し込んだ人の名前

      if ($this->flag[$name] === false) {//もしまだ申し込んでいなければ
        $level = $player->getLevel();
        $entity = $level->getEntity($packet->target);/* $packet->targetにはタップした相手のeidが入ってます */

        if ($entity instanceof Player) {//エンティティには動物などのmobも含まれるのでプレイヤーのみに絞る
          $entity->sendMessage("{$name} さんに申し込まれました");
          $player->sendMessage("{$entity->getName()} さんに申し込みました");
          $this->flag[$name] = $entity->getName();//
          /* $this->flagの中身
            Array(
              ["申し込んだ人の名前"] => "申し込まれた人の名前"
            )
          */
        }
      }else{//申し込んでいれば
        $player->sendMessage("既に{$this->flag[$name]} さんに申し込んでいます");
      }
    }
  }

  /*
    あとはコマンドで承認すればテレポートして試合開始、とか、全体の流れを文にして書き出してみて下さい
    それをコードに起こすのがプログラミングです
    また、$this->flag の flagの部分などは 変数 なので、名前のつけ方は自由です。
    書いていてわかりやすい名前を付ければいいでしょう。
    例:
      $this->flag = true;
      $this->hata = true;
      $aiueo = "あいうえお";
      $akasatana = "あかさたな";

    注意: このような文は変数の値が上書きされてしまうので注意しましょう
      $name = $player->getName(); //Aさんの名前を取得
      echo $name; //実行結果: "Aさん"
      $name = $entity->getName(); //Bさんの名前を取得
      echo $name; //実行結果: "Bさん"
  */
  /****************************************************************************/
  /* 課題(っぽいの)
    Q1. $array = []; という変数があります。そこに、以下の文を用いて値を入れました。
        出力されるのは次のうちどれでしょう?*/

      $array["a"] = "a";
      $array["b"] = "b";
      $array["a"] = "c";

      echo $array["a"];

    /*選択肢:
      1. "a"
      2. "b"
      3. "c"

    答え:
      3. "c"
    解説:
      最初の*/$array["a"] = "a" /*で、"a"を代入されますが、三行目で*/$array["a"] = "c" /*を代入しています。
      同じキーを指定した場合、古い値は破棄され、新しい値が代入されます。

    ----------------------------------------------------------------------------

    Q2. $array = []; という変数があります。そこに、以下の文を用いて値を入れました。
        出力されるのは次のうちどれでしょう?*/

      $array["array"] = [];
      $array["array"]["a"] = "a";
      $array["array"]["b"] = "b";
      $array["array"]["b"] = "";

      echo $array["array"]["b"];

    /*選択肢:
      1. 何も出力されない
      2. "a"
      3. "b"

      答え:
        1. 何も出力されない
      解説:
        多次元配列(配列の中に配列が入っている)の問題です。少し難しいかもしれません。
        最初に*/ $array["array"] = [] /*で、
          $array = Array(
            ["array"] = Array()
          )
        となり、
        次の*/ $array["array"]["a"] = "a"/*,*/ $array["array"]["b"] = "b" /*で以下のようになります。
          $array = Array(
            ["array"] = Array(
                          ["a"] => "a"
                          ["b"] => "b"
                        )
          )
        となります。
        そして最後に、*/$array["array"]["b"] = "" /*として上書きしてますので、最終的に以下のような配列になります。
          $array = Array(
            ["array"] = Array(
                          ["a"] => "a"
                          ["b"] =>
                        )
          )
        よって、出力結果は何も出力されない、が正解です。

    分かりにくかったかもしれませんが好評ならまたやります
  */
}
