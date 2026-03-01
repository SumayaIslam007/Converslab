<div class="wrap">
    <h1><?php echo esc_html__('Converselab Settings', 'converselab'); ?></h1>

    <form method="post">

        <?php wp_nonce_field('converselab_save_settings','converselab_nonce'); ?>

        <table class="form-table">

            <tr>
                <th scope="row">Enable Notes</th>
                <td>
                    <input type="checkbox"
                        name="converselab_notes_enabled"
                        value="1"
                        <?php checked(1, $notes_enabled); ?>>
                </td>
            </tr>

            <tr>
                <th scope="row">Default Notes Count</th>
                <td>
                    <input type="number"
                        name="converselab_notes_default_count"
                        value="<?php echo esc_attr($default_count); ?>"
                        min="1">
                </td>
            </tr>

            <tr>
                <th scope="row">Allowed Roles</th>
                <td>
                    <?php
                    global $wp_roles;
                    foreach ($wp_roles->roles as $role_key => $role) :
                    ?>
                        <label>
                            <input type="checkbox"
                                name="converselab_notes_allowed_roles[]"
                                value="<?php echo esc_attr($role_key); ?>"
                                <?php checked(in_array($role_key, $allowed_roles, true)); ?>>
                            <?php echo esc_html($role['name']); ?>
                        </label><br>
                    <?php endforeach; ?>
                </td>
            </tr>

        </table>

        <?php submit_button('Save Settings', 'primary', 'converselab_settings_submit'); ?>

    </form>
</div>
