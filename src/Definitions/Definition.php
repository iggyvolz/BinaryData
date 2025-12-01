<?php

namespace iggyvolz\BinaryData\Definitions;

use iggyvolz\BinaryData\Reader;
use iggyvolz\BinaryData\Writer;
use ReflectionParameter;

/** @template T */
abstract class Definition
{
    /**
     * @var int|null The size of the object, if fixed
     */
    public ?int $size { get => null; }
    /** @param array $args *@return T */
    public abstract function read(ReflectionParameter $refl, Reader $input, array $args): mixed;
    /** @param T $data */
    public abstract function write(ReflectionParameter $refl, Writer $output, mixed $data): void;
}