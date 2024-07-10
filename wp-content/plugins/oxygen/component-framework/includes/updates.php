<?php 

function oxygen_vsb_update_3_6() {

    if ( !get_option("oxygen_vsb_update_3_6") && oxygen_vsb_is_touched_install() ) {

        // check user license to whether enable Edit Mode option ot not
        oxygen_vsb_check_is_agency_bundle();

        // need to update universal.css to apply new Columns Padding Global Styles
        oxygen_vsb_cache_universal_css();

        // make sure this fires only once
        add_option("oxygen_vsb_update_3_6", true);

    };
}
add_action("admin_init", "oxygen_vsb_update_3_6");


function oxygen_vsb_update_3_7() {

    if ( !get_option("oxygen_vsb_update_3_7") ) {
    
        if ( oxygen_vsb_is_touched_install() ) {
            add_option("oxygen_options_autoload", "yes");
        }
        else {
            add_option("oxygen_options_autoload", "no");
        }

        add_option("oxygen_vsb_update_3_7", true);
    };
}
add_action("admin_init", "oxygen_vsb_update_3_7", 1);


function oxygen_vsb_update_4_0() {

    if ( !get_option("oxygen_vsb_update_4_0_shortcodes_signed") && oxygen_vsb_is_touched_install() ) {
        add_action( 'admin_notices', 'shortcodes_to_json_notice' );
    };
}
add_action("admin_init", "oxygen_vsb_update_4_0", 1);

function shortcodes_to_json_notice() { 

	$page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : false;

	if ($page == 'oxygen_vsb_sign_shortcodes') {
		return;
	}

	?>
    <div class="notice notice-warning">
        <p><?php _e( 'Oxygen is now using JSON instead of WordPress shortcodes to store your designs.', 'oxygen' );
         		echo ' <a href="'.get_admin_url().'admin.php?page=oxygen_vsb_sign_shortcodes">';
         		_e( 'Please sign shortcodes to convert them to new format right away.', 'oxygen' ); 
         		echo "</a>";
         	?>
        </p>
    </div>
<?php }


function oxygen_vsb_update_4_8_3() {

    if ( get_option("oxy_migrated_to_prefixed_meta") ) {
        return;
    }
        
    if ( oxygen_vsb_is_touched_install() ) {
        oxy_prefix_meta_keys();
    }
        
    // make sure this fires only once
    add_option("oxy_migrated_to_prefixed_meta", true);
    update_option("oxy_meta_keys_prefixed", true );
}
add_action("admin_init", "oxygen_vsb_update_4_8_3", 20); // 20 because it shold be called after oxy_post_meta_prefix_unprefix()

function oxygen_vsb_update_4_8_3_migration_notice() {
    global $wpdb;

    // Query to find meta keys starting with 'ct_' but not '_ct_'
    $query = "
        SELECT meta_key
        FROM $wpdb->postmeta
        WHERE meta_key LIKE 'ct_%'
        AND meta_key NOT LIKE '\_ct_%'
        LIMIT 1
    ";

    // Run the query
    $results = $wpdb->get_results($query);
    
    if( !empty($results) ) {
        add_action("admin_notices", "oxygen_meta_migration_notice");
    }
};
add_action("admin_init", "oxygen_vsb_update_4_8_3_migration_notice");

function oxygen_meta_migration_notice() {
    ?>
    <div class="notice notice-warning is-dismissible">
        <p><?php _e( 'Oxygen 4.8.3+ requires a migration of your Oxygen meta keys, and some keys have not yet migrated automatically. If this notice remains after 5 to 10 minutes and your site is experiencing issues, ', 'oxygen' );
         		echo '<a href="'.get_admin_url().'admin.php?page=oxygen_vsb_settings&tab=tools">';
         		_e( 'please click here to re-run the migration manually.', 'oxygen' ); 
         		echo "</a>";
         	?>
        </p>
    </div>
    <?php
}


function oxy_prefix_meta_keys($unprefix="") {

    global $wpdb;

    // Prefixes to search for and replace
    $prefixes = array('ct_', 'oxy_', 'oxygen_');
    $replacements = array('_ct_', '_oxy_', '_oxygen_');

    if ($unprefix==='unprefix') {
        $prefixes = array('_ct_', '_oxy_', '_oxygen_');
        $replacements = array('ct_', 'oxy_', 'oxygen_');
    }
    
    foreach ($prefixes as $index => $prefix) {
        // Construct SQL query to update meta_key values
        $query = $wpdb->prepare(
            "UPDATE {$wpdb->postmeta}
             SET meta_key = REPLACE(meta_key, %s, %s)
             WHERE meta_key LIKE %s",
            $prefix,
            $replacements[$index],
            $prefix . '%'
        );

        //var_dump($query);
    
        // Execute the query
        $wpdb->query($query);
    }
}

// Add a way to unprefix with GET params
function oxy_post_meta_prefix_unprefix() {

    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }

    if (isset($_GET['unprefix_meta'])) {
        oxy_prefix_meta_keys('unprefix');
        update_option( 'oxy_meta_keys_prefixed', false );
    }

    if (isset($_GET['prefix_meta'])) {
        oxy_prefix_meta_keys('prefix');
        update_option( 'oxy_meta_keys_prefixed', true );
    }
}
add_action("admin_init", "oxy_post_meta_prefix_unprefix", 10);