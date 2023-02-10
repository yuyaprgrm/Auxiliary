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
use pocketmine\world\World;

class TeleportWorldCommand extends WrapperCommand
{
    public function __construct(string $name, array $aliases = [], $default = "op")
    {
        parent::__construct($name, "Teleport between worlds", $aliases, $default);
    }

    protected function getParameterDetails() : array
    {
        return [
            "<world: text> [player: target]"
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if (!parent::execute($sender, $commandLabel, $args)) return null;

        if ($sender instanceof Player) {
            $worlds = $sender->getServer()->getWorldManager()->getWorlds();
            if (!isset($args[0])) {
                $sender->sendMessage(TextFormat::GREEN . "--- List of teleport-able worlds ---");
                foreach ($worlds as $world) {
                    $sender->sendMessage($world->getFolderName());
                }
                return null;
            }

            $target = null;
            foreach($worlds as $world){
                if (strtolower($world->getFolderName()) === $args[0]) {
                    $target = $world;
                    break;
                }
            }
            if ($target === null) {
                $loaded = $sender->getServer()->getWorldManager()->loadWorld($args[0]);
                if (!$loaded) return null;

                $target = $sender->getServer()->getWorldManager()->getWorldByName($args[0]);
            }

            $player = $sender;
            if (isset($args[1]) && is_string($args[1])) {
                $player = $sender->getServer()->getPlayerByPrefix($args[1]);
            }

            if ($player === null) {
                $sender->sendMessage(TextFormat::RED . "Can't find player " . $args[1]);
                return null;
            }

            if ($target instanceof World && $player instanceof Player) {
                $player->teleport($target->getSafeSpawn());

                $format = match (true) {
                    $player !== $sender => "Teleported {%0} to world \"{%1}\"",
                    default => "Teleported to world \"{%1}\""
                };
                $message = str_replace(["{%0}", "{%1}"], [$player->getName(), $target->getFolderName()], $format);
                WrapperCommand::broadcastCommandMessage($sender, $message);
            }
        }
        return null;
    }
}