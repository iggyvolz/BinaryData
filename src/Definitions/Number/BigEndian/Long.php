<?php

namespace iggyvolz\BinaryData\Definitions\Number\BigEndian;

use Attribute;
use iggyvolz\BinaryData\Definitions\Number\AbstractInteger;
use iggyvolz\BinaryData\TestCase;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
#[TestCase("\x00\x00\x00\x00\x00\x00\x00\x00", 0)]
#[TestCase("\x00\x00\x00\x00\x00\x00\x00\x01", 1)]
#[TestCase("\xff\xff\xff\xff\xff\xff\xff\xff", -1)]
#[TestCase("\xff\xff\xff\xff\xff\xff\xff\xfe", -2)]
#[TestCase("\x80\x00\x00\x00\x00\x00\x00\x00", PHP_INT_MIN)]
#[TestCase("\x7f\xff\xff\xff\xff\xff\xff\xff", PHP_INT_MAX)]
final class Long extends AbstractInteger
{
    public function __construct()
    {
        parent::__construct(
            8,
            true,
            true,
        );
    }
}