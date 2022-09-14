<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\command;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PlaySoundCommand extends DebugCommand
{
    public function __construct(string $name, array $aliases = [])
    {
        parent::__construct($name, "Play sound effect", $aliases);
    }

    public function getParameterDetails() : array
    {
        return [
            "<soundName: text> [volume: float] [pitch: float]"
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if (!parent::execute($sender, $commandLabel, $args)) return null;

        if ($sender instanceof Player) {
            $volume = 0.5;
            $pitch = 1.0;
            if (!isset($args[0])) {
                $sender->sendMessage(TextFormat::RED . "Sound name is not defined");
                return null;
            }
            if (is_numeric($args[0]) || $args[0] === "") {
                $sender->sendMessage(TextFormat::RED . "Type of sound name is must string, not integer or null");
                return null;
            } else {
                $soundName = $args[0];
            }

            if (isset($args[1])) {
                if (!is_numeric($args[1])) {
                    $sender->sendMessage(TextFormat::RED . "Type of sound volume is must numeric, not string");
                    return null;
                }
                $volume = (float) $args[1];
            }

            if (isset($args[2])) {
                if (!is_numeric($args[2])) {
                    $sender->sendMessage(TextFormat::RED . "Type of sound pitch is must numeric, not string");
                    return null;
                }
                $pitch = (float) $args[2];
            }

            $pk = new PlaySoundPacket();
            $pk->soundName = $soundName;
            $pk->volume = $volume;
            $pk->pitch = $pitch;
            $pk->x = $sender->getPosition()->getX();
            $pk->y = $sender->getPosition()->getY();
            $pk->z = $sender->getPosition()->getZ();
            $sender->getNetworkSession()->sendDataPacket($pk);

            $message = "Play the sound \"" . $soundName . "\" (volume: " . $volume . ", pitch: " . $pitch . ")";
            DebugCommand::broadcastCommandMessage($sender, $message);
        }
        return null;
    }
}