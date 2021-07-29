<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('View Entry', 'wpcf7-entries'); ?></h1>
    <hr class="wp-header-end">

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <table class="wp-list-table widefat fixed striped table-view-list table-wpcf7-entry">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($fields as $key => $value) :
                            if (empty($value)) continue; ?>
                        <tr>
                            <th><?php echo $key ?></th>
                            <td><?php echo nl2br(stripslashes($value)) ?></td>
                        </tr>

                        <?php endforeach; ?>
                    </tbody>

                </table>

            </div><!-- /post-body-content -->

            <div id="postbox-container-1" class="postbox-container">
                <div id="side-sortables" class="meta-box-sortables ui-sortable" style="">
                    <div id="submitdiv" class="postbox ">
                        <div class="postbox-header">
                            <h2 class="hndle ui-sortable-handle"><?php _e('Status', 'wpcf7-entries'); ?></h2>
                            <div class="handle-actions hide-if-no-js">
                                <button type="button" class="handlediv"><span class="toggle-indicator"></span></button>
                            </div>
                        </div>
                        <div class="inside">
                            <div class="submitbox" id="submitpost">
                                <div id="minor-publishing">

                                    <div id="misc-publishing-actions">
                                        <div class="misc-pub-section">
                                            <ul style="margin:0">
                                                <li><label><input type="radio" name="entry_spam" value="1"> <?php _e('Spam', 'wpcf7-entries'); ?></label></li>
                                                <li><label><input type="radio" name="entry_spam" value="0" checked="checked"><?php _e('Not Spam', 'wpcf7-entries'); ?></label></li>
                                            </ul>                                            
                                        </div>

                                        <div class="misc-pub-section curtime misc-pub-curtime">
                                            <span id="timestamp"><?php _e('Submitted on', 'wpcf7-entries'); ?>: <b><?php echo date(get_option( 'date_format'), strtotime($submitted_date)) ?> @ <?php echo date(get_option( 'time_format'), strtotime($submitted_date)) ?></b> </span>
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>

                                <div id="major-publishing-actions">
                                    <div id="delete-action">                                      
                                        <a class="submitdelete deletion" href="<?php echo add_query_arg(['delete' => wp_create_nonce($entry_id)]); ?>"><?php _e('Move to Bin', 'wpcf7-entries'); ?></a>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /post-body -->
        <br class="clear">
    </div>
</div>