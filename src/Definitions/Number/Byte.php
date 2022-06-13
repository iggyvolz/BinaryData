<?php

namespace iggyvolz\BinaryData\Definitions\Number;

use Attribute;
use iggyvolz\BinaryData\TestCase;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
#[TestCase("\x00", 0)]
#[TestCase("\x01", 1)]
#[TestCase("\xff", -1)]
#[TestCase("\xfe", -2)]
final class Byte extends AbstractInteger
{
    public function __construct()
    {
        parent::__construct(
            1,
            false, // does not matter
            true,
        );
    }
}