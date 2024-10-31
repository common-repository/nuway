<?php
if (!defined('ABSPATH')) {
    exit;  // Prevent direct access
}
?>
<style>
    .nuway {
        background-color: #ebebeb;
        padding: 20px;
        color: #444444;
    }

    .nuway h1 {
        font-size: 23px;
    }

    .nuway_installed {
        float: left;
        background: #4caf50;
        padding: 4px 10px;
        border-radius: 4px;
        color: white;
        font-family: tahoma;
        font-size: 60%;
        font-weight: normal;
        margin-right: 5px;
    }

    #nuway_id {
        width: 200px;
    }
</style>
<div class="wrap">
    <h1>
        <a href="https://www.nuway.co" target="_blank">
            <img style="width:200px;" src="<?php echo esc_url(NUWAY_PLUGIN_IMG_URL . 'logo.png'); ?>" alt="<?php esc_attr_e('Nuway', 'nuway_1.0.7'); ?>" />
        </a>
    </h1>
    <?php
    // Display error message if any
    if ($nuway_error = get_transient('nuway_error')) {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo esc_html($nuway_error); ?></p>
        </div>
        <?php
        // Clear error message transient
        delete_transient('nuway_error');
    }
    ?>

    <div class="nuway">
        <h1><?php esc_html_e('Setting', 'nuway_1.0.7'); ?>
            <?php if (isset($nuway_widget_id) && $nuway_widget_id) { ?>
                <div class="nuway_installed"><?php esc_html_e('Installed', 'nuway_1.0.7'); ?></div>
            <?php } ?>
        </h1>
        <hr>
        <p>
            <?php
            if (!isset($nuway_widget_id) || !$nuway_widget_id) {
                // Instruction for installation if widget ID is not set
                esc_html_e('To install the Nuway widget on your website, only one step remains. Now,', 'nuway_1.0.7');
            } ?>
            <?php esc_html_e('in the dashboard panel, go to the', 'nuway_1.0.7'); ?>
            <a href="https://app.nuway.co/#/chatbot/settings" target="_blank"><?php esc_html_e('Chatbot Setting', 'nuway_1.0.7'); ?></a>
            <?php esc_html_e('page and enter your Company ID in the box below.', 'nuway_1.0.7'); ?>
        </p>
        <br>
        <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" id="form-token">
            <input type="hidden" name="action" value="nuway_save_setting">
            <div>
                <label for="nuway_id"><?php esc_html_e('Company ID:', 'nuway_1.0.7'); ?></label>
                <input type="text" id="nuway_id" name="widget_id" value="<?php echo esc_attr(get_option('nuway_widget_id')); ?>" />
                <?php wp_nonce_field('nuway_nonce' . get_current_user_id()); ?>
                <input type="submit" name="submit" class="button button-primary" value="<?php echo esc_attr__('Submit', 'nuway_1.0.7'); ?>">
            </div>
            <br><br>
            <?php
            if (!isset($nuway_widget_id) || !$nuway_widget_id) {
                // Registration link if widget ID is not set
                ?>
                <hr>
                <p>
                    <?php esc_html_e('If you haven\'t registered on NUWAY yet, you can', 'nuway_1.0.7'); ?>
                    <a href="https://app.nuway.co/#/register" target="_blank"><?php esc_html_e('join', 'nuway_1.0.7'); ?></a>
                    <?php esc_html_e('and multiply your service/product sales by conversing with your customers.', 'nuway_1.0.7'); ?>
                </p>
            <?php } ?>
        </form>
    </div>
    <?php
    if (isset($nuway_widget_id) && $nuway_widget_id) {
        // Additional instructions if widget ID is set
        ?>
        <br>
        <div class="nuway">
            <?php esc_html_e('To start conversations with users, customize the widget, manage operators, and utilize other NUWAY features, log into the management interface.', 'nuway_1.0.7'); ?>
            <br><br>
            <a class="button button-primary" href="https://app.nuway.co/#/login" target="_blank"><?php esc_html_e('Log into the management system', 'nuway_1.0.7'); ?></a>
            <br><br>
        </div>
    <?php } ?>

    <p style="font-size: 12px; text-align: center;">
        <?php esc_html_e('NUWAY, the online chat system |', 'nuway_1.0.7'); ?>
        <a href="https://www.nuway.co/" target="_blank"><?php esc_html_e('Nuway.co', 'nuway_1.0.7'); ?></a>
    </p>
</div>

