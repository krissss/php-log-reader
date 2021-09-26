<?php

namespace Kriss\LogReader\Objects;

use Symfony\Component\Finder\SplFileInfo;

class DirObject
{
    protected $file;
    protected $key;

    public function __construct(SplFileInfo $file, string $key)
    {
        $this->file = $file;
        $this->key = $key;
    }

    public function getName(): string
    {
        return $this->key;
    }

    public function getKey(): string
    {
        return $this->key;
    }
}
