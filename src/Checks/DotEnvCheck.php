<?php

namespace Backstage\Laravel\OK\Checks;

use Dotenv\Dotenv;
use Backstage\Laravel\OK\Checks\Base\Check;
use Backstage\Laravel\OK\Checks\Base\Result;

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
