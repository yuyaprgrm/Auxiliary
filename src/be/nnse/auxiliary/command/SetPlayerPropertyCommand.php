<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\command;

use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\entity\BlockPosMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\ByteMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataTypes;
use pocketmine\network\mcpe\protocol\types\entity\FloatMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\LongMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\MetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\ShortMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\network\mcpe\protocol\types\entity\Vec3MetadataProperty;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class SetPlayerPropertyCommand extends WrapperCommand
{
    /** @var string[] */
    private array $entityMetadataTypeNames = [];
    /** @var string[] */
    private array $entityMetadataPropertyNames = [];

    public function __construct(string $name, array $aliases = [], $default = "op")
    {
        parent::__construct($name, "Set target player's metadata property", $aliases, $default);

        foreach ((new \ReflectionClass(EntityMetadataTypes::class))->getConstants() as $key => $value) {
            $this->entityMetadataTypeNames[$value] = $key;
        }
        foreach ((new \ReflectionClass(EntityMetadataProperties::class))->getConstants() as $key => $value) {
            $this->entityMetadataPropertyNames[$value] = $key;
        }
    }

    public function getParameterDetails() : array
    {
        return [
            "<property: int> <value: text> [player: target]",
            "<property: text> <value: text> [player: target]"
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if (!parent::execute($sender, $commandLabel, $args)) return null;

        if ($sender instanceof Player) {
            if (!isset($args[0])) {
                $sender->sendMessage(TextFormat::RED . "Entity metadata property key is not defined");
                return null;
            }
            if (is_numeric($args[0])) {
                if ($args[0] === "0") {
                    $sender->sendMessage(TextFormat::RED . "0 is not valid key");
                    return null;
                }
                $propertyKey = (int) $args[0];
            } else {
                $searchPropertyName = strtoupper($args[0]);
                $flippedMetadataPropertyNames = array_flip($this->entityMetadataPropertyNames);
                if (!isset($flippedMetadataPropertyNames[$searchPropertyName])) {
                    $sender->sendMessage(TextFormat::RED . $args[0] . " is an undefined property");
                    return null;
                } else {
                    $propertyKey = (int) $flippedMetadataPropertyNames[$searchPropertyName];
                }
            }
            $propertyName = $this->entityMetadataPropertyNames[$propertyKey] ?? "UNKNOWN";

            if (!isset($args[1])) {
                $sender->sendMessage(TextFormat::RED . "Value is not defined");
                return null;
            }
            $rawPropertyValue = $args[1];
            $propertyValue = $this->getCorrectValue($rawPropertyValue);
            $typeName = $this->entityMetadataTypeNames[$propertyValue->getTypeId()] ?? "UNKNOWN";

            $player = $sender;
            if (isset($args[2]) && is_string($args[2])) {
                $player = $sender->getServer()->getPlayerByPrefix($args[2]);
            }
            if ($player === null) {
                $sender->sendMessage(TextFormat::RED . "Can't find player " . $args[2]);
                return null;
            }

            if ($player instanceof Player) {
                try {
                    $player->getNetworkProperties()->set($propertyKey, $propertyValue);
                } catch (\Exception $e) {
                    $player->sendMessage(TextFormat::RED . "The value type is different");
                    return null;
                }

                $format = match (true) {
                    $player !== $sender => "Set {%0}'s metadata property: {%1} to \"{%2}\" ({%3})",
                    default => "Set metadata property: {%1} to \"{%2}\" ({%3})"
                };
                $message = str_replace(
                    ["{%0}", "{%1}", "{%2}", "{%3}"],
                    [$player->getName(), $propertyName, $rawPropertyValue, $typeName],
                    $format
                );
                WrapperCommand::broadcastCommandMessage($sender, $message);
            }
        }
        return null;
    }

    /**
     * @param string $raw
     * @return MetadataProperty
     */
    private function getCorrectValue(string $raw) : MetadataProperty
    {
        // int
        if (ctype_digit($raw)) {
            return new IntMetadataProperty((int) $raw);
        }
        // byte, float, short, long, (string)
        if (preg_match("/[bfsl]$/", $raw)) {
            $type = substr($raw , -1);
            $rawValue = substr($raw, 0, -1);
            if (!is_numeric($rawValue)) {
                // string
                return new StringMetadataProperty($raw);
            }
            return match ($type) {
                "b" => new ByteMetadataProperty((int) $rawValue),
                "f" => new FloatMetadataProperty((float) $rawValue),
                "s" => new ShortMetadataProperty((int) $rawValue),
                "l" => new LongMetadataProperty((int) $rawValue)
            };
        }
        // v3f
        if (str_contains($raw, "v#")) {
            $coords = explode(",", str_replace("v#", "", $raw));
            $x = (float) $coords[0] ?? 0;
            $y = (float) $coords[1] ?? 0;
            $z = (float) $coords[2] ?? 0;
            return new Vec3MetadataProperty(new Vector3($x, $y, $z));
        }
        // block pos
        if (str_contains($raw, "b#")) {
            $coords = explode(",", str_replace("b#", "", $raw));
            $x = (int) $coords[0] ?? 0;
            $y = (int) $coords[1] ?? 0;
            $z = (int) $coords[2] ?? 0;
            return new BlockPosMetadataProperty(new BlockPosition($x, $y, $z));
        }
        // string
        return new StringMetadataProperty($raw);
    }
}