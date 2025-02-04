<?php

namespace Vulcan\lib\models\settings\spacing;

class Padding extends AbstractWhiteSpace
{
    public function __construct(
        float $top,
        float $right,
        float $bottom,
        float $left
    ) {
        $this->top = $top;
        $this->right = $right;
        $this->bottom = $bottom;
        $this->left = $left;
    }
}
