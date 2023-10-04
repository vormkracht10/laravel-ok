<?php

namespace Vormkracht10\LaravelOK\Checks\Traits;

use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;
use Vormkracht10\LaravelOK\Exceptions\InvalidCheck;

class ChecksForElementOnPage extends Check
{
    protected ?string $failureMessage = null;

    protected ?string $url = null;

    protected ?string $element = null;

    protected ?string $attribute = null;

    protected ?string $text = null;

    protected ?string $containsText = null;

    protected int $timeout = 1;

    protected int $retryTimes = 1;

    /** @var array<string, string> */
    protected array $headers = [];

    public function url(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function element(string $element): self
    {
        $this->element = $element;

        return $this;
    }

    public function containsText(string $text): self
    {
        $this->containsText = $text;

        return $this;
    }

    public function text(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function attribute(string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    /** @param  array<string, string>  $headers */
    public function headers(array $headers = []): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function checkExpectedElement(Response $response, string $element)
    {
        $crawler = new Crawler($response->body());

        if ($this->attribute) {
            $element = $crawler->filterXPath("//{$element}[@{$this->attribute}]");
        } else {
            $element = $crawler->filter($element);
        }

        if (is_null($this->text) && is_null($this->attribute)) {
            return true;
        }

        if (! is_null($this->text)) {
            foreach ($element as $e) {
                if ($e->textContent == $this->text) {
                    return true;
                }
            }
        }

        if (! is_null($this->containsText)) {
            foreach ($element as $e) {
                if (str_contains($e->textContent, $this->containsText)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @throws InvalidCheck
     * @throws Exception
     */
    public function getUrlResponse(): PromiseInterface|Response|Result
    {
        if (is_null($this->url)) {
            throw InvalidCheck::urlNotSet();
        }

        $request = Http::timeout($this->timeout)
            ->withHeaders($this->headers)
            ->retry($this->retryTimes)
            ->send('GET', $this->url);

        if (! $request->successful()) {
            return $this->failedResult();
        }

        return $request;
    }

    public function run(): Result
    {
        $result = Result::new();

        return $this->checkExpectedElement($this->getUrlResponse(), $this->element)
            ? $result->ok()
            : $result->failed("The element '{$this->element}' could not be found on the page '{$this->url}'");
    }

    protected function failedResult(): Result
    {
        return Result::new()
            ->failed($this->failureMessage ?? "Pinging {$this->getName()} failed.")
            ->summary('Unreachable');
    }
}
