# Auxiliary
Plugin which assist to make plugin for PocketMine-MP

```yaml
# Whether to close the server if there are no players
auto-shutdown: false


# Whether to stop the time
stop-time:
  enabled: false
  # The time to stop
  time: 0


# Whether to catch a packet name
catch-packet:
  enabled: false
  # target packet type: ClientBoundPacket
  client: true
  # target packet type: ServerBoundPacket
  server: true
  # Ignore packet names (e.g. ["PlayerAuthInputPacket"])
  ignores: ["PlayerAuthInputPacket"]


# Value text format
# {%0}: title
# {%1}: value
value-format: "{%0} ({%1})"


# Whether to show the status about location and motion.
movement-status:
  enabled: false
  # Player's positions
  show-xyz: true
  # Player's yaw
  show-yaw: true
  # Player's pitch
  show-pitch: true
  # World name in player now
  show-world: true
  # Player's direction
  show-direction: true


# Whether to show the status about blocks
blocks-status:
  enabled: false
  # The maximum distance of the target block
  max-range: 20
  # Target block
  target: true
  # Under block
  under: true
  # Above block
  above: true
  # Whether you are underwater or not
  in_water: true
  # Whether you are under-lava or not
  in_lava: true


# Whether to be able to use those commands and command detail settings
# The first argument is "?" to check the usage of the command
# NOTE: THESE COMMAND CANNOT RUN FROM CONSOLE!
# e.g. /killentity ?
command:
  enabled: true
  # Add the feature to check item in hand
  # Usage /checkitem
  # e.g. /checkitem
  check-item:
    # Validation of this command
    enabled: true
    # Command name.
    name: "checkitem"
    # Command aliases.
    aliases: ["ci", "id"]
    # Default state of the permission. Explanation of each value:
    #  op (isop, operator, isoperator, admin, isadmin): only op players
    #  notop (!op, notoperator, !operator, notadmin, !admin): only not-op players
    #  true: everyone
    #  false: no one
    permission: "op"
  # Add the feature to kill entities in the world
  # Usage /killentity
  #       /killentity <id|typeName>[,...<id|typeName>]
  # e.g. /killentity 0
  #      /killentity zombie
  #      /killentity zombie,villager,0,1
  kill-entity:
    enabled: true
    name: "killentity"
    aliases: ["kille", "ke"]
    permission: "op"
  # Add the feature to spawn entity in the world
  # Usage /spawnentity <typeName> [nameTag]
  # e.g. /spawnentity Villager
  spawn-entity:
    enabled: true
    name: "spawnentity"
    aliases: [ "spawne", "summon", "se" ]
    permission: "op"
  # Add the feature to teleport between worlds
  # Usage /tpworld <worldName> [player]
  # e.g. /tpworld lobby
  #      /tpworld lobby steve
  teleport-to-world:
    enabled: true
    name: "tpworld"
    aliases: ["tptw", "tw"]
    permission: "op"
  # Add the feature to teleport to spawn point of the world
  # Usage /tpspawn [player]
  # e.g. /tpspawn
  #      /tpspawn steve
  teleport-to-spawn:
    enabled: true
    name: "tpspawn"
    aliases: ["spawn", "tpts"]
    permission: "op"
  # Add the feature to play/stop sound effect
  # Usage /playsound <soundName> [volume] [pitch]
  # e.g. /playsound note.guitar 1 1
  play-sound:
    enabled: true
    name: "playsound"
    aliases: ["psound", "ps"]
    permission: "op"
  # Add the feature to stop sound effect
  # Usage /stopsound <soundName>
  # e.g. /stopsound note.guitar
  stop-sound:
    enabled: true
    name: "stopsound"
    aliases: ["ssound", "ss"]
    permission: "op"
  # Set target player's metadata flag
  # Usage /setplayerflag <flag> <value> [player]
  # e.g. /setplayerflag 0 true
  #      /setplayerflag ONFIRE true
  #      /setplayerflag sneaking true
  set-player-flag:
    enabled: true
    name: "setplayerflag"
    aliases: ["playerflag", "pf"]
    permission: "op"
  # Set target player's metadata property
  # If it is not set, add it with any type
  # Property: https://github.com/pmmp/BedrockProtocol/blob/master/src/types/entity/EntityMetadataProperties.php
  # Value example per type:
  #   string: steve
  #   int: 10
  #   byte: 10b
  #   long: 10l
  #   short: 10s
  #   float: 10f
  #   blockpos: b#10,10,10
  #   vector3f: v#10,10,10
  # Usage /setplayerproperty <property> <value> [player]
  # e.g. /setplayerproperty 4 Steve
  #      /setplayerproperty NAMETAG Steve
  #      /setplayerproperty 38 2f
  #      /setplayerproperty scale 2f
  #      /setplayerproperty PLAYER_BED_POSITION b#10,10,10
  #      /setplayerproperty RIDER_SEAT_POSITION v#10,10,10
  set-player-property:
    enabled: true
    name: "setplayerproperty"
    aliases: ["playerproperty", "pp"]
    permission: "op"
```
