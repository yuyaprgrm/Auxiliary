<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\command;

use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class UpdatePlayerFlagCommand extends WrapperCommand
{
    /** @var string[] */
    private array $flagNames = [];

    public function __construct(string $name, array $aliases = [], $default = "op")
    {
        parent::__construct($name, "Update target player's metadata flag", $aliases, $default);

        $reflectionClass = new \ReflectionClass(EntityMetadataFlags::class);
        foreach ($reflectionClass->getConstants() as $key => $value) {
            $this->flagNames[$value] = $key;
        }
    }

    public function getParameterDetails() : array
    {
        return [
            "<id: int> <value: text> [player: target]"
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
            if (!is_numeric($args[0])) {
                $sender->sendMessage(TextFormat::RED . "Type of entity metadata flag key is must numeric, not string");
                return null;
            } else {
                $key = (int) $args[0];
            }

            if (!isset($args[1])) {
                $sender->sendMessage(TextFormat::RED . "Value is not defined");
                return null;
            }
            if (is_numeric($args[1]) || $args[1] === "") {
                $sender->sendMessage(TextFormat::RED . "Type of value is must string, not integer or null");
                return null;
            } else {
                $value = isset(array_flip(["true", "t"])[strtolower($args[1])]);
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
                $player->getNetworkProperties()->setGenericFlag($key, $value);

                $format = match (true) {
                    $player !== $sender => "Updated {%0}'s metadata flag: {%1} to \"{%2}\"",
                    default => "Updated metadata flag: {%1} to \"{%2}\""
                };
                $message = str_replace(
                    ["{%0}", "{%1}", "{%2}"],
                    [$player->getName(), $this->flagNames[$key] ?? "UNKNOWN", $value ? "true" : "false"],
                    $format
                );
                WrapperCommand::broadcastCommandMessage($sender, $message);
            }
        }
        return null;
    }
}