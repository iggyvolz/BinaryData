<?php

namespace iggyvolz\BinaryData;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER | Attribute::TARGET_PROPERTY)]
/**
 * @template-extends DebugPrinter<string,string>
 */
class HexDebugPrinter implements DebugPrinter
{

    public function handle(mixed $data): string
    {
        return implode(" ", str_split(bin2hex($data), 2));
    }
}