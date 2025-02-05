<?php

namespace Kriss\LogReader\Traits;

use Kriss\LogReader\Objects\FileObject;
use Kriss\LogReader\LogReader;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

trait LogReaderControllerTrait
{
    public function index()
    {
        $this->checkIsEnable();
        $path = $this->getPathFromRequest();
        $data = $this->getLogReader()->readDir($path);

        $view = $this->getLogReader()->getListViewFile();
        if ($view === 'default') {
            $view = __DIR__ . '/../view/list.php';
        }
        return $this->renderPhpTemplate($view, [
            'dirs' => $data['dirs'],
            'files' => $data['files'],
            'urlBuilder' => function ($action, $params = []) {
                return $this->getUrl($action, $params);
            },
            'canDelete' => $this->getLogReader()->isDeleteEnable(),
            'viewMaxSize' => $this->getLogReader()->getViewMaxSize(),
            'bootstrapCssUrl' => $this->getLogReader()->getBootstrapV3CssUrl(),
            'tailLine' => $this->getLogReader()->getTailDefaultLine(),
        ]);
    }

    public function view()
    {
        $this->checkIsEnable();
        $file = $this->getPathFromRequest();
        $fileDTO = new FileObject(new SplFileInfo($file, '', ''), $file);
        if ($fileDTO->getSizeKB() > $this->getLogReader()->getViewMaxSize()) {
            return 'file is too large, use tail or download';
        }

        return new Response(file_get_contents($file), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function tail()
    {
        $this->checkIsEnable();
        $file = $this->getPathFromRequest();
        $line = (int)$this->getRequest()->get('line', $this->getLogReader()->getTailDefaultLine());
        $result = shell_exec("tail -n {$line} {$file}");

        return new Response($result, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    public function download()
    {
        $this->checkIsEnable();
        $file = $this->getPathFromRequest();

        return new BinaryFileResponse($file, 200, [], true, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }

    public function delete()
    {
        $this->checkIsEnable();
        if (!$this->getLogReader()->isDeleteEnable()) {
            return 'can not delete';
        }
        $file = $this->getPathFromRequest();
        @unlink($file);

        return new RedirectResponse($this->getRequest()->headers->get('referer'));
    }

    protected function checkIsEnable()
    {
        if (!$this->getLogReader()->isEnable()) {
            throw new \LogicException('log-reader is not enable');
        }
    }

    protected function getPathFromRequest(): string
    {
        $file = $this->getRequest()->get('key', '');

        $filePath = $this->getLogReader()->getFullPath($file);
        if (!file_exists($filePath)) {
            throw new \Exception('file not exist: ' . $file);
        }

        return $filePath;
    }

    abstract protected function getLogReader(): LogReader;

    abstract protected function getRequest(): Request;

    abstract protected function getBaseUrl(): string;

    protected function getUrl(string $action, $params = []): string
    {
        $url = rtrim($this->getBaseUrl(), '/') . '/' . ltrim($action, '/');
        if ($params) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
        }
        return $url;
    }

    protected function renderPhpTemplate($_file_, $_params_ = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require $_file_;
            return ob_get_clean();
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }
}
