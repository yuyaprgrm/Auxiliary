<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\listener;

use be\nnse\auxiliary\Auxiliary;
use be\nnse\auxiliary\ConfigValue;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class CatchPacketListener implements Listener
{
    /** @var string[] */
    private array $ignorePacketNames;
    /** @var bool */
    private mixed $allowClientBoundPacket;
    /** @var bool */
    private mixed $allowServerBoundPacket;

    public function __construct()
    {
        $this->ignorePacketNames = (array) ConfigValue::IGNORE_PACKETS()->get();
        $this->allowClientBoundPacket = (bool) ConfigValue::CATCH_CLIENT_PACKET()->get();
        $this->allowServerBoundPacket = (bool) ConfigValue::CATCH_SERVER_PACKET()->get();
    }

    public function onSendData(DataPacketSendEvent $event) : void
    {
        if (!$this->allowClientBoundPacket) return;
        $ops = array_map(function (Player $player) {
            return Server::getInstance()->isOp($player->getName());
        }, Server::getInstance()->getOnlinePlayers());
        foreach ($event->getPackets() as $pk) {
            $name = $pk->getName();
            if (in_array($name, $this->ignorePacketNames)) continue;
            foreach ($ops as $op) {
                if ($op instanceof Player && $op->isConnected()) {
                    $op->sendMessage(Auxiliary::getInstance()->formatText("SEND", $name));
                }
            }
        }
    }

    public function onReceiveData(DataPacketReceiveEvent $event) : void
    {
        $pk = $event->getPacket();
        if (!$this->allowServerBoundPacket) return;
        if (in_array($pk->getName(), $this->ignorePacketNames)) return;

        $player = $event->getOrigin()?->getPlayer();
        if ($player instanceof Player && $player->isConnected() && $player->getServer()->isOp($player->getName())) {
            $player->sendMessage(Auxiliary::getInstance()->formatText("RECEIVE", $pk->getName()));
        }
    }
}