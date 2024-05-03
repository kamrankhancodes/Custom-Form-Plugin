<?php
/*
* Plugin Name: Custom Form
* Plugin URI: https:codesystematic.om
* Author: Sajjad
* Author URI: https://sajjadcodes.com
* Description: Plugin to add a custom form to landing pages and display form data on the thank you page.
* Text Domain: wp-custom-form
* Domain Path: /languages/
* Version: 1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!defined('WPCF_PLUGIN_DIR')){
    define('WPCF_PLUGIN_DIR', plugin_dir_url( __FILE__) );
}

require plugin_dir_path( __FILE__ ). 'inc/settings.php';

if (!function_exists('wpcf_template_scripts')) {
    function wpcf_template_scripts() {
            wp_enqueue_style('wpcf-css', WPCF_PLUGIN_DIR . 'assets/css/style.css');
        }
    add_action('wp_enqueue_scripts', 'wpcf_template_scripts');
}

function enqueue_admin_styles_wpcf() {
    wp_enqueue_style( 'plugin-admin-styles-css', plugin_dir_url( __FILE__ ) . 'admin/assets/css/adminstyle.css' );
}
add_action( 'admin_enqueue_scripts', 'enqueue_admin_styles_wpcf' );

function custom_form_shortcode( $atts ) {
    // Parse attributes
    $atts = shortcode_atts( array(
        // Define default attributes if needed
    ), $atts );

    // Form HTML
    $form_html = '
    <div class="custom-form-container">
        <h2 class="form-heading">Submit Your Information</h2>
        <form id="custom-form" method="post">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>
    ';

    return $form_html;
}
add_shortcode( 'custom_form', 'custom_form_shortcode' );

function handle_custom_form_submission() {
    if ( isset( $_POST['name'] ) && isset( $_POST['email'] ) ) {
        $existing_form_data = get_option('custom_form_data', array());
        // Process form data
        $name = sanitize_text_field( $_POST['name'] );
        $email = sanitize_email( $_POST['email'] );

        // Save form data in options table
        $form_data = array(
            'name' => $name,
            'email' => $email
        );
        $existing_form_data[] = $form_data;
        update_option( 'custom_form_data', $existing_form_data );

        // Redirect to thank you page
        wp_redirect( home_url( '/thank-you' ) );
        exit;
    }
}

add_action( 'init', 'handle_custom_form_submission' );

function display_form_data_shortcode() {
    // Retrieve form data from options table
    $form_data = get_option( 'custom_form_data' );

    // Check if there are any form submissions
    if ( ! empty( $form_data ) ) {
        // Get the last form submission
        $latest_submission = end( $form_data );

        // Extract name and email from the latest submission
        $name = $latest_submission['name'];
        $email = $latest_submission['email'];

        // Output form data HTML
        $form_data_html = '
        <div class="thank-you-container">
            <h2 class="thank-you-heading">Thank you for your submission!</h2>
            <div class="form-data">
                <p><strong>Name:</strong> ' . $name . '</p>
                <p><strong>Email:</strong> ' . $email . '</p>
            </div>
        </div>
        ';

        return $form_data_html;
    } else {
        // No form submissions found
        return '<p>No form submissions found.</p>';
    }
}
add_shortcode( 'display_form_data', 'display_form_data_shortcode' );

function create_thank_you_page() {
    // Check if thank you page exists
    $thank_you_page = get_page_by_title( 'Thank You' );

    // If thank you page doesn't exist, create it
    if ( ! $thank_you_page ) {
        $thank_you_page_args = array(
            'post_title'    => 'Thank You',
            'post_content'  => '[display_form_data]',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        );

        // Insert the page into the database
        wp_insert_post( $thank_you_page_args );
    }
}
register_activation_hook( __FILE__, 'create_thank_you_page' );
