<?php

namespace iggyvolz\BinaryData\Definitions\String;

use Attribute;
use iggyvolz\BinaryData\Definitions\Definition;
use iggyvolz\BinaryData\Reader;
use iggyvolz\BinaryData\TestCase;
use iggyvolz\BinaryData\Writer;
use ReflectionParameter;
use TypeError;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
#[TestCase("Hello world\0", "Hello world")]
#[TestCase("\0", "")]
final class NullTerminatedString extends Definition
{
    public function read(ReflectionParameter $refl, Reader $input): string
    {
        $buf = "";
        while(($c = $input->read(1)) !== "\0") {
            $buf .= $c;
        }
        return $buf;
    }

    public function write(ReflectionParameter $refl, Writer $output, mixed $data): void
    {
        if(!is_string($data)) {
            throw new TypeError();
        }
        $output->write($data);
        $output->write("\0");
    }
}