<?php

namespace Vormkracht10\LaravelOK\Checks\Traits;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Spatie\Health\Exceptions\InvalidCheck;
use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class ChecksForElementOnPage extends Check
{
    protected ?string $failureMessage = null;

    protected ?string $url = null;

    protected ?string $element = null;

    protected ?string $attribute = null;

    protected ?string $text = null;

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

        $element = $crawler->filter($element);

        if ($this->attribute) {
            $element = $crawler->filterXPath("//{$element}")->extract([$this->attribute]);
        }

        if (! $element) {
            return false;
        }

        if (is_null($this->text) && is_null($this->attribute)) {
            return true;
        }

        if (! is_null($this->text) && is_null($this->attribute)) {
            foreach ($element as $e) {
                if ($e->textContent == $this->text) {
                    return true;
                }
            }
        }

        // Check if attribute has text
        if (! is_null($this->attribute) && ! is_null($this->text)) {
            foreach ($element as $e) {
                dd($e, $e->attr($this->attribute), $this->text);
                if ($e->attr($this->attribute) == $this->text) {
                    return true;
                }
            }
        }

        return false;
    }

    /** @throws InvalidCheck */
    public function getUrlResponse(): Response
    {
        if (is_null($this->url)) {
            throw InvalidCheck::urlNotSet();
        }

        try {
            $request = Http::timeout($this->timeout)
                ->withHeaders($this->headers)
                ->retry($this->retryTimes)
                ->send('GET', $this->url);

            if (! $request->successful()) {
                return $this->failedResult();
            }
        } catch (Exception $exception) {
            throw $exception;
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
            ->failed()
            ->shortSummary('Unreachable')
            ->notificationMessage($this->failureMessage ?? "Pinging {$this->getName()} failed.");
    }
}
