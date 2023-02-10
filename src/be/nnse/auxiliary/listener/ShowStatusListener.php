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

namespace be\nnse\auxiliary\listener;

use be\nnse\auxiliary\ConfigValue;
use be\nnse\auxiliary\Formatter;
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
    private bool $showNext;
    /** @var bool */
    private bool $showAbove;
    /** @var bool */
    private bool $showFront;
    /** @var bool */
    private bool $showBack;
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
        $this->showNext = (bool) ConfigValue::BLOCKS_STATUS_NEXT()->get();
        $this->showAbove = (bool) ConfigValue::BLOCKS_STATUS_ABOVE()->get();
        $this->showFront = (bool) ConfigValue::BLOCKS_STATUS_FRONT()->get();
        $this->showBack = (bool) ConfigValue::BLOCKS_STATUS_BACK()->get();
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
                $text[] = Formatter::getInstance()->xyz2Str("XYZ", $x, $y, $z);
            }
            if ($this->showYaw) {
                $yaw = sprintf(self::NUMBER_FORMAT, $location->getYaw());
                $text[] = Formatter::getInstance()->oto2Str("Yaw", $yaw);
            }
            if ($this->showPitch) {
                $pitch = sprintf(self::NUMBER_FORMAT, $location->getPitch());
                $text[] = Formatter::getInstance()->oto2Str("Pitch", $pitch);
            }
            if ($this->showWorld) {
                $worldName = $location->getWorld()->getFolderName();
                $text[] = Formatter::getInstance()->oto2Str("World", $worldName);
            }
            if ($this->showFacing) {
                $face = $player->getHorizontalFacing();
                $text[] = Formatter::getInstance()->oto2Str("Direction", Facing::toString($face));
            }
        }
        $text[] = "";

        if ($this->enabledBlockStatus) {
            if ($this->showTarget) {
                $target = $player->getTargetBlock($this->maxRange);
                $text[] = Formatter::getInstance()->block2Str("Target", $target);
            }
            if ($this->showUnder) {
                $under = $player->getWorld()->getBlock($player->getPosition()->subtract(0, 0.2, 0));
                $text[] = Formatter::getInstance()->block2Str("Under", $under);
            }
            if ($this->showAbove) {
                $above = $player->getWorld()->getBlock($player->getEyePos()->add(0, 0.5, 0));
                $text[] = Formatter::getInstance()->block2Str("Above", $above);
            }

            $nextVector = $player->getPosition()->asVector3();
            $nextVector->x -= sin(deg2rad($player->getLocation()->yaw)) * 0.45;
            $nextVector->z += cos(deg2rad($player->getLocation()->yaw)) * 0.45;

            if ($this->showNext) {
                $v = clone $nextVector;
                $v->y -= 0.25;
                $nextBlock = $player->getWorld()->getBlock($v);
                $text[] = Formatter::getInstance()->block2Str("Next", $nextBlock);
            }
            if ($this->showFront) {
                $v = clone $nextVector;
                $v->y += 0.5;
                $frontBlock = $player->getWorld()->getBlock($v);
                $text[] = Formatter::getInstance()->block2Str("Front", $frontBlock);
            }
            if ($this->showBack) {
                $backVector = $player->getPosition()->asVector3();
                $backVector->x -= sin(deg2rad($player->getLocation()->yaw + 180)) * 0.45;
                $backVector->z += cos(deg2rad($player->getLocation()->yaw + 180)) * 0.45;
                $backVector->y += 0.5;
                $backBlock = $player->getWorld()->getBlock($backVector);
                $text[] = Formatter::getInstance()->block2Str("Back", $backBlock);
            }
            if ($this->showInWater) {
                $data = $player->getWorld()->getBlock($player->getPosition()->add(0, 0.2, 0)) instanceof Water;
                $text[] = Formatter::getInstance()->bool2Str("In water", $data);
            }
            if ($this->showInLava) {
                $data = $player->getWorld()->getBlock($player->getPosition()->add(0, 0.2, 0)) instanceof Lava;
                $text[] = Formatter::getInstance()->bool2Str("In lava", $data);
            }
        }

        $message = implode("\n", $text);
        $player->sendTip($message);
    }
}