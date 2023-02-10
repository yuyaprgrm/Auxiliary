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

namespace be\nnse\auxiliary\command;

use be\nnse\auxiliary\Formatter;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class CheckItemCommand extends WrapperCommand
{
    public function __construct(string $name, array $aliases = [], string $default = "op")
    {
        parent::__construct($name, "Check an item in hand", $aliases, $default);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : mixed
    {
        if (!parent::execute($sender, $commandLabel, $args)) return null;

        if ($sender instanceof Player) {
            $item = $sender->getInventory()->getItemInHand();
            $itemText = $item->getId() . ":" . $item->getMeta() . " (" . $item->getName() . ")";
            $canDestroy = $item->getCanDestroy();
            $canPlaceOn = $item->getCanPlaceOn();

            $sender->sendMessage(Formatter::getInstance()->oto2Str("ItemInHand", $itemText));
            if (!empty($canDestroy)) {
                $sender->sendMessage(Formatter::getInstance()->oto2Str("CanDestroy", implode(", ", $canDestroy)));
            }
            if (!empty($canPlaceOn)) {
                $sender->sendMessage(Formatter::getInstance()->oto2Str("CanPlaceOn", implode(", ", $canPlaceOn)));
            }

            $nbt = $item->getNamedTag();
            foreach ($nbt->getValue() as $key => $tag) {
                $value = $tag->getValue();
                if (is_array($value)) {
                    $tagData = Formatter::getInstance()->oto2Str((string) $key, implode(", ", $tag->getValue()));
                } else {
                    $tagData = Formatter::getInstance()->oto2Str((string) $key, $tag->getValue());
                }
                $sender->sendMessage($tagData);
            }

            WrapperCommand::broadcastCommandMessage($sender, "Checked an item in hand", false);
        }
        return null;
    }
}