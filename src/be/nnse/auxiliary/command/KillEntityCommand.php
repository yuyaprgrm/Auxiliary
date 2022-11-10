<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\command;

use be\nnse\auxiliary\Auxiliary;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ReflectionClass;

class KillEntityCommand extends DebugCommand
{
    public function __construct(string $name, array $aliases = [])
    {
        parent::__construct($name, "Kill entities in the world", $aliases);
    }

    public function getParameterDetails() : array
    {
        return [
            "<id: int>",
            "<type: text>",
            "<id: int>[,<type: text>,...]"
        ];
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if (!parent::execute($sender, $commandLabel, $args)) return null;

        if ($sender instanceof Player) {
            $entities = $sender->getWorld()->getEntities();
            if (count($args) < 1) {
                $sender->sendMessage(TextFormat::GREEN . "--- List of entity in this world ---");
                foreach ($entities as $entity) {
                    $eid = Auxiliary::getInstance()->formatText("ID", $entity->getId());
                    $type = Auxiliary::getInstance()->formatText("Type", $entity::getNetworkTypeId());
                    $v = $entity->getPosition()->asVector3();
                    $pos = Auxiliary::getInstance()->formatText(
                        "Position",
                        $v->getFloorX().", ".$v->getFloorY().", ".$v->getFloorZ()
                    );

                    $sender->sendMessage($eid.", ".$type.", ".$pos);
                }
                return null;
            }

            if (!isset($args[0])) {
                return null;
            }
            $targets = [];

            // TypeId & integer id
            if (is_string($args[0]) && str_contains($args[0], ",")) {
                $candidates = explode(",", $args[0]);
                foreach ($candidates as $candidate) {
                    if (is_numeric($candidate)) {
                        $eid = (int) $candidate;
                        $target = $sender->getWorld()->getEntity($eid);
                        if ($target !== null) $targets[] = $target;
                    }
                    if (is_string($candidate)) {
                        foreach ($entities as $entity) {
                            $candidateTypeId = $this->getCorrectNetworkTypeId($candidate);
                            $targetTypeId = strtolower($entity::getNetworkTypeId());
                            if ($candidateTypeId === $targetTypeId) {
                                $targets[] = $entity;
                            }
                        }
                    }
                }
            } else {
                // integer id
                if (is_numeric($args[0])) {
                    $eid = (int) $args[0];
                    $target = $sender->getWorld()->getEntity($eid);
                    if ($target !== null) $targets[] = $target;
                }
                // TypeId
                if (is_string($args[0])) {
                    foreach ($entities as $entity) {
                        $candidateTypeId = $this->getCorrectNetworkTypeId($args[0]);
                        $targetTypeId = strtolower($entity::getNetworkTypeId());
                        if ($candidateTypeId === $targetTypeId) {
                            $targets[] = $entity;
                        }
                    }
                }
            }

            $deleted = false;
            $targetEntries = [];
            $includePlayer = false;
            foreach ($targets as $target) {
                if ($target === null) continue;
                if ($target->isClosed()) continue;
                if ($target instanceof Player) {
                    $includePlayer = true;
                    continue;
                }

                $tid = str_replace("minecraft:", "", $target::getNetworkTypeId());
                $targetEntries[$tid] = isset($targetEntries[$tid]) ? $targetEntries[$tid] + 1 : 1;

                $target->flagForDespawn();
                $deleted = true;
            }
            if ($includePlayer) {
                $sender->sendMessage(TextFormat::RED . "Players cannot be despawned");
            }
            if ($deleted) {
                $texts = [];
                foreach ($targetEntries as $id => $count) {
                    if ($count == 1) {
                        $texts[] = $count . " " . $id;
                    } else {
                        $texts[] = $count . " " . $this->toPlural($id);
                    }
                }
                $text = implode(", ", $texts);
                $message = "Target entities have been despawned (" . $text . ")";
                DebugCommand::broadcastCommandMessage($sender, $message);
            }
        }
        return null;
    }

    /**
     * @param string $value
     * @return string|null
     */
    private function getCorrectNetworkTypeId(string $value) : ?string
    {
        if (!str_contains($value, "minecraft:")) {
            $value = "minecraft:$value";
        }
        $reflection = new ReflectionClass(EntityIds::class);
        foreach ($reflection->getConstants() as $constant) {
            if (!is_string($constant)) continue;
            if (strtolower($value) === strtolower($constant)) {
                return $constant;
            }
        }
        return null;
    }

    /**
     * @param string $word
     * @return string
     */
    private function toPlural(string $word) : string
    {
        $tmpStr = $word;
        $tmpStr = preg_replace("/(s|sh|ch|o|x)$/","$1es",$tmpStr);
        $tmpStr = preg_replace("/(f|fe)$/","ves",$tmpStr);
        $tmpStr = preg_replace("/(a|i|u|e|o)y$/","$1ys",$tmpStr);
        $tmpStr = preg_replace("/y$/","ies",$tmpStr);
        if (!str_ends_with($tmpStr, "s")) {
            $tmpStr = $tmpStr . "s";
        }
        return $tmpStr;
    }
}