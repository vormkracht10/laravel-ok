<?php

namespace Vormkracht10\LaravelOK\Enums;

enum Status: string
{
    case OK = 'ok';
    case FAILED = 'failed';
    case SKIPPED = 'skipped';
    case CRASHED = 'crashed';
}
