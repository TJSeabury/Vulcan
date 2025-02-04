<?php

namespace Vulcan\lib\controllers\settings;

use Vulcan\lib\models\settings\ThemeJSON as ThemeJsonModel;

use Vulcan\lib\models\settings\color\Color;
use Vulcan\lib\models\settings\color\ColorGroup;
use function \Vulcan\lib\utils\Obj;
use Vulcan\lib\types\ColorCollection;
use Vulcan\lib\types\DuotoneCollection;
use Vulcan\lib\types\GradientCollection;
use OzdemirBurak\Iris\Color\Factory;
use Vulcan\lib\models\settings\color\Duotone;
use Vulcan\lib\models\settings\color\EnumGradientTypes;
use Vulcan\lib\models\settings\color\Gradient;
use Vulcan\lib\types\ColorStops;
use Vulcan\lib\types\Angle;
use Vulcan\lib\types\ColorStop;



$settingsTemplate = obj([
    "settings" => obj([
        "border" => obj([
            "customRadius" => false,
        ]),
        "color" => new ColorGroup(
            [],
            new ColorCollection([
                new Color(
                    'Very dark grey',
                    Factory::init('rgb(131, 12, 8)'),
                ),
                new Color(
                    'Strong magenta',
                    Factory::init('#a156b4'),
                ),
            ]),
            new DuotoneCollection([
                new Duotone(
                    'Black and White',
                    Factory::init('#000'),
                    Factory::init('#FFF'),
                ),
            ]),
            new GradientCollection([
                new Gradient(
                    'Blush light purple',
                    EnumGradientTypes::Linear,
                    new Angle(135),
                    new ColorStops([
                        new ColorStop('rgb(255,206,236)', 0),
                        new ColorStop('rgb(152,150,240)', 100),
                    ]),
                ),
                new Gradient(
                    'Blush bordeaux',
                    EnumGradientTypes::Linear,
                    new Angle(135),
                    new ColorStops([
                        new ColorStop('rgb(254,205,165)', 0),
                        new ColorStop('rgb(254,45,45)', 50),
                        new ColorStop('rgb(107,0,62)', 100)
                    ]),
                ),
            ])
        ),
        "custom" => obj([]),
        "layout" => obj([
            "contentSize" => "800px",
            "wideSize" => "1000px",
        ]),
        "spacing" => obj([
            "customMargin" => false,
            "customPadding" => false,
            "units" => ["px", "em", "rem", "vh", "vw"],
        ]),
        "typography" => obj([
            "customFontSize" => true,
            "customLineHeight" => false,
            "dropCap" => true,
            "fontFamilies" => [
                obj([
                    "fontFamily" => "-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Oxygen-Sans,Ubuntu,Cantarell, \"Helvetica Neue\",sans-serif",
                    "slug" => "system-font",
                    "name" => "System Font",
                ]),
                obj([
                    "fontFamily" => "Helvetica Neue, Helvetica, Arial, sans-serif",
                    "slug" => "helvetica-arial",
                    "name" => "Helvetica or Arial",
                ]),
            ],
            "fontSizes" => [
                obj([
                    "slug" => "normal",
                    "size" => 16,
                    "name" => "Normal"
                ]),
                obj([
                    "slug" => "big",
                    "size" => 32,
                    "name" => "Big",
                ]),
            ],
        ]),
        "blocks" => obj([
            "core/paragraph" => obj([
                "color" => obj([
                    "palette" => [
                        obj([
                            "slug" => "black",
                            "color" => "#000000",
                            "name" => "Black",
                        ]),
                        obj([
                            "slug" => "white",
                            "color" => "#ffffff",
                            "name" => "White",
                        ])
                    ]
                ]),
                "custom" => obj([]),
                "layout" => obj([]),
                "spacing" => obj([]),
                "typography" => obj([]),
            ]),
            "core/heading" => obj([]),
            "etc" => obj([]),
        ])
    ])
]);

class Themejson extends ThemeJsonModel
{
    public function __construct()
    {
    }
}
