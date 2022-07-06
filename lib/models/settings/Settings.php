<?php

namespace Vulcan\lib\models\settings;

use Vulcan\lib\models\settings\border\BorderGroup;
use Vulcan\lib\models\settings\color\ColorGroup;
use Vulcan\lib\models\settings\custom\CustomGroup;
use Vulcan\lib\models\settings\layout\LayoutGroup;
use Vulcan\lib\models\settings\spacing\SpacingGroup;
use Vulcan\lib\models\settings\typography\TypographyGroup;

class Settings {
    public BorderGroup $border;
    public ColorGroup $color;
    public CustomGroup $custom;
    public LayoutGroup $layout;
    public SpacingGroup $spacing;
    public TypographyGroup $typography;
    public BlockGroup $blocks;

    public function __construct(
        BorderGroup $border,
        ColorGroup $color,
        CustomGroup $custom,
        LayoutGroup $layout,
        SpacingGroup $spacing,
        TypographyGroup $typography,
        BlockGroup $blocks
    )
    {
        $this->border = $border;
        $this->color = $color;
        $this->custom = $custom;
        $this->layout = $layout;
        $this->spacing = $spacing;
        $this->typography = $typography;
        $this->blocks = $blocks;
    }
}

        "border" => obj([
            "customRadius" => false,
        ]),
        
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