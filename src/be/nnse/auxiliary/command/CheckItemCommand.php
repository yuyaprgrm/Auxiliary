<?php

declare(strict_types=1);

namespace be\nnse\auxiliary\command;

use be\nnse\auxiliary\Auxiliary;
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
            $id = $item->getId();
            $meta = $item->getMeta();
            $name = $item->getName();
            $canDestroy = $item->getCanDestroy();
            $canPlaceOn = $item->getCanPlaceOn();

            $sender->sendMessage(Auxiliary::getInstance()->formatText($id.":".$meta, $name));
            if (!empty($canDestroy)) {
                $blockIds = Auxiliary::getInstance()->formatText("CanDestroy", implode(", ", $canDestroy));
                $sender->sendMessage($blockIds);
            }
            if (!empty($canPlaceOn)) {
                $blockIds = Auxiliary::getInstance()->formatText("CanPlaceOn", implode(", ", $canPlaceOn));
                $sender->sendMessage($blockIds);
            }

            $nbt = $item->getNamedTag();
            foreach ($nbt->getValue() as $key => $tag) {
                $value = $tag->getValue();
                if (is_array($value)) {
                    $tagData = Auxiliary::getInstance()->formatText((string) $key, implode(", ", $tag->getValue()));
                } else {
                    $tagData = Auxiliary::getInstance()->formatText((string) $key, $tag->getValue());
                }
                $sender->sendMessage($tagData);
            }

            WrapperCommand::broadcastCommandMessage($sender, "Checked an item in hand", false);
        }
        return null;
    }
}