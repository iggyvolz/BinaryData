<?php

namespace iggyvolz\BinaryData;

interface Writer
{
    public function write(string $bytes): void;
}