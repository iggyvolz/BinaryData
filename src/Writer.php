<?php

namespace iggyvolz\BinaryData;

interface Writer
{
    public function write(string $bytes): void;
    public int $tell {get;}
}