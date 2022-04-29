<?php

/**
 * Plugin Name: requestForm
 * Description: This plugin is my test work
 * Plugin URI:  https://github.com/shapovalovmax/Request-form.git
 * Author URI:  https://github.com/shapovalovmax
 * Author:      Max Shapovalov
 * Version:     1.0
 */

defined('ABSPATH') or die('Really?');

if (class_exists('requestForm') == false) {

    add_action('plugins_loaded', array('requestForm', 'init'));

    register_activation_hook(__FILE__, array('requestForm', 'request_form_db'));

    class requestForm
    {

        protected static $instance;

        public $table_name;

        public $table_result;

        public static function init()
        {
            is_null(self::$instance) AND self::$instance = new self;
            return self::$instance;
        }

        public function __construct()
        {
            global $wpdb;

            $this->table_name = $wpdb->prefix . 'request_form';
            $this->table_result = $wpdb->get_results("SELECT * FROM  $this->table_name");

            add_shortcode('request-form', array($this, 'request_form'));
            add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));

            add_action('wp_ajax_requestForm', array($this, 'ajax_requestForm'));
            add_action('wp_ajax_nopriv_requestForm', array($this, 'ajax_requestForm'));

            add_action('wp_ajax_deleteTable', array($this, 'deleteTable'));
            add_action('wp_ajax_nopriv_deleteTable', array($this, 'deleteTable'));

            add_action('admin_enqueue_scripts', array($this, 'plugin_styles'));
            add_action('admin_menu', array($this, 'requestForm_register_admin_page'));
        }

        public function wp_enqueue_scripts()
        {
            wp_enqueue_style('request-form', plugin_dir_url(__FILE__) . 'assets/style.css');
            wp_enqueue_style('request-form-jquery-ui', plugin_dir_url(__FILE__) . 'assets/jquery-ui-1.13.1/jquery-ui.min.css');

            wp_enqueue_script('request-form', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), null, true);

            wp_localize_script('request-form', 'requestForm', array(
                'ajaxUrl'  => admin_url('admin-ajax.php'),
                'action'   => 'requestForm',
                'security' => wp_create_nonce('requestForm_security'),
            ));
        }

        public function request_form()
        {
            $content = '';
            $content .= '<div class="wrap-request-form">';
            $content .= '<form id="requestForm">';
            $content .= '<input type="text" name="name" id="name" placeholder="Name" required><br/>';
            $content .= '<input type="email" name="email" placeholder="E-mail" required><br/>';
            $content .= '<input type="tel" name="phone" placeholder="Phone" required><br/>';
            $content .= '<button class="btn-request-submit" type="submit">Send</button>';
            $content .= '</form>';
            $content .= '</div>';

            return $content;
        }

        public function plugin_styles()
        {
            wp_register_style('request-form-plugin', plugins_url('/admin-styles' . '.css', dirname(__FILE__) . '/admin/admin-styles' . '.css'));
            wp_enqueue_style('request-form-plugin');

            wp_enqueue_script('request-form-plugin', plugins_url('/admin-scripts' . '.js', dirname(__FILE__) . '/admin/admin-scripts' . '.js'), array('jquery'), null, true);

            wp_localize_script('request-form-plugin', 'deleteTable', array(
                'ajaxTable' => admin_url('admin-ajax.php'),
                'action' => 'deleteTable',
            ));

        }

        public function requestForm_register_admin_page()
        {
            add_menu_page(
                'Таблица запросов',
                'Таблица запросов',
                'manage_categories',
                'request-form',
                array($this, 'request_form_render_admin_page')
            );
        }

        public function request_form_render_admin_page()
        {
            include plugin_dir_path(__FILE__) . 'admin/admin-page.php';
        }

        public function deleteTable()
        {
            global $wpdb;

            if ($_POST['count'] > 0) {
                $sql = "TRUNCATE TABLE $this->table_name";
                $wpdb->query($sql);
                wp_send_json_success();
            } else {
                wp_send_json_error();
            }

            wp_die();
        }

        public function ajax_requestForm()
        {
            global $wpdb;


            if (!wp_verify_nonce($_POST['security'], 'requestForm_security')) {
                wp_die('Security error!');
            }

            parse_str($_POST['form'], $data);

            $str = $data['phone'];
            $resultNumber = preg_replace("/[^,.0-9]/", '', $str);


            $success = $wpdb->insert($this->table_name,
                ['name' => $data['name'], 'email' => $data['email'], 'phone' => $resultNumber, 'time' => date('Y-m-d')],
                ['%s', '%s', '%s', '%s']);

            wp_die();
        }

        public function request_form_db()
        {

            global $wpdb;
            $tb_name = $wpdb->prefix . 'request_form';;
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE IF NOT EXISTS $tb_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                name tinytext NOT NULL COLLATE utf8_general_ci,
                email varchar(50),
                phone DECIMAL(14, 0),
                time DATE NOT NULL,
                UNIQUE KEY id (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

            $is_error = empty($wpdb->last_error);
            return $is_error;

        }


    }

}