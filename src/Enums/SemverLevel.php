<?php

namespace Backstage\Laravel\OK\Enums;

enum SemverLevel: string
{
    case All = 'all';
    case Major = 'major';
    case Minor = 'minor';
}
