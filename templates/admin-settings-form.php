<?php
extract($args);
?>
<div class="white-box">
    <div class="white-box-header">
        <div class="os-form-sub-header">
            <h3><?php _e('Linked Services', 'latepoint-linked-services'); ?></h3>
            <div class="os-form-sub-header-actions">
                <?php echo OsFormHelper::checkbox_field('select_all_service_extras', __('Select All', 'latepoint-linked-services'), 'off', false, ['class' => 'os-select-all-toggler']); ?>
            </div>
        </div>
    </div>
    <div class="white-box-content">
        <div class="os-complex-connections-selector">
            <?php if ($other_services) {
                foreach ($other_services as $service) {
                    // one location
                    $is_connected = $current_service->is_new_record() ? false : in_array($service->id, $linked_services);
                    $is_connected_value = $is_connected ? 'yes' : 'no';
                    ?>
                    <div class="connection <?php echo $is_connected ? 'active' : ''; ?>">
                        <div class="connection-i selector-trigger">
                            <!--                            <div class="connection-avatar"><img src="-->
                            <?php //echo $service->get_avatar_url();
                            ?><!--"/></div>-->
                            <h3 class="connection-name"><?php echo esc_html($service->name); ?></h3>
                            <?php echo OsFormHelper::hidden_field('service[links][link_' . $service->id . '][connected]', $is_connected_value, array('class' => 'connection-child-is-connected')); ?>
                        </div>
                    </div>
                    <?php
                }
            } else { ?>
                <div class="no-results-w">
                    <div class="icon-w"><i class="latepoint-icon latepoint-icon-folder"></i></div>
                    <h2><?php _e('You have not yet created other services', 'latepoint'); ?></h2>
                </div> <?php
            }
            ?>
        </div>
    </div>
</div>
