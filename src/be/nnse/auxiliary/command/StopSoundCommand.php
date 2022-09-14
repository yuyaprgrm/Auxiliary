<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\command;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\StopSoundPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class StopSoundCommand extends DebugCommand
{
    public function __construct(string $name, array $aliases = [])
    {
        parent::__construct($name, "Stop sound effect", $aliases);
    }

    public function getParameterDetails() : array
    {
        return [
            "<soundName: text>",
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if (!parent::execute($sender, $commandLabel, $args)) return null;

        if ($sender instanceof Player) {
            $soundName = "";
            if (!isset($args[0])) {
                $sender->sendMessage(TextFormat::RED . "Sound name is not defined");
                return null;
            }

            $sender->getNetworkSession()->sendDataPacket(StopSoundPacket::create($soundName, true));

            $message = "Stop the sound \"" . $soundName . "\"";
            DebugCommand::broadcastCommandMessage($sender, $message);
        }
        return null;
    }
}