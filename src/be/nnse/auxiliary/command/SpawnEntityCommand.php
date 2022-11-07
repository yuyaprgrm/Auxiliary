<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\command;

use be\nnse\auxiliary\Auxiliary;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityFactory;
use pocketmine\entity\Villager;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ReflectionClass;

class SpawnEntityCommand extends DebugCommand
{
    public function __construct(string $name, array $aliases = [])
    {
        parent::__construct($name, "Spawn entity in the world", $aliases);
    }

    public function getParameterDetails() : array
    {
        return [
            "<entityName: text> [nameTag: text]",
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if (!parent::execute($sender, $commandLabel, $args)) return null;

        if (!$sender instanceof Player) return null;

        if (!isset($args[0])) return null;

        if (is_numeric($args[0]) || $args[0] === "") {
            $sender->sendMessage(TextFormat::RED . "Type of entity name is must string, not integer or null");
            return null;
        } else {
            $entityName = $args[0];
        }

        $reflectionClass = new ReflectionClass(EntityFactory::class);
        $property = $reflectionClass->getProperty("saveNames");
        $property->setAccessible(true);
        $rSaveNames = $property->getValue(EntityFactory::getInstance());
        $class = null;
        foreach ($rSaveNames as $className => $v) {
            if ($entityName === $v) {
                $class = $className;
                break;
            }
        }
        $property->setAccessible(false);

        if ($class === null) {
            $sender->sendMessage(TextFormat::RED . "The entity \"" . $entityName . "\" could not find");
            return false;
        }

        $customName = null;
        if (isset($args[1])) {
            if (is_numeric($args[1])) {
                $sender->sendMessage(TextFormat::RED . "Type of entity custom name is must string, not integer or null");
                return null;
            } else {
                $customName = $args[1];
            }
        }

        /** @var Entity $entity */
        $entity = new $class($sender->getLocation());
        $entity->spawnToAll();

        if ($customName !== null) {
            $entity->setNameTag($customName);
            $entity->setNameTagVisible();
            $entity->setNameTagAlwaysVisible();
        }

        $message = "Spawn new entity \"" . $entity::getNetworkTypeId() . "\" in this world";
        DebugCommand::broadcastCommandMessage($sender, $message);
        return null;
    }
}