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
  # Current player's motion
  show-motion: true
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
# e.g. /killentity ?
command:
  enabled: true
  # Add the feature to check item in hand. (e.g. "/checkitem")
  # Usage /checkitem
  # e.g. /checkitem
  check-item:
    # Validation of this command
    enabled: true
    # Command name.
    name: "checkitem"
    # Command aliases.
    aliases: ["ci", "id"]
  # Add the feature to kill entities in the world. (e.g. "/killentity <id|typeName>[,...<id|typeName>]")
  # Usage /killentity
  #       /killentity <id|typeName>[,...<id|typeName>]
  # e.g. /killentity 0
  #      /killentity zombie
  #      /killentity zombie,villager,0,1
  kill-entity:
    enabled: true
    name: "killentity"
    aliases: ["kille", "ke"]
  # Add the feature to teleport between worlds
  # Usage /tpworld <worldName> [player]
  # e.g. /tpworld lobby
  #      /tpworld lobby steve
  teleport-to-world:
    enabled: true
    name: "tpworld"
    aliases: ["tptw", "tw"]
  # Add the feature to teleport to spawn point of the world
  # Usage /tpspawn [player]
  # e.g. /tpspawn
  #      /tpspawn steve
  teleport-to-spawn:
    enabled: true
    name: "tpspawn"
    aliases: ["spawn", "tpts"]
  # Add the feature to play/stop sound effect
  # Usage /playsound <soundName> [volume] [pitch]
  # e.g. /playsound note.guitar 1 1
  play-sound:
    enabled: true
    name: "playsound"
    aliases: ["psound", "ps"]
  # Add the feature to stop sound effect
  # Usage /stopsound <soundName>
  # e.g. /playsound note.guitar
  stop-sound:
    enabled: true
    name: "stopsound"
    aliases: ["ssound", "ss"]
```
