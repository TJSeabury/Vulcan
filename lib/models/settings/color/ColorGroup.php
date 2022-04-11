<?php 

namespace Vulcan\lib\models\settings\color;

$template = obj([
    "link"=> false,
    "custom"=> true,
    "customDuotone"=> true,
    "customGradient"=> true,
    "duotone"=> [
        obj([
            "colors"=> [ "#000", "#FFF" ],
            "slug"=> "black-and-white",
            "name"=> "Black and White",
        ]),
    ],
    "gradients"=> [
        obj([
            "slug"=> "blush-bordeaux",
            "gradient"=> "linear-gradient(135deg,rgb(254,205,165) 0%,rgb(254,45,45) 50%,rgb(107,0,62) 100%)",
            "name"=> "Blush bordeaux",
        ]),
        obj([
            "slug"=> "blush-light-purple",
            "gradient"=> "linear-gradient(135deg,rgb(255,206,236) 0%,rgb(152,150,240) 100%)",
            "name"=> "Blush light purple",
        ]),
    ],
    "palette"=> [
        obj([
            "slug"=> "strong-magenta",
            "color"=> "#a156b4",
            "name"=> "Strong magenta",
        ]),
        obj([
            "slug"=> "very-dark-grey",
            "color"=> "rgb(131, 12, 8)",
            "name"=> "Very dark grey",
        ]),
    ],
]);

class ColorGroup {
    
}