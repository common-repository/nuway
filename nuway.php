<?php
/**
 * Plugin Name: Nuway
 * Author: Nuway
 * Plugin URI: https://www.nuway.co
 * Description: Nuway’s chatbot provides instant support 24/7, without the need for human agents to be available around the clock. This can reduce the number of agents in your CRM department.
 * Version: 1.0.7
 * Text Domain: nuway_1.0.7
 * Domain Path: /languages
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Load plugin text domain
function nuway_load_textdomain() {
    load_plugin_textdomain('nuway_1.0.7', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'nuway_load_textdomain');

// Define image URL constant
define("NUWAY_PLUGIN_IMG_URL", plugin_dir_url(__FILE__) . "img/");

// Activation and deactivation hooks
register_activation_hook(__FILE__, 'nuway_plugin_install');
register_deactivation_hook(__FILE__, 'nuway_plugin_delete');

// Add admin menu page
function nuway_plugin_admin_menu() {
    add_menu_page(__('Nuway', 'nuway_1.0.7'), __('Nuway', 'nuway_1.0.7'), 'manage_options', 'nuway-settings', 'nuway_plugin_preferences', NUWAY_PLUGIN_IMG_URL . "logo-m.png");
}
add_action('admin_menu', 'nuway_plugin_admin_menu');

// Register settings
function nuway_plugin_register_settings() {
    register_setting('nuway_settings_group', 'nuway_widget_id', 'nuway_plugin_validate');
}
add_action('admin_init', 'nuway_plugin_register_settings');

// Validate function for settings
function nuway_plugin_validate($input) {
    return sanitize_text_field($input);
}

// Save preferences
function nuway_plugin_save_setting() {
    if (
        isset($_POST["_wpnonce"]) &&
        !empty($_POST['submit']) &&
        is_user_logged_in() &&
        current_user_can('manage_options') &&
        check_admin_referer('nuway_nonce' . get_current_user_id())
    ) {
        $nuwayError = null;
        $g_id = isset($_POST['widget_id']) ? trim(sanitize_text_field($_POST['widget_id'])) : '';

        // Validate widget ID
        if (!empty($g_id) && preg_match("/^[0-9a-zA-Z]+$/", $g_id)) {
            update_option('nuway_widget_id', $g_id);
            $nuway = Nuway_Plugin::getInstance();
            $nuway->install();
        } else {
            $nuwayError = __("Invalid ID!", 'nuway_1.0.7');
        }

        set_transient('nuway_error', $nuwayError);
    }

    wp_safe_redirect(wp_get_referer());
    exit();
}
add_action('admin_post_nuway_save_setting', 'nuway_plugin_save_setting');

// Append chatbot script to footer
function nuway_plugin_append() {
    $widget_id = Nuway_Plugin::getInstance()->getId();
    if ($widget_id) {
        wp_enqueue_script(
            'nuway-chatbot-script',
            'https://app.nuway.co/js/chatbot-loader.js',
            array(),
            '1.0.7', // Use plugin version to bust cache
            true
        );

        wp_add_inline_script('nuway-chatbot-script', sprintf(
            "document.addEventListener('DOMContentLoaded', function() {
                var scriptElement = document.getElementById('nuway-chatbot-script-js');
                if (scriptElement) {
                    scriptElement.setAttribute('nuway-company-id', '%s');
                    scriptElement.setAttribute('nuway-url', 'https://app.nuway.co');
                }
            });",
            esc_attr($widget_id)
        ));
    }
}
add_action('wp_enqueue_scripts', 'nuway_plugin_append', 100000);

// Plugin activation function
function nuway_plugin_install() {
    return Nuway_Plugin::getInstance()->install();
}

// Plugin deactivation function
function nuway_plugin_delete() {
    return Nuway_Plugin::getInstance()->delete();
}

// Plugin settings page
function nuway_plugin_preferences() {
    if (
        is_user_logged_in() &&
        current_user_can('manage_options') &&
        isset($_POST["_wpnonce"]) &&
        wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'nuway_nonce' . get_current_user_id())
    ) {
        if (isset($_POST["widget_id"])) {
            Nuway_Plugin::getInstance()->save();
        }
    }

    Nuway_Plugin::getInstance()->render();
}

// Main plugin class
class Nuway_Plugin {
    protected static $instance;
    private $widget_id = '';

    private function __construct() {
        $this->widget_id = get_option('nuway_widget_id');
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function install() {
        if (!$this->widget_id) {
            $this->widget_id = get_option('nuway_widget_id', '');
        }
        $this->save();
    }

    public function delete() {
        delete_transient('nuway_error');
        delete_option('nuway_widget_id');
    }

    public function getId() {
        return $this->widget_id;
    }

    public function render() {
        $widget_id = esc_attr($this->widget_id);
        require_once "setting.php"; 
    }

    public function save() {
        update_option('nuway_widget_id', $this->widget_id);
    }
}

