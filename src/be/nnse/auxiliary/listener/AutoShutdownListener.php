<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Server;

class AutoShutdownListener implements Listener
{
    public function onQuit(PlayerQuitEvent $event) : void
    {
        $count = count(Server::getInstance()->getOnlinePlayers());
        if ($count == 1) Server::getInstance()->shutdown();
    }
}