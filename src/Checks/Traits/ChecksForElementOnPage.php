<?php

namespace Vormkracht10\LaravelOK\Checks\Traits;

use Symfony\Component\DomCrawler\Crawler;
use Vormkracht10\LaravelOK\Checks\Base\Result;

trait ChecksForElementOnPage
{
    public function checkExpectedElement(string $element)
    {
        // TODO: Pass the response body to the crawler
        $crawler = new Crawler();

        $elementIsPresent = $crawler->filter($element)->count() > 0;

        if (! $elementIsPresent) {
            return false;
        }

        return true;
    }

    public function run(): Result
    {
        $element = $this->queryElement(); // This is the method that is not implemented yet

        $result = Result::new();

        return $this->checkExpectedElement($element)
            ? $result->ok()
            : $result->failed(
                $this->getMessage() ?: 'The element could not be found on the page'
            );
    }
}
