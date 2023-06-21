<?php

namespace Vormkracht10\LaravelOK\Checks\Traits;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Spatie\Health\Exceptions\InvalidCheck;
use Vormkracht10\LaravelOK\Checks\Base\Result;

trait ChecksForElementOnPage
{
    protected ?string $url = null;

    /** @var array<string, string> */
    protected array $headers = [];

    public function url(string $url): self
    {
        $this->url = $url;

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

        $elementIsPresent = $crawler->filter($element)->count() > 0;

        if (! $elementIsPresent) {
            return false;
        }

        return true;
    }

    /** @throws InvalidCheck */
    public function getUrlResponse(): Response
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->send('GET', $this->url);

            if (! $response->ok()) {
                throw InvalidCheck::couldNotSendRequest($response);
            }
        } catch (Exception $exception) {
            throw InvalidCheck::couldNotSendRequest($exception);
        }

        return $response;
    }

    public function run(): Result
    {
        $result = Result::new();

        return $this->checkExpectedElement($this->getUrlResponse(), $this->element)
            ? $result->ok()
            : $result->failed(
                $this->getMessage() ?: "The element '{$this->element}' could not be found on the page '{$this->url}'"
            );
    }
}
