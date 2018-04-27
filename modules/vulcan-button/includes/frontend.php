<?php
$text = $settings->button_text;
$link = $settings->button_link;
$icon = $settings->button_icon;
$color = formatColor( $settings->button_text_color );
$bkg_color = formatColor( $settings->button_background_color );
$alignment = $settings->button_alignment;
$newTab = $settings->open_in_new_tab ? 'target="_blank"' : '';
$radius = $settings->border_radius;

function formatColor( string $c )
{
	if ( false !== strpos( $c, 'rgb' ) )
	{
		return $c;
	}
	if ( false !== ctype_xdigit( $c ) )
	{
		return "#$c";
	}
}

$html = <<<HTML
<div id="vulcan_button_{$id}" class="vulcan_button" style="text-align: {$alignment}" >
	<a href="{$link}" {$newTab}>
		<div class="transform-wrapper" style="background-color:{$bkg_color};border-radius:{$radius}px;" >
			<i class="{$icon}" style="color:{$color};"></i>
			<span style="color:{$color};">{$text}</span>
		</div>
		<div class="shadow" style="border-radius:{$radius}px;"></div>
	</a>
</div>
HTML;
echo $html;