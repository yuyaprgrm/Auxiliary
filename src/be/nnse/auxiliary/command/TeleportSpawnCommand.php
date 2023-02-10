<?php

/*
 * ┌┐┌┌┐┌┌─┐┌─┐  ║  ╔═╗┬ ┬─┐ ┬┬┬  ┬┌─┐┬─┐┬ ┬
 * ││││││└─┐├┤   ║  ╠═╣│ │┌┴┬┘││  │├─┤├┬┘└┬┘
 * ┘└┘┘└┘└─┘└─┘  ║  ╩ ╩└─┘┴ └─┴┴─┘┴┴ ┴┴└─ ┴
 *
 * Plugin which assist to make plugin.
 * @author nnse
 */

declare(strict_types=1);

namespace be\nnse\auxiliary\command;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class TeleportSpawnCommand extends WrapperCommand
{
    public function __construct(string $name, array $aliases = [], $default = "op")
    {
        parent::__construct($name, "Teleport to spawn point of the world", $aliases, $default);
    }

    protected function getParameterDetails() : array
    {
        return [
            "[player: target]"
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if (!parent::execute($sender, $commandLabel, $args)) return null;

        $player = $sender;
        if (isset($args[0])) {
            $player = $sender->getServer()->getPlayerByPrefix((string) $args[0]);
            if ($player === null) {
                $sender->sendMessage(TextFormat::RED . "Can't find player " . $args[0]);
                return null;
            }
        }

        if ($player instanceof Player) {
            $world = $player->getWorld();
            $player->teleport($world->getSafeSpawn());

            $format = match (true) {
                $player !== $sender => "Teleported {%0} to spawn point in \"{%1}\"",
                default => "Teleported to spawn point in \"{%1}\""
            };
            $message = str_replace(["{%0}", "{%1}"], [$player->getName(), $world->getFolderName()], $format);
            WrapperCommand::broadcastCommandMessage($sender, $message);
        }
        return null;
    }
}