<?php

namespace iggyvolz\BinaryData\Definitions\String;

use Attribute;
use iggyvolz\BinaryData\Definitions\Definition;
use iggyvolz\BinaryData\Definitions\Number\Byte;
use iggyvolz\BinaryData\Reader;
use iggyvolz\BinaryData\TestCase;
use iggyvolz\BinaryData\Writer;
use ReflectionParameter;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
#[TestCase("\x01x", "x", constructorArgs: [new Byte])]
class LengthPrefixedString extends Definition
{
    public function __construct(public readonly Definition $prefix)
    {
    }

    public function read(ReflectionParameter $refl, Reader $input): string
    {
        $length = $this->prefix->read($refl, $input);
        return $input->read($length);
    }

    public function write(ReflectionParameter $refl, Writer $output, mixed $data): void
    {
        if(!is_string($data)) {
            throw new \TypeError();
        }
        $this->prefix->write($refl, $output, strlen($data));
        $output->write($data);
    }
}