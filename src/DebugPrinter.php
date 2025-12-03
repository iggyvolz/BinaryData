<?php

namespace iggyvolz\BinaryData;

/**
 * @template T
 * @template U
 */
interface DebugPrinter
{
    /**
     * @param T $data
     * @return U
     */
    public function handle(mixed $data): mixed;
}