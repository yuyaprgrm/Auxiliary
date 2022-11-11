<?php

declare(strict_types=1);

namespace be\nnse\auxiliary;

use be\nnse\auxiliary\command\CheckItemCommand;
use be\nnse\auxiliary\command\KillEntityCommand;
use be\nnse\auxiliary\command\PlaySoundCommand;
use be\nnse\auxiliary\command\SpawnEntityCommand;
use be\nnse\auxiliary\command\StopSoundCommand;
use be\nnse\auxiliary\command\TeleportSpawnCommand;
use be\nnse\auxiliary\command\TeleportWorldCommand;
use be\nnse\auxiliary\command\SetPlayerFlagCommand;
use be\nnse\auxiliary\command\SetPlayerPropertyCommand;
use be\nnse\auxiliary\listener\AutoShutdownListener;
use be\nnse\auxiliary\listener\CatchPacketListener;
use be\nnse\auxiliary\listener\ShowStatusListener;
use pocketmine\plugin\PluginBase;

class Auxiliary extends PluginBase
{
    /**
     * @return void
     */
    public function onEnable() : void
    {
        self::$instance = $this;
        $this->registerAutoShutdownListener();
        $this->registerCatchPacketListener();
        $this->registerShowStatusListener();

        $this->registerCommands();
        $this->adaptTimeSetting();
    }

    /**
     * @param string $title
     * @param string $value
     * @return string
     */
    public function formatText(string $title, mixed $value) : string
    {
        $format = (string) ConfigValue::VALUE_FORMAT()->get();
        return str_replace(["{%0}", "{%1}"], [$title, (string) $value], $format);
    }


    /**
     * @return void
     */
    private function registerAutoShutdownListener() : void
    {
        if (ConfigValue::AUTO_SHUTDOWN()->get()) {
            $this->getServer()->getPluginManager()->registerEvents(new AutoShutdownListener(), $this);
        }
    }

    /**
     * @return void
     */
    private function registerCatchPacketListener() : void
    {
        if (ConfigValue::CATCH_PACKET()->get()) {
            $this->getServer()->getPluginManager()->registerEvents(new CatchPacketListener(), $this);
        }
    }

    /**
     * @return void
     */
    private function registerShowStatusListener() : void
    {
        if (ConfigValue::MOVEMENT_STATUS()->get() || ConfigValue::BLOCKS_STATUS()->get()) {
            $this->getServer()->getPluginManager()->registerEvents(new ShowStatusListener(), $this);
        }
    }

    /**
     * @return void
     */
    private function registerCommands() : void
    {
        if (ConfigValue::COMMAND()->get()) {
            $commands = [];
            if (ConfigValue::COMMAND_CHECK_ITEM()->get()) {
                $commands[] = new CheckItemCommand(
                    (string) ConfigValue::COMMAND_CHECK_ITEM_NAME()->get(),
                    (array) ConfigValue::COMMAND_CHECK_ITEM_ALIASES()->get(),
                    (string) ConfigValue::COMMAND_CHECK_ITEM_PERMISSION()->get()
                );
            }
            if (ConfigValue::COMMAND_KILL_ENTITY()->get()) {
                $commands[] = new KillEntityCommand(
                    (string) ConfigValue::COMMAND_KILL_ENTITY_NAME()->get(),
                    (array) ConfigValue::COMMAND_KILL_ENTITY_ALIASES()->get(),
                    (string) ConfigValue::COMMAND_KILL_ENTITY_PERMISSION()->get(),
                );
            }
            if (ConfigValue::COMMAND_SPAWN_ENTITY()->get()) {
                $commands[] = new SpawnEntityCommand(
                    (string) ConfigValue::COMMAND_SPAWN_ENTITY_NAME()->get(),
                    (array) ConfigValue::COMMAND_SPAWN_ENTITY_ALIASES()->get(),
                    (string) ConfigValue::COMMAND_SPAWN_ENTITY_PERMISSION()->get(),
                );
            }
            if (ConfigValue::COMMAND_TP_WORLD()->get()) {
                $commands[] = new TeleportWorldCommand(
                    (string) ConfigValue::COMMAND_TP_WORLD_NAME()->get(),
                    (array) ConfigValue::COMMAND_TP_WORLD_ALIASES()->get(),
                    (string) ConfigValue::COMMAND_TP_WORLD_PERMISSION()->get(),
                );
            }
            if (ConfigValue::COMMAND_TP_SPAWN()->get()) {
                $commands[] = new TeleportSpawnCommand(
                    (string) ConfigValue::COMMAND_TP_SPAWN_NAME()->get(),
                    (array) ConfigValue::COMMAND_TP_SPAWN_ALIASES()->get(),
                    (string) ConfigValue::COMMAND_TP_SPAWN_PERMISSION()->get(),
                );
            }
            if (ConfigValue::COMMAND_PLAY_SOUND()->get()) {
                $commands[] = new PlaySoundCommand(
                    (string) ConfigValue::COMMAND_PLAY_SOUND_NAME()->get(),
                    (array) ConfigValue::COMMAND_PLAY_SOUND_ALIASES()->get(),
                    (string) ConfigValue::COMMAND_PLAY_SOUND_PERMISSION()->get(),
                );
            }
            if (ConfigValue::COMMAND_STOP_SOUND()->get()) {
                $commands[] = new StopSoundCommand(
                    (string) ConfigValue::COMMAND_STOP_SOUND_NAME()->get(),
                    (array) ConfigValue::COMMAND_STOP_SOUND_ALIASES()->get(),
                    (string) ConfigValue::COMMAND_STOP_SOUND_PERMISSION()->get(),
                );
            }
            if (ConfigValue::COMMAND_SET_PLAYER_FLAG()->get()) {
                $commands[] = new SetPlayerFlagCommand(
                    (string) ConfigValue::COMMAND_SET_PLAYER_FLAG_NAME()->get(),
                    (array) ConfigValue::COMMAND_SET_PLAYER_FLAG_ALIASES()->get(),
                    (string) ConfigValue::COMMAND_SET_PLAYER_FLAG_PERMISSION()->get(),
                );
            }
            if (ConfigValue::COMMAND_SET_PLAYER_PROPERTY()->get()) {
                $commands[] = new SetPlayerPropertyCommand(
                    (string) ConfigValue::COMMAND_SET_PLAYER_PROPERTY_NAME()->get(),
                    (array) ConfigValue::COMMAND_SET_PLAYER_PROPERTY_ALIASES()->get(),
                    (string) ConfigValue::COMMAND_SET_PLAYER_PROPERTY_PERMISSION()->get(),
                );
            }

            Auxiliary::getInstance()->getServer()->getCommandMap()->registerAll("auxiliary", $commands);
        }
    }

    /**
     * @return void
     */
    private function adaptTimeSetting() : void
    {
        $stopTime = (bool) ConfigValue::STOP_TIME()->get();
        if (!$stopTime) return;

        $time = (int) ConfigValue::STOP_TIME_TIME()->get();
        $loadedWorlds = $this->getServer()->getWorldManager()->getWorlds();
        foreach ($loadedWorlds as $loadedWorld) {
            $loadedWorld->setTime($time);
            $loadedWorld->stopTime();
        }
    }


    /** @var self */
    private static self $instance;

    /**
     * @return self
     */
    public static function getInstance() : self
    {
        return self::$instance;
    }
}