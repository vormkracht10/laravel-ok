<?php

namespace Backstage\Laravel\OK\Enums;

enum Status: string
{
    case OK = 'ok';
    case FAILED = 'failed';
    case SKIPPED = 'skipped';
    case CRASHED = 'crashed';
}
