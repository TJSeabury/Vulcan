<?php

namespace Vulcan\lib\models\admin;

/**
 * Class MenuField
 * Part of the Settings API. Use this to define a settings field that will show as part of a settings section inside a settings page. The fields are shown using do_settings_fields() in do_settings-sections()
 * 
 * The $callback argument should be the name of a function that echoes out the html input tags for this setting field. Use get_option() to retrieve existing values to show.
 * 
 * $id
 * (string) (Required) Slug-name to identify the field. Used in the 'id' attribute of tags.
 * 
 * $title
 * (string) (Required) Formatted title of the field. Shown as the label for the field during output.
 * 
 * $callback
 * (callable) (Required) Function that fills the field with the desired form inputs. The function should echo its output.
 * 
 * $page
 * (string) (Required) The slug-name of the settings page on which to show the section (general, reading, writing, ...).
 * 
 * $section
 * (string) (Optional) The slug-name of the section of the settings page in which to show the box.
 * 
 * Default value: 'default'
 * 
 * $args
 * (array) (Optional) Extra arguments used when outputting the field.
 * 
 * 'label_for'
 * (string) When supplied, the setting title will be wrapped in a <label> element, its for attribute populated with this value.
 * 'class'
 * (string) CSS Class to be added to the <tr> element when the field is output.
 * Default value: array()
 *
 * @package Vulcan\models\admin
 */
class MenuField
{

    /**
     * @var object
     */
    private $settings;

    /**
     * MenuField constructor.
     *
     * @param string $section
     * @param string $group
     * @param string $id
     * @param string $type
     * @param string $description
     *
     * @throws \Vulcan\lib\utils\VulcanException
     */
    public function __construct(
        object $pageSettings,
        string $section,
        string $group,
        string $id,
        string $type,
        string $description
    ) {
        if (
            !$section || !is_string($section) ||
            !$group || !is_string($group) ||
            !$id || !is_string($id) ||
            !$type || !is_string($type) ||
            !$description || !is_string($description)
        ) {
            throw new \Vulcan\lib\utils\VulcanException('invalid_arguement');
        }

        $this->settings = (object)array(
            'pageSettings' => $pageSettings,
            'options_group' => $GLOBALS['themeName'] . '_' . $group,
            'options_id' => $GLOBALS['themeName'] . '_' . $id,
            'display_name' => ucwords(str_replace('_', ' ', $id)),
            'section' => $section,
            'type' => $type,
            'description' => $description

        );

        \register_setting(
            $this->settings->pageSettings->slug,
            $this->settings->options_id
        );
    }

    /**
     *
     */
    public function render()
    {
        $s = $this->settings;
        \add_settings_field(
            $s->options_id,
            $s->display_name,
            $this->get_view($s->type, $s),
            $this->settings->pageSettings->slug,
            $s->section,
            array(
                'section' => $s->section,
                'group' => $s->options_group,
                'id' => $s->options_id,
                'type' => $s->type,
                'description' => $s->description
            )
        );
    }

    /**
     * @param $type
     * @param $s
     *
     * @return \Closure
     */
    private static function get_view($type, $s): callable
    {
        return function () use ($type, $s) {
            $data = array(
                'id' => $s->options_id,
                'desc' => $s->description
            );
            $view = new \Vulcan\lib\views\View(
                'admin',
                'MenuField' . ucwords($type),
                $data
            );
            echo $view->render();
        };
    }
}
