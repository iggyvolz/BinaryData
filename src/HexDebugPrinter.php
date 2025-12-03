<?php

namespace iggyvolz\BinaryData;

use iggyvolz\BinaryData\DebugPrinter;

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