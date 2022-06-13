<?php

namespace iggyvolz\BinaryData\Definitions\Number\LittleEndian;

use Attribute;
use iggyvolz\BinaryData\Definitions\Number\AbstractInteger;
use iggyvolz\BinaryData\TestCase;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
#[TestCase("\x00\x00\x00\x00\x00\x00\x00\x00", 0)]
#[TestCase("\x01\x00\x00\x00\x00\x00\x00\x00", 1)]
#[TestCase("\xff\xff\xff\xff\xff\xff\xff\xff", -1)]
#[TestCase("\xfe\xff\xff\xff\xff\xff\xff\xff", -2)]
#[TestCase("\x00\x00\x00\x00\x00\x00\x00\x80", PHP_INT_MIN)]
#[TestCase("\xff\xff\xff\xff\xff\xff\xff\x7f", PHP_INT_MAX)]
final class Long extends AbstractInteger
{
    public function __construct()
    {
        parent::__construct(
            8,
            false,
            true,
        );
    }
}