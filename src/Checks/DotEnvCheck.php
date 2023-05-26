<?php

namespace Vormkracht10\LaravelOK\Checks;

use Dotenv\Dotenv;
use Vormkracht10\LaravelOK\Checks\Base\Check;
use Vormkracht10\LaravelOK\Checks\Base\Result;

class DotEnvCheck extends Check
{
    protected array $required;

    public function required(array $variables): self
    {
        $this->required = $variables;

        return $this;
    }

    public function run(): Result
    {
        $result = Result::new();

        $vars = collect(
            Dotenv::parse(file_get_contents(base_path('.env')))
        );

        $missing = collect($this->required)
            ->filter(fn ($variable, $value) => is_int($value) ? ! $vars->has($variable) : $vars->get($value) != $variable);

        if ($missing->isNotEmpty()) {
            return $result->failed('Missing required variables in .env file: '.$missing->implode(', '));
        } else {
            return $result->ok();
        }
    }
}
