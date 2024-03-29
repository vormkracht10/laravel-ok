<?php

namespace Vormkracht10\LaravelOK\Checks\Traits;

use Exception;
use Illuminate\Support\Facades\Http;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Exceptions\InvalidCheck;

class RendersWebPage extends Check
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

    /**
     * @throws InvalidCheck
     */
    public function run(): Result
    {
        if (is_null($this->url)) {
            throw InvalidCheck::urlNotSet();
        }

        try {
            $request = Http::timeout($this->timeout)
                ->withHeaders($this->headers)
                ->retry($this->retryTimes)
                ->send($this->method, $this->url);

            if (! $request->successful()) {
                return $this->failedResult();
            }
        } catch (Exception) {
            return $this->failedResult();
        }

        return Result::new()
            ->ok('Reachable');
    }

    protected function failedResult(): Result
    {
        return Result::new()
            ->failed($this->failureMessage ?? "Pinging {$this->getName()} failed.")
            ->summary('Unreachable');
    }
}
