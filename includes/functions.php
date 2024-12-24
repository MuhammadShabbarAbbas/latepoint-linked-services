<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get template part (for templates like the shop-loop).
 *
 * WC_TEMPLATE_DEBUG_MODE will prevent overrides in themes from taking priority.
 *
 * @param mixed  $slug Template slug.
 * @param string $name Template name (default: '').
 */
function os_get_template_part($slug, $name, $args = array(), $path = LATEPOINT_LINKED_SERVICES_DIR . "templates/")
{
    /*
     * locate_template() returns path to file.
     * if either the child theme or the parent theme have overridden the template.
     */
    $template = locate_template("{$slug}-{$name}.php");

    if (!$template) {
        /*
         * If neither the child nor parent theme have overridden the template,
         * we load the template from the 'templates' sub-directory of the directory this file is in.
         */
        $template = $path . "{$slug}-{$name}.php";
    }

    $template = apply_filters('os_get_template_part', $template, $slug, $name, $args);

    if (!file_exists($template)) {
        /* translators: %s template */
        _doing_it_wrong(__FUNCTION__, sprintf(__('%s does not exist.', 'expert-classroom'), '<code>' . $template . '</code>'), '1.0.0');
        return;
    }
    load_template($template, false, $args);
}