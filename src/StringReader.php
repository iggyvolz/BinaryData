<?php

namespace iggyvolz\BinaryData;

final class StringReader implements Reader
{

    /**
     * @param string $data
     */
    public function __construct(private string $data)
    {
    }

    public function read(int $bytes): string
    {
        $ret = substr($this->data, 0, $bytes);
        $this->data = substr($this->data, $bytes);
        return $ret;
    }

    public bool $done { get => $this->data === ""; }
}