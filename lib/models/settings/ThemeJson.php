<?php 

namespace Vulcan\lib\models\settings;

use \Vulcan\lib\utils\Object as obj;

$settingsTemplate = obj([
    "version"=> 2,
    "settings"=> obj([
        "border"=> obj([
            "customRadius"=> false,
        ]),
        "color"=> Color(),
        "custom"=> obj([]),
        "layout"=> obj([
            "contentSize"=> "800px",
            "wideSize"=> "1000px",
        ]),
        "spacing"=> obj([
            "customMargin"=> false,
            "customPadding"=> false,
            "units"=> [ "px", "em", "rem", "vh", "vw" ],
        ]),
        "typography"=> obj([
            "customFontSize"=> true,
            "customLineHeight"=> false,
            "dropCap"=> true,
            "fontFamilies"=> [
                obj([
                    "fontFamily"=> "-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Oxygen-Sans,Ubuntu,Cantarell, \"Helvetica Neue\",sans-serif",
                    "slug"=> "system-font",
                    "name"=> "System Font",
                ]),
                obj([
                    "fontFamily"=> "Helvetica Neue, Helvetica, Arial, sans-serif",
                    "slug"=> "helvetica-arial",
                    "name"=> "Helvetica or Arial",
                ]),
            ],
            "fontSizes"=> [
                obj([
                    "slug"=> "normal",
                    "size"=> 16,
                    "name"=> "Normal"
                ]),
                obj([
                    "slug"=> "big",
                    "size"=> 32,
                    "name"=> "Big",
                ]),
            ],
        ]),
        "blocks"=> obj([
            "core/paragraph"=> obj([
                "color"=> obj([
                    "palette"=> [
                        obj([
                            "slug"=> "black",
                            "color"=> "#000000",
                            "name"=> "Black",
                        ]),
                        obj([
                            "slug"=> "white",
                            "color"=> "#ffffff",
                            "name"=> "White",
                        ])
                    ]
                ]),
                "custom"=> obj([]),
                "layout"=> obj([]),
                "spacing"=> obj([]),
                "typography"=> obj([]),
            ]),
            "core/heading"=> obj([]),
            "etc"=> obj([]),
        ])
    ])
]);

class ThemeJSON {



}