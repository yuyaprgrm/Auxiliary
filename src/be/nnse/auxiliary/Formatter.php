<?php

declare(strict_types=1);

namespace be\nnse\auxiliary;

use pocketmine\block\Block;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;

class Formatter
{
    use SingletonTrait;

    /**
     * One-To-One value format
     * @param string $title
     * @param string $value
     * @return string
     */
    public function oto2Str(string $title, mixed $value) : string
    {
        $format = (string) ConfigValue::FORMAT_ONE_TO_ONE()->get();
        $replaced = str_replace(["{%title}", "{%value}"], [$title, (string) $value], $format);
        return TextFormat::colorize($replaced);
    }

    /**
     * X, Y, Z value format
     * @param string $title
     * @param string $x
     * @param string $y
     * @param string $z
     * @return string
     */
    public function xyz2Str(string $title, string $x, string $y, string $z) : string
    {
        $format = (string) ConfigValue::FORMAT_XYZ_VALUE()->get();
        $replaced = str_replace(
            ["{%title}", "{%x}", "{%y}", "{%z}"],
            [$title, $x, $y, $z],
            $format
        );
        return TextFormat::colorize($replaced);
    }

    /**
     * Block value format
     * @param string $title
     * @param Block $block
     * @return string
     */
    public function block2Str(string $title, Block $block) : string
    {
        $id = $block->getIdInfo()->getBlockId();
        $meta = $block->getMeta();
        $name = $block->getName();
        $typeId = $block->getTypeId();
        $replaced = str_replace(
            ["{%title}", "{%id}", "{%meta}", "{%name}", "{%typeId}"],
            [$title, (string) $id, (string) $meta, $name, (string) $typeId],
            (string) ConfigValue::FORMAT_BLOCK_VALUE()->get()
        );
        return TextFormat::colorize($replaced);
    }

    /**
     * Boolean value format
     * @param string $title
     * @param bool $value
     * @return string
     */
    public function bool2Str(string $title, bool $value) : string
    {
        $format = $value ? ConfigValue::FORMAT_BOOLEAN_VALUE_TRUE()->get() : ConfigValue::FORMAT_BOOLEAN_VALUE_FALSE()->get();
        return TextFormat::colorize(str_replace(["{%title}"], [$title], (string) $format));
    }
}