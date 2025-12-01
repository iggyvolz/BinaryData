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
#[TestCase("hello", "hello", constructorArgs: [5])]
class FixedLengthString extends Definition
{
    public function __construct(public int $length)
    {
    }

    public function read(ReflectionParameter $refl, Reader $input, array $args): string
    {
        return $input->read($this->length);
    }

    public function write(ReflectionParameter $refl, Writer $output, mixed $data): void
    {
        if(!is_string($data)) {
            throw new \TypeError();
        }
        if(strlen($data) !== $this->length) {
            throw new \RuntimeException("Invalid data length");
        }
        $output->write($data);
    }
}