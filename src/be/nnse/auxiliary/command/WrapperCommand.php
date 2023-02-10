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

use be\nnse\auxiliary\Auxiliary;
use be\nnse\auxiliary\ConfigValue;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Translatable;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\permission\PermissionParser;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

abstract class WrapperCommand extends Command implements PluginOwned
{
    /** @var string */
    private string $default;

    public function __construct(string $name, string $description = "", array $aliases = [], string $default = "op")
    {
        $usageMessage = implode("\n", array_map(function (string $param) use ($name) {
            return "/" . $name . " " . $param;
        }, $this->getParameterDetails()));
        parent::__construct($name, $description, $usageMessage, $aliases);

        $this->setPermission(DefaultPermissionNames::GROUP_USER);
        $this->default = PermissionParser::defaultFromString($default);
    }

    public function getOwningPlugin() : Plugin
    {
        return Auxiliary::getInstance();
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if ($sender instanceof ConsoleCommandSender) {
            $sender->sendMessage(TextFormat::RED . "This command must be executed from in the game");
            return false;
        }
        if (!$this->checkPermission($sender)) {
            $sender->sendMessage(TextFormat::RED . "You do not have permission to use this command");
            return false;
        }
        if (isset($args[0]) && $args[0] === "?") {
            $sender->sendMessage($this->getUsage());
            return false;
        }
        return true;
    }

    public static function broadcastCommandMessage(CommandSender $source, Translatable|string $message, bool $sendToSource = true) : void
    {
        if (ConfigValue::COMMAND_ANNOUNCE_TO_OPERATORS()->get()) {
            parent::broadcastCommandMessage($source, $message, $sendToSource);
        } else {
            $source->sendMessage($message);
        }
    }

    /**
     * @return array
     */
    protected function getParameterDetails() : array
    {
        return [];
    }

    /**
     * @param CommandSender $sender
     * @return bool
     */
    private function checkPermission(CommandSender $sender) : bool
    {
        if ($sender instanceof ConsoleCommandSender) return false;
        $isOp = Server::getInstance()->isOp($sender->getName());
        return match ($this->default) {
            PermissionParser::DEFAULT_OP => $isOp,
            PermissionParser::DEFAULT_NOT_OP => !$isOp,
            PermissionParser::DEFAULT_TRUE => true,
            default => false
        };
    }
}