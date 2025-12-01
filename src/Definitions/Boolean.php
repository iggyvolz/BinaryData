<?php

namespace iggyvolz\BinaryData\Definitions;

use Attribute;
use iggyvolz\BinaryData\Reader;
use iggyvolz\BinaryData\TestCase;
use iggyvolz\BinaryData\Writer;
use ReflectionParameter;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
#[TestCase("\x00", false)]
#[TestCase("\x01", true)]
#[TestCase("\x02", true, true)]
final class Boolean extends Definition
{
    public function read(ReflectionParameter $refl, Reader $input, array $args): bool
    {
        return $input->read(1) !== "\x00";
    }

    public function write(ReflectionParameter $refl, Writer $output, mixed $data): void
    {
        if(!is_bool($data)) throw new \TypeError();
        $output->write($data ? "\x01": "\x00");
    }
}