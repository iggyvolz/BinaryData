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
        $this->tell += strlen($ret);
        return $ret;
    }

    public bool $done { get => $this->data === ""; }
    private(set) int $tell = 0;
}