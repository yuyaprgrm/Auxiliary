<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\utils\TextFormat;

abstract class DebugCommand extends Command
{
    public function __construct(string $name, string $description = "", array $aliases = [])
    {
        $usageMessage = implode("\n", array_map(function (string $param) use ($name) {
            return "/" . $name . $param;
        }, $this->getParameterDetails()));
        parent::__construct($name, $description, $usageMessage, $aliases);

        $this->setPermission(DefaultPermissionNames::GROUP_OPERATOR);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if ($sender instanceof ConsoleCommandSender) {
            $this->executedFromConsole($sender);
            return false;
        }

        if (isset($args[0]) && $args[0] === "?") {
            $sender->sendMessage($this->getUsage());
            return false;
        }
        return true;
    }

    /**
     * @param CommandSender $sender
     * @return void
     */
    public function executedFromConsole(CommandSender $sender) : void
    {
        $sender->sendMessage(TextFormat::RED . "This command must be executed from in the game.");
    }

    /**
     * @return array
     */
    public function getParameterDetails() : array
    {
        return [];
    }
}