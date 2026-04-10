<?php

if (!function_exists('breakdance_zero_theme_setup')) {
    function breakdance_zero_theme_setup()
    {
        add_theme_support('title-tag');
        add_theme_support( 'post-thumbnails' );

    }
}

add_action('after_setup_theme', 'breakdance_zero_theme_setup');


if (!function_exists('warn_if_breakdance_is_disabled')) {
    add_action( 'admin_notices', 'warn_if_breakdance_is_disabled' );

    function warn_if_breakdance_is_disabled() {
        if (defined('__BREAKDANCE_DIR__')){
            return;
        }

        ?>
        <div class="notice notice-error is-dismissible">
            <p>You're using Breakdance's Zero Theme but Breakdance is not enabled. This isn't supported.</p>
        </div>
        <?php
    }
}

// 隐藏 "Iks Menu FAQs" 主菜单
add_action('admin_menu', function() {
 remove_menu_page('edit.php?post_type=iksm_faq');
 
 // 隐藏 "Iks Menu FAQs" 下的所有子菜单
 remove_submenu_page('edit.php?post_type=iksm_faq', 'edit.php?post_type=iksm_faq');
 remove_submenu_page('edit.php?post_type=iksm_faq', 'post-new.php?post_type=iksm_faq');
 remove_submenu_page('edit.php?post_type=iksm_faq', 'edit-tags.php?taxonomy=iksm_faq_group&post_type=iksm_faq');
}, 999);