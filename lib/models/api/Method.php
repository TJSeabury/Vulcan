<?php

namespace Vulcan\lib\models\api;

class Method
{
    protected static $validVerbs = [
        'GET',
        'POST',
        'PATCH',
        'PUT',
        'DELETE',
    ];

    protected static function verbsAreValid(array $verbs): bool
    {
        foreach ($verbs as $verb) {
            if (!in_array(
                strtoupper($verb),
                static::$validVerbs
            )) {
                return false;
            }
        }
        return true;
    }

    public function formatVerbs(): string
    {
        return implode(
            ', ',
            array_map(
                fn ($v) => strtoupper($v),
                $this->verbs
            )
        );
    }

    public array $verbs;

    protected $action;

    public function __construct(array $verbs, callable $action)
    {
        if (!static::verbsAreValid($verbs)) {
            throw new \Error('Invalid HTTP verbs! ' . $verbs);
        }
        $this->verbs = $verbs;
        $this->action = $action;
    }

    public function action(\WP_REST_Request $request): void
    {
        if (!is_callable($this->action)) {
            throw new \Error('Action is not callable!');
        }
        $this->action($request);
    }
}
