<?php

namespace iggyvolz\BinaryData;

final class StringWriter implements Writer
{
    private(set) string $data = "";

    public function write(string $bytes): void
    {
        $this->data .= $bytes;
    }
}