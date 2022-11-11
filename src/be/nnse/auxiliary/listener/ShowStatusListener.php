<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\listener;

use be\nnse\auxiliary\Auxiliary;
use be\nnse\auxiliary\ConfigValue;
use pocketmine\block\Lava;
use pocketmine\block\Water;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Facing;
use pocketmine\Server;

class ShowStatusListener implements Listener
{
    private const NUMBER_FORMAT = "%.2f";

    /** @var bool */
    private bool $enabledMovementStatus;
    /** @var bool */
    private bool $enabledBlockStatus;
    /** @var bool */
    private bool $showXyz;
    /** @var bool */
    private bool $showYaw;
    /** @var bool */
    private bool $showPitch;
    /** @var bool */
    private bool $showWorld;
    /** @var bool */
    private bool $showFacing;
    /** @var int */
    private int $maxRange;
    /** @var bool */
    private bool $showTarget;
    /** @var bool */
    private bool $showUnder;
    /** @var bool */
    private bool $showAbove;
    /** @var bool */
    private bool $showInWater;
    /** @var bool */
    private bool $showInLava;

    public function __construct()
    {
        $this->enabledMovementStatus = (bool) ConfigValue::MOVEMENT_STATUS()->get();
        $this->enabledBlockStatus = (bool) ConfigValue::BLOCKS_STATUS()->get();

        $this->showXyz = (bool) ConfigValue::MOVEMENT_STATUS_SHOW_XYZ()->get();
        $this->showYaw = (bool) ConfigValue::MOVEMENT_STATUS_SHOW_YAW()->get();
        $this->showPitch = (bool) ConfigValue::MOVEMENT_STATUS_SHOW_PITCH()->get();
        $this->showWorld = (bool) ConfigValue::MOVEMENT_STATUS_SHOW_WORLD()->get();
        $this->showFacing = (bool) ConfigValue::MOVEMENT_STATUS_SHOW_DIRECTION()->get();

        $this->maxRange = (int) ConfigValue::BLOCKS_STATUS_MAX_RANGE()->get();
        $this->showTarget = (bool) ConfigValue::BLOCKS_STATUS_TARGET()->get();
        $this->showUnder = (bool) ConfigValue::BLOCKS_STATUS_UNDER()->get();
        $this->showAbove = (bool) ConfigValue::BLOCKS_STATUS_ABOVE()->get();
        $this->showInWater = (bool) ConfigValue::BLOCKS_STATUS_IN_WATER()->get();
        $this->showInLava = (bool) ConfigValue::BLOCKS_STATUS_IN_LAVA()->get();
    }

    /**
     * @param PlayerMoveEvent $event
     * @return void
     */
    public function onMove(PlayerMoveEvent $event) : void
    {
        if (!$this->enabledMovementStatus && !$this->enabledBlockStatus) return;

        $player = $event->getPlayer();
        if (!Server::getInstance()->isOp($player->getName())) return;

        $text = [];
        if ($this->enabledMovementStatus) {
            $location = $player->getLocation();
            if ($this->showXyz) {
                $x = sprintf(self::NUMBER_FORMAT, $location->getX());
                $y = sprintf(self::NUMBER_FORMAT, $location->getY());
                $z = sprintf(self::NUMBER_FORMAT, $location->getZ());
                $text[] = Auxiliary::getInstance()->formatText("XYZ", $x.", ".$y.", ".$z);
            }
            if ($this->showYaw) {
                $yaw = sprintf(self::NUMBER_FORMAT, $location->getYaw());
                $text[] = Auxiliary::getInstance()->formatText("Yaw", $yaw);
            }
            if ($this->showPitch) {
                $pitch = sprintf(self::NUMBER_FORMAT, $location->getPitch());
                $text[] = Auxiliary::getInstance()->formatText("Pitch", $pitch);
            }
            if ($this->showWorld) {
                $worldName = $location->getWorld()->getFolderName();
                $text[] = Auxiliary::getInstance()->formatText("World", $worldName);
            }
            if ($this->showFacing) {
                $face = $player->getHorizontalFacing();
                $text[] = Auxiliary::getInstance()->formatText("Direction", Facing::toString($face));
            }
        }
        $text[] = "";

        if ($this->enabledBlockStatus) {
            if ($this->showTarget) {
                $target = $player->getTargetBlock($this->maxRange);
                $data = $target->getName() . " | " . $target->getId() . ":" . $target->getMeta();
                $text[] = Auxiliary::getInstance()->formatText("Target ", $data);
            }
            if ($this->showUnder) {
                $under = $player->getWorld()->getBlock($player->getPosition()->subtract(0, 0.2, 0));
                $data = $under->getName() . " | " . $under->getId() . ":" . $under->getMeta();
                $text[] = Auxiliary::getInstance()->formatText("Under ", $data);
            }
            if ($this->showAbove) {
                $above = $player->getWorld()->getBlock($player->getEyePos()->add(0, 0.5, 0));
                $data = $above->getName() . " | " . $above->getId() . ":" . $above->getMeta();
                $text[] = Auxiliary::getInstance()->formatText("Above ", $data);
            }
            if ($this->showInWater) {
                $data = $player->getWorld()->getBlock($player->getPosition()->add(0, 0.2, 0)) instanceof Water;
                $text[] = Auxiliary::getInstance()->formatText("In water ", ($data ? "True" : "False"));
            }
            if ($this->showInLava) {
                $data = $player->getWorld()->getBlock($player->getPosition()->add(0, 0.2, 0)) instanceof Lava;
                $text[] = Auxiliary::getInstance()->formatText("In lava ", ($data ? "True" : "False"));
            }
        }

        $message = implode("\n", $text);
        $player->sendTip($message);
    }
}