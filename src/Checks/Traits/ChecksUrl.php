<?php

namespace Backstage\Laravel\OK\Checks\Traits;

use Exception;
use Illuminate\Support\Facades\Http;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

class ChecksUrl extends Check
{
    protected ?string $url = null;

    protected ?string $failureMessage = null;

    protected int $timeout = 1;

    protected int $retryTimes = 1;

    protected string $method = 'GET';

    /** @var array<string, string> */
    protected array $headers = [];

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function timeout(int $seconds): self
    {
        $this->timeout = $seconds;

        return $this;
    }

    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function retryTimes(int $times): self
    {
        $this->retryTimes = $times;

        return $this;
    }

    /**
     * @param  array<string, string>  $headers
     * @return $this
     */
    public function headers(array $headers = []): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function failureMessage(string $failureMessage): self
    {
        $this->failureMessage = $failureMessage;

        return $this;
    }

    public function run(): Result
    {
        if (is_null($this->url)) {
            throw new Exception('URL not set');
        }

        // try {
        $request = Http::timeout($this->timeout)
            ->withHeaders($this->headers)
            ->retry($this->retryTimes)
            ->send($this->method, $this->url);

        var_dump($request->successful());
        if (! $request->successful()) {
            // return $this->failedResult();
        }
        // } catch (Exception) {
        //     return $this->failedResult();
        // }

        return Result::new()
            ->ok();
    }

    protected function failedResult(): Result
    {
        return Result::new()
            ->failed()
            ->message($this->failureMessage ?? "Pinging {$this->getName()} failed.");
    }
}
