<?php

namespace iggyvolz\BinaryData\Definitions\Number\BigEndian;

use Attribute;
use iggyvolz\BinaryData\Definitions\Number\AbstractInteger;
use iggyvolz\BinaryData\TestCase;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
#[TestCase("\x00\x00", 0)]
#[TestCase("\x00\x01", 1)]
#[TestCase("\xff\xff", 65535)]
#[TestCase("\xff\xfe", 65534)]
final class UShort extends AbstractInteger
{
    public function __construct()
    {
        parent::__construct(
            2,
            true,
            false,
        );
    }
}