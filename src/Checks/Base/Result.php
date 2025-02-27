<?php

namespace Backstage\Laravel\OK\Checks\Base;

use Carbon\CarbonInterface;
use Backstage\Laravel\OK\Enums\Status;

class Result
{
    public Check $check;

    public ?CarbonInterface $ended_at;

    public static function new(string $message = ''): self
    {
        return new self(Status::OK, $message);
    }

    public function __construct(
        public Status $status,
        public string $message = '',
        public string $summary = '',
    ) {
        //
    }

    public function check(Check $check): self
    {
        $this->check = $check;

        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function summary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function ok(string $message = ''): self
    {
        $this->message = $message;

        $this->status = Status::OK;

        return $this;
    }

    public function failed(string $message = ''): self
    {
        $this->message = $message;

        $this->status = Status::FAILED;

        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function endedAt(CarbonInterface $carbon): self
    {
        $this->ended_at = $carbon;

        return $this;
    }
}
