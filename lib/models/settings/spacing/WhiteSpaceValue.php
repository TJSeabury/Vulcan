<?php

namespace Vulcan\lib\models\settings\spacing;

use Vulcan\lib\models\settings\EnumLengthPercentageUnits;

class WhiteSpaceValue
{
    private float $value;
    private string $unit;
    public string $string;

    public function __construct(float $value, string $unit)
    {
        $this->update($value, $unit);
    }

    public function update(float $value, string $unit): void
    {
        $this->value = $value;
        if (!EnumLengthPercentageUnits::isValidValue($unit)) throw new \Error("Given unit ( {$unit} ) is not a valid css Length-Percentage unit.");
        $this->unit = $unit;
        if (EnumLengthPercentageUnits::AUTO == $unit) {
            $this->value = null;
        }
        $this->string = $this->print();
    }

    public function print(): string
    {
        return "{$this->value}{$this->unit}";
    }
}
