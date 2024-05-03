<?php

function wpcf_settings_page_html() {
        if(!is_admin()){
        return;
    }
    ?>
        <div class="wrap">
            <h3>Custom Form Shortcode</h3>
            <p>Please add the below shortcode in the form page</p>
            <p><b>Shortcode:</b> [custom_form]</p>
        </div>
        <?php
}
function wpcf_register_menu_page(){

    add_menu_page('Custom Form', 'Custom Form','manage_options','wpcf-settings', 'wpcf_settings_page_html','dashicons-edit-page', 30);
}
add_action('admin_menu','wpcf_register_menu_page');
