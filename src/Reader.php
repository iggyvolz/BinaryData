<?php

namespace iggyvolz\BinaryData;

interface Reader
{
    public function read(int $bytes): string;
    public int $tell {get;}
}