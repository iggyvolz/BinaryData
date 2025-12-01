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
#[TestCase("\x01x", "x", constructorArgs: [Byte::class])]
class LengthPrefixedString extends Definition
{
    public readonly Definition $prefix;
    /**
     * @param class-string<Definition>|Definition $prefix
     */
    public function __construct(string|Definition $prefix)
    {
        if(is_string($prefix)) {
            $prefix = new $prefix();
        }
        $this->prefix = $prefix;
    }

    public function read(ReflectionParameter $refl, Reader $input, array $args): string
    {
        $length = $this->prefix->read($refl, $input, $args);
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