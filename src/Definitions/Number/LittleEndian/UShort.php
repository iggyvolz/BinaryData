<?php

namespace iggyvolz\BinaryData\Definitions\Number\LittleEndian;

use Attribute;
use iggyvolz\BinaryData\Definitions\Number\AbstractInteger;
use iggyvolz\BinaryData\TestCase;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
#[TestCase("\x00\x00", 0)]
#[TestCase("\x01\x00", 1)]
#[TestCase("\xff\xff", (1 << 16) - 1)]
#[TestCase("\xfe\xff", (1 << 16) - 2)]
final class UShort extends AbstractInteger
{
    public function __construct()
    {
        parent::__construct(
            2,
            false,
            false,
        );
    }
}