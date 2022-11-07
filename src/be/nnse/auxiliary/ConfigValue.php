<?php

declare(strict_types=1);

namespace be\nnse\auxiliary;

use pocketmine\utils\EnumTrait;

/**
 * @method static ConfigValue AUTO_SHUTDOWN()
 * @method static ConfigValue STOP_TIME()
 * @method static ConfigValue STOP_TIME_TIME()
 * @method static ConfigValue CATCH_PACKET()
 * @method static ConfigValue CATCH_CLIENT_PACKET()
 * @method static ConfigValue CATCH_SERVER_PACKET()
 * @method static ConfigValue IGNORE_PACKETS()
 * @method static ConfigValue VALUE_FORMAT()
 * @method static ConfigValue MOVEMENT_STATUS()
 * @method static ConfigValue MOVEMENT_STATUS_SHOW_XYZ()
 * @method static ConfigValue MOVEMENT_STATUS_SHOW_YAW()
 * @method static ConfigValue MOVEMENT_STATUS_SHOW_PITCH()
 * @method static ConfigValue MOVEMENT_STATUS_SHOW_WORLD()
 * @method static ConfigValue MOVEMENT_STATUS_SHOW_DIRECTION()
 * @method static ConfigValue BLOCKS_STATUS()
 * @method static ConfigValue BLOCKS_STATUS_MAX_RANGE()
 * @method static ConfigValue BLOCKS_STATUS_TARGET()
 * @method static ConfigValue BLOCKS_STATUS_UNDER()
 * @method static ConfigValue BLOCKS_STATUS_ABOVE()
 * @method static ConfigValue BLOCKS_STATUS_IN_WATER()
 * @method static ConfigValue BLOCKS_STATUS_IN_LAVA()
 * @method static ConfigValue COMMAND()
 * @method static ConfigValue COMMAND_CHECK_ITEM()
 * @method static ConfigValue COMMAND_KILL_ENTITY()
 * @method static ConfigValue COMMAND_SPAWN_ENTITY()
 * @method static ConfigValue COMMAND_TP_WORLD()
 * @method static ConfigValue COMMAND_TP_SPAWN()
 * @method static ConfigValue COMMAND_PLAY_SOUND()
 * @method static ConfigValue COMMAND_STOP_SOUND()
 * @method static ConfigValue COMMAND_CHECK_ITEM_NAME()
 * @method static ConfigValue COMMAND_KILL_ENTITY_NAME()
 * @method static ConfigValue COMMAND_SPAWN_ENTITY_NAME()
 * @method static ConfigValue COMMAND_TP_WORLD_NAME()
 * @method static ConfigValue COMMAND_TP_SPAWN_NAME()
 * @method static ConfigValue COMMAND_PLAY_SOUND_NAME()
 * @method static ConfigValue COMMAND_STOP_SOUND_NAME()
 * @method static ConfigValue COMMAND_CHECK_ITEM_ALIASES()
 * @method static ConfigValue COMMAND_KILL_ENTITY_ALIASES()
 * @method static ConfigValue COMMAND_SPAWN_ENTITY_ALIASES()
 * @method static ConfigValue COMMAND_TP_WORLD_ALIASES()
 * @method static ConfigValue COMMAND_TP_SPAWN_ALIASES()
 * @method static ConfigValue COMMAND_PLAY_SOUND_ALIASES()
 * @method static ConfigValue COMMAND_STOP_SOUND_ALIASES()
 */
class ConfigValue
{
    use EnumTrait {
        __construct as Enum___construct;
    }

    protected static function setup() : void
    {
        self::registerAll(
            new self("auto_shutdown", "auto-shutdown", false),

            new self("stop_time", "stop-time.enabled", false),
            new self("stop_time_time", "stop-time.time", 0),

            new self("catch_packet", "catch-packet.enabled", false),
            new self("catch_client_packet", "catch-packet.client", true),
            new self("catch_server_packet", "catch-packet.server", true),
            new self("ignore_packets", "catch-packet.ignores", []),

            new self("value_format", "value-format", "{%0} ({%1})"),

            new self("movement_status", "movement-status.enabled", false),
            new self("movement_status_show_xyz", "movement-status.show-xyz", true),
            new self("movement_status_show_yaw", "movement-status.show-yaw", true),
            new self("movement_status_show_pitch", "movement-status.show-pitch", true),
            new self("movement_status_show_world", "movement-status.show-world", true),
            new self("movement_status_show_direction", "movement-status.show-direction", true),

            new self("blocks_status", "blocks-status.enabled", false),
            new self("blocks_status_max_range", "blocks-status.max-range", 15),
            new self("blocks_status_target", "blocks-status.target", true),
            new self("blocks_status_under", "blocks-status.under", true),
            new self("blocks_status_above", "blocks-status.above", true),
            new self("blocks_status_in_water", "blocks-status.in_water", true),
            new self("blocks_status_in_lava", "blocks-status.in_lava", true),

            new self("command", "command.enabled", true),
            new self("command_check_item", "command.check-item.enabled", true),
            new self("command_kill_entity", "command.kill-entity.enabled", true),
            new self("command_spawn_entity", "command.spawn-entity.enabled", true),
            new self("command_tp_world", "command.teleport-to-world.enabled", true),
            new self("command_tp_spawn", "command.teleport-to-spawn.enabled", true),
            new self("command_play_sound", "command.play-sound.enabled", true),
            new self("command_stop_sound", "command.stop-sound.enabled", true),

            new self("command_check_item_name", "command.check-item.name", "checkitem"),
            new self("command_kill_entity_name", "command.kill-entity.name", "killentity"),
            new self("command_spawn_entity_name", "command.spawn-entity.name", "spawnentity"),
            new self("command_tp_world_name", "command.teleport-to-world.name", "tpworld"),
            new self("command_tp_spawn_name", "command.teleport-to-spawn.name", "tpspawn"),
            new self("command_play_sound_name", "command.play-sound.name", "playsound"),
            new self("command_stop_sound_name", "command.stop-sound.name", "stopsound"),

            new self("command_check_item_aliases", "command.check-item.aliases", []),
            new self("command_kill_entity_aliases", "command.kill-entity.aliases", []),
            new self("command_spawn_entity_aliases", "command.spawn-entity.aliases", []),
            new self("command_tp_world_aliases", "command.teleport-to-world.aliases", []),
            new self("command_tp_spawn_aliases", "command.teleport-to-spawn.aliases", []),
            new self("command_play_sound_aliases", "command.play-sound.aliases", []),
            new self("command_stop_sound_aliases", "command.stop-sound.aliases", []),
        );
    }

    /** @var string */
    private string $key;

    /** @var mixed */
    private mixed $default;

    private function __construct(string $enumName, string $key, mixed $default)
    {
        $this->Enum___construct($enumName);
        $this->key = $key;
        $this->default = $default;
    }

    /**
     * @return mixed
     */
    public function get() : mixed
    {
        return Auxiliary::getInstance()->getConfig()->getNested($this->key, $this->default);
    }
}