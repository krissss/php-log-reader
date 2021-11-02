<?php

namespace Kriss\LogReader;

use Kriss\LogReader\Objects\DirObject;
use Kriss\LogReader\Objects\FileObject;
use Symfony\Component\Finder\Finder;

class LogReader
{
    /**
     * 日志根路径
     * @var string
     */
    private $logPath;
    /**
     * 是否启用
     * @var bool
     */
    private $enable = false;
    /**
     * 是否允许删除
     * @var bool
     */
    private $deleteEnable = false;
    /**
     * 日志文件后缀
     * @var string[]
     */
    private $logExtensions = ['*.log'];
    /**
     * 最大直接访问的单个文件大小
     * @var int
     */
    private $viewMaxSize = 10240;
    /**
     * tail 查看时默认读取的行大小
     * @var int
     */
    private $tailDefaultLine = 1000;
    /**
     * list 视图的文件，default 为默认
     * @var string
     */
    private $listViewFile = 'default';
    /**
     * bootstrap.css url
     * @var string
     */
    private $bootstrapV3CssUrl = 'https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css';

    public function __construct(string $logPath, $config = [])
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }

        if (!$this->enable) {
            return;
        }
        $this->logPath = $this->normalizePath($logPath);
    }

    /**
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * @return bool
     */
    public function isDeleteEnable(): bool
    {
        return $this->deleteEnable;
    }

    /**
     * @return int
     */
    public function getViewMaxSize(): int
    {
        return $this->viewMaxSize;
    }

    /**
     * @return int
     */
    public function getTailDefaultLine(): int
    {
        return $this->tailDefaultLine;
    }

    /**
     * @return string
     */
    public function getListViewFile(): string
    {
        return $this->listViewFile;
    }

    /**
     * @return string
     */
    public function getBootstrapV3CssUrl(): string
    {
        return $this->bootstrapV3CssUrl;
    }

    /**
     * 读目录
     * @param string $path
     * @return array{dirs: array, files: array}
     */
    public function readDir(string $path): array
    {
        $finder = (new Finder())->ignoreUnreadableDirs()->in($path)->depth(0);
        $data = [
            'dirs' => [],
            'files' => [],
        ];
        foreach ((clone $finder)->sortByName()->directories() as $directory) {
            $data['dirs'][] = new DirObject($directory, $this->getRelativePath($directory->getRealPath()));
        }

        // for finder > 4.4 use: (clone $finder)->sortByModifiedTime()->reverseSorting()->files()->name($this->logExtensions)
        $finder = (clone $finder)->sortByModifiedTime()->files();
        foreach ($this->logExtensions as $extension) {
            $finder->name($extension);
        }
        foreach ($finder as $file) {
            $data['files'][] = new FileObject($file, $this->getRelativePath($file->getRealPath()));
        }
        $data['files'] = array_reverse($data['files']);

        return $data;
    }

    /**
     * 获取全路径
     * @param string $path
     * @return string
     */
    public function getFullPath(string $path): string
    {
        return $this->normalizePath($this->logPath . '/' . ltrim($path, '/'));
    }

    /**
     * 获取相对日志根目录的路径
     * @param string $path
     * @return string
     */
    public function getRelativePath(string $path): string
    {
        return ltrim(str_replace($this->logPath, '', $this->normalizePath($path)), '/');
    }

    /**
     * 格式化路径
     * @param string $path
     * @return string
     */
    protected function normalizePath(string $path): string
    {
        $isWindowsShare = strpos($path, '\\\\') === 0;

        if ($isWindowsShare) {
            $path = substr($path, 2);
        }

        $path = rtrim(strtr($path, '/\\', '//'), '/');

        if (strpos('/' . $path, '/.') === false && strpos($path, '//') === false) {
            return $isWindowsShare ? "\\\\$path" : $path;
        }

        $parts = [];

        foreach (explode('/', $path) as $part) {
            if ($part === '..' && !empty($parts) && end($parts) !== '..') {
                array_pop($parts);
            } elseif ($part !== '.' && ($part !== '' || empty($parts))) {
                $parts[] = $part;
            }
        }

        $path = implode('/', $parts);

        if ($isWindowsShare) {
            $path = '\\\\' . $path;
        }

        return $path === '' ? '.' : $path;
    }
}
