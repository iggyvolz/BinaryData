<?php

namespace iggyvolz\BinaryData\Definitions;

use iggyvolz\BinaryData\Reader;
use iggyvolz\BinaryData\Writer;

/** @template T */
abstract class Definition
{
    /** @return T */
    public abstract function read(Reader $input): mixed;
    /** @param T $data */
    public abstract function write(Writer $output, mixed $data): void;
}