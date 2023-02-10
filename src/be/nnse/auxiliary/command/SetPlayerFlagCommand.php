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
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class SetPlayerFlagCommand extends WrapperCommand
{
    /** @var string[] */
    private array $entityMetadataFlagNames = [];

    public function __construct(string $name, array $aliases = [], $default = "op")
    {
        parent::__construct($name, "Set target player's metadata flag", $aliases, $default);

        $reflectionClass = new \ReflectionClass(EntityMetadataFlags::class);
        foreach ($reflectionClass->getConstants() as $key => $value) {
            $this->entityMetadataFlagNames[$value] = $key;
        }
    }

    protected function getParameterDetails() : array
    {
        return [
            "<flag: int> <value: text> [player: target]",
            "<flag: text> <value: text> [player: target]"
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if (!parent::execute($sender, $commandLabel, $args)) return null;

        if ($sender instanceof Player) {
            if (!isset($args[0])) {
                $sender->sendMessage(TextFormat::RED . "Entity metadata flag key is not defined");
                return null;
            }
            if (is_numeric($args[0])) {
                $flagKey = (int) $args[0];
            } else {
                $searchFlagName = strtoupper($args[0]);
                $flippedEntityMetadataFlagNames = array_flip($this->entityMetadataFlagNames);
                if (!isset($flippedEntityMetadataFlagNames[$searchFlagName])) {
                    $sender->sendMessage(TextFormat::RED . $searchFlagName . " is an undefined flag");
                    return null;
                } else {
                    $flagKey = (int) $flippedEntityMetadataFlagNames[$searchFlagName];
                }
            }
            $flagName = $this->entityMetadataFlagNames[$flagKey] ?? "UNKNOWN";

            if (!isset($args[1])) {
                $sender->sendMessage(TextFormat::RED . "Value is not defined");
                return null;
            }
            if (is_numeric($args[1]) || $args[1] === "") {
                $sender->sendMessage(TextFormat::RED . "Type of value is must string, not integer or null");
                return null;
            } else {
                $flagValue = isset(array_flip(["true", "t"])[strtolower($args[1])]);
            }

            $player = $sender;
            if (isset($args[2]) && is_string($args[2])) {
                $player = $sender->getServer()->getPlayerByPrefix($args[2]);
            }
            if ($player === null) {
                $sender->sendMessage(TextFormat::RED . "Can't find player " . $args[2]);
                return null;
            }

            if ($player instanceof Player) {
                $player->getNetworkProperties()->setGenericFlag($flagKey, $flagValue);

                $format = match (true) {
                    $player !== $sender => "Set {%0}'s metadata flag: {%1} to \"{%2}\"",
                    default => "Set metadata flag: {%1} to \"{%2}\""
                };
                $message = str_replace(
                    ["{%0}", "{%1}", "{%2}"],
                    [$player->getName(), $flagName, $flagValue ? "true" : "false"],
                    $format
                );
                WrapperCommand::broadcastCommandMessage($sender, $message);
            }
        }
        return null;
    }
}