<?php

namespace Kriss\LogReader\Objects;

use Carbon\Carbon;
use Symfony\Component\Finder\SplFileInfo;

class FileObject
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

    public function getModifyAtForHumans(): string
    {
        $carbon = Carbon::createFromTimestamp($this->file->getMTime());
        return "<span title='{$carbon->format('Y-m-d H:i:s')}'>{$carbon->diffForHumans()}</span>";
    }

    public function getSizeKB(): int
    {
        return intval($this->file->getSize() / 1024);
    }

    public function getSizeForHumans(): string
    {
        $units = ['KB', 'MB', 'GB'];
        $size = $this->file->getSize();
        foreach ($units as $unit) {
            $size = $size / 1024;
            if ($size < 1024) {
                return round($size, 2) . $unit;
            }
        }
        return round($size, 2) . end($units);
    }
}
