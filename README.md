# Log Reader For PHP

![preview](https://github.com/krissss/php-log-reader/raw/master/preview.png)

# Installation

```bash
composer require kriss/log-reader
```

# Usage

## Use LogReaderControllerTrait in controller

```php
use Kriss\LogReader\Traits\LogReaderControllerTrait;
use Kriss\LogReader\LogReader;

class SomeController {
    use LogReaderControllerTrait;
    
    private $logReader;
    
    protected function getLogReader(): LogReader
    {
        if (!$this->logReader) {
            //$runtimePath = __DIR__ . '/../runtime/logs';
            $this->logReader = new LogReader($runtimePath, [
                'enable' => true,
                'deleteEnable' => true,
                // others
            ]);
        }
        return $this->logReader;
    }

    protected function getRequest(): SymfonyRequest
    {
        return request()->getSymfonyRequest();
    }

    protected function getBaseUrl(): string
    {
        return 'some/url-prefix';
    }
}
```

# For Laravel

[see example](./example/laravel)
