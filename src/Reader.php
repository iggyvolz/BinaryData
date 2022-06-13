<?php

namespace iggyvolz\BinaryData;

interface Reader
{
    public function read(int $bytes): string;
}