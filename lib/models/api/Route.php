<?php

namespace Vulcan\lib\models\api;

class Route
{
    public string $name;
    public array $methods = [];

    protected array $usedVerbs = [];

    protected function useVerbs(array $verbs): void
    {
        // we can assume each verb is valid since Method handles that check.
        $this->usedVerbs[] = array_unique(array_merge(
            $this->usedVerbs,
            array_map(fn ($v) => strtoupper($v), $verbs)
        ));
    }

    protected function methodVerbsDoNotConflict(\Vulcan\lib\models\api\Method $method): bool
    {
        foreach ($method->verbs as $verb) {
            if (in_array(strtoupper($verb), $this->usedVerbs)) {
                return false;
            }
        }
        return true;
    }

    public function __construct(
        string $name,
        array $methods
    ) {
        $this->name = $name;
        // Store each method in methods if it is an instance of Method and 
        // ensure there is no verb overlap between the methods.
        foreach ($methods as $method) {
            if (!$method instanceof \Vulcan\lib\models\api\Method) {
                throw new \Error('Methods must be instance of Method!');
            }
            if ($this->methodVerbsDoNotConflict($method)) {
                $this->useVerbs($method->verbs);
                $this->methods[] = $method;
            }
        }
    }

    protected function composeMethod(\Vulcan\lib\models\api\Method $method)
    {
        return [
            // By using this constant we ensure that when the WP_REST_Server changes our readable endpoints will work as intended.
            'methods'  => $method->formatVerbs(),
            // Here we register our callback. The callback is fired when this endpoint is matched by the WP_REST_Server class.
            'callback' => $method->action
        ];
    }

    public function register(): array
    {
        if (1 === count($this->methods)) {
            return $this->composeMethod($this->methods[0]);
        }
        return array_reduce(
            $this->methods,
            fn ($m) => $this->composeMethod($m),
            []
        );
    }
}
