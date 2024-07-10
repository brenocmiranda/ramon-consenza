<?php
/*
Plugin Name: Oxygen Gutenberg Integration
Author: Soflyy
Author URI: https://oxygenbuilder.com
Description: Edit Oxygen-designed content directly in the Gutenberg Block Editor.
Version: 1.4.5
*/

require_once("admin/includes/updater/edd-updater.php");
define("CT_OXYGEN_GUTENBERG_VERSION", 	"1.4.5");

class Oxygen_Gutenberg
{
    private $content_array;
	private $reusable_parts;
	static $running;
	private $all_blocks_shortcodes;
	private $rendered_blocks_shortcodes;

	function __construct()
	{
		// Add Gutenberg compatibility only for WordPress 5.0.2+
		if ( version_compare($GLOBALS['wp_version'], '5.0.2') >= 0 ) {
			add_action('init', array($this, 'init'), 11);
			add_action('current_screen', array($this, 'current_screen'));
		}

		Oxygen_Gutenberg::$running = false;

		register_activation_hook( __FILE__, array('Oxygen_Gutenberg', 'install'));

		$this->all_blocks_shortcodes = '';
		$this->rendered_blocks_shortcodes = '';

		add_filter("oxygen_font_families_check_shortcodes", array($this, "frontend_google_fonts"));
	}

	function init()
	{
	    global $pagenow;

		Oxygen_Gutenberg::$running = true;

	    // Exit if Oxygen is not present or if Oxygen version is less than 2.9.9
	    if( !defined('CT_VERSION') || version_compare(CT_VERSION, '2.9.9' ) == -1 ) return;

		add_theme_support('editor-styles');
		add_theme_support( 'align-wide' );

		// Grab all Oxygen reusable parts
		$args = array(
			'posts_per_page' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
			'post_type' => 'oxy_user_library',
			'post_status' => 'publish',
			/*'meta_key' => 'ct_template_type',
			'meta_value' => 'reusable_part'*/
		);

		$args2 = array(
			'posts_per_page' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
			'post_type' => 'page',
			'post_status' => 'publish',
			'meta_key' => '_ct_oxygenberg_full_page_block',
			'meta_value' => '1'
		);

		$library_blocks = new WP_Query($args);
		$page_blocks = new WP_Query($args2);
		$this->blocks = array_merge( $library_blocks->posts, $page_blocks->posts );

		// Add an "Oxygen Blocks" blocks category in gutenberg
		add_filter('block_categories_all', function ($categories, $post) {

		    $block_category_label = get_option('oxygen_vsb_block_category_label');
			$full_page_block_category_label = get_option('oxygen_vsb_full_page_block_category_label');

			return array_merge(
				array(
					array(
						'slug' => 'oxygen-vsb-blocks',
						'title' => empty( $block_category_label ) ? 'Oxygen Blocks' : $block_category_label,
					),
					array(
						'slug' => 'oxygen-vsb-full-page-blocks',
						'title' => empty( $full_page_block_category_label ) ? 'Oxygen Full Page Blocks' : $full_page_block_category_label,
					),
				),
				$categories
			);
		}, 10, 2);

		// Register blocks for server-side rendering
        $this->content_array = [];
        foreach ($this->blocks as $reusable_part) {
            // Block names must contain a prefix (oxygen-vsb in our case) separated with a forward slash.
            // In addition to that, WordPress doesn't allow block names to start with numbers.
            // As Oxygen reusable names are their slugs, which can start with a number, we have to prefix it too.
            register_block_type('oxygen-vsb/ovsb-' . $reusable_part->post_name, array(
                    'render_callback' => array($this, 'render_gutenberg_block'),
                    'attributes' => array(
                        'reusable' => array(
                            'type' => 'string',
                            'default' => 'ovsb-' . $reusable_part->post_name
                        )
                    )
                )
            );
            if (is_admin()) {
                $content = get_post_meta($reusable_part->ID, "_ct_builder_shortcodes", true);

                if ($content) {
                    $this->content_array[] = $content;
                }
            }
        }

		add_filter('do_shortcode_tag', array( $this, 'remove_oxygenberg_metadata' ), 10, 2);
		add_action( 'edit_post', array( $this, 'save_page') );

		if (is_admin() && ($pagenow == 'post.php' || $pagenow == 'post-new.php')) {
            $post_id = isset($_REQUEST["post"]) ? $_REQUEST["post"] : 0;
            $editor_enabled = true;
            if( isset($_REQUEST['post_type']) ){
	            if( !post_type_supports( $_REQUEST['post_type'], 'editor')  ) $editor_enabled = false;
            } else if( $post_id > 0){
                $post = get_post($post_id);
	            if( $post != NULL && !post_type_supports( $post->post_type, 'editor')  ) $editor_enabled = false;
            }

			if($editor_enabled) {
				wp_enqueue_script("purejs-tooltip", CT_FW_URI . "/vendor/purejs-tooltip/mousetip.es2015.min.js", array(), CT_VERSION);
				wp_enqueue_script(
					'oxygen-vsb-gutenberg-blocks',
					plugin_dir_url( __FILE__ ) . "oxygen-gutenberg.js",
					array(
						'wp-blocks',
						'wp-i18n',
						'wp-element',
						'purejs-tooltip'
					)
				);

				// Enqueue generated script
			    wp_enqueue_script(
					'oxygen-vsb-gutenberg-blocks-generator', 
					get_site_url() . "?oxygen_gutenberg_script=true&post=".$post_id."&nocache=".time().
					"&post_id=".$post_id."&nonce=".wp_create_nonce('oxygen-nonce-'.$post_id), 
					array('oxygen-vsb-gutenberg-blocks')
				);
				add_editor_style(CT_FW_URI . "/oxygen.css");
                wp_enqueue_style("oxygenberg", plugin_dir_url( __FILE__ ) . "oxygen-gutenberg.css", array());
                add_action('admin_print_footer_scripts', array($this, 'admin_footer_styles'));

				$universal_css_url = get_option('oxygen_vsb_universal_css_url');
				$universal_css_url = add_query_arg("cache", get_option("oxygen_vsb_last_save_time"), $universal_css_url);
				$protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https:' : 'http:';
				add_editor_style( $protocol.$universal_css_url );

				// Disable AOS inside gutenberg
				remove_action("oxygen_vsb_component_attr", array($GLOBALS['oxygen_vsb_aos'], "attributes") );
				remove_action("ct_footer_js", array($GLOBALS['oxygen_vsb_aos'], "init") );
				wp_dequeue_script('oxygen-aos');

				add_action('ct_after_parent_template_selector', array($this,'add_full_page_template_checkbox'));

			}
		}

		if (!empty($_GET['oxygen_gutenberg_script'])) $this->generate_gutenberg_script();
	}

    function current_screen () {
        global $current_screen;
        if ($current_screen->is_block_editor) {
            if (is_array($this->content_array)) {
                foreach ($this->content_array as $content) {
                    // Run the reusable shortcodes in void so it's css are added to the queue
                    $content = ct_obfuscate_shortcode($content);
                    do_shortcode($content);
                    
                    // Collect all blocks shortcodes to generate fonts
                    $this->all_blocks_shortcodes .= $content;
                }
            }
        }
    }

	function save_page( $post_id ) {
	    global $wpdb;

        // Check if our nonce is set
        if ( ! isset( $_POST['ct_view_meta_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid
        if ( ! wp_verify_nonce( $_POST['ct_view_meta_box_nonce'], 'ct_view_meta_box' ) ) {
            return;
        }
        
        // Check if current user have access to use Oxygen builder
        if ( !oxygen_vsb_current_user_can_access() ) {
            return;
        }

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if( isset( $_REQUEST['action'] ) && isset( $_REQUEST['ct_oxygenberg_full_page_block']) ) {
			if( !empty($_REQUEST['ct_oxygenberg_full_page_block']) ) {
				if( get_post_meta( $post_id, '_ct_oxygenberg_full_page_block', true ) != '1' ) {
					update_post_meta( $post_id,'_ct_oxygenberg_full_page_block', true );
					remove_action( 'edit_post', array($this,'save_page') );
					$post = get_post( $post_id );
					update_post_meta( $post_id, '_ct_full_page_block_backup', $post->post_content );
					$post->post_content = '<!-- wp:oxygen-vsb/ovsb-' . $post->post_name . ' /-->';
					wp_update_post( $post );
				}
			} else {
				if( get_post_meta( $post_id, '_ct_oxygenberg_full_page_block', true ) == '1' ) {
					remove_action( 'edit_post', array($this,'save_page') );
					update_post_meta( $post_id,'_ct_oxygenberg_full_page_block', false );
					$content = get_post_meta( $post_id, '_ct_full_page_block_backup', true );
					$post = get_post( $post_id );
					$post->post_content = $content;
					wp_update_post( $post );
					delete_post_meta( $post_id, '_ct_full_page_block_backup' );
				}
			}
		}
	}

	function admin_footer_styles()
	{
		echo "<style>";
		// Remove the ct_css_styles function hooked at component-init.php because we are using the universal.css file instead
		remove_action("ct_footer_styles", "ct_css_styles");
		// Run non-universal styles
		do_action("ct_footer_styles");
		echo "</style>";

        if ($this->all_blocks_shortcodes) {
            add_web_font($this->all_blocks_shortcodes);
        }

		do_action('wp_footer');
	}

	function frontend_google_fonts($shortcodes)
	{
		if ($this->rendered_blocks_shortcodes) {
			$shortcodes .= $this->rendered_blocks_shortcodes;
		}

		return $shortcodes;
	}

	function render_gutenberg_block($attributes, $content = '')
	{
	    global $oxygen_vsb_css_files_to_load;
	    global $oxygen_vsb_components;
	    global $oxygen_is_gutenberg_block;
        global $oxygen_svg_icons_to_load;

        if (!is_array($oxygen_vsb_css_files_to_load)){
            $oxygen_vsb_css_files_to_load = array();
        }
		$found_reusable = null;

		if ($posts = get_posts(array(
			'name' => substr($attributes['reusable'], 5), //remove the "ovsb-" prefix from block name to convert it to post slug
			'post_type' => array('oxy_user_library', 'page'),
			'posts_per_page' => 1
		))) $found_reusable = $posts[0];

		if (!is_null($found_reusable)) {
            $oxygen_is_gutenberg_block = true;
		    $content = get_post_meta($found_reusable->ID, '_ct_builder_shortcodes', true);
			$this->rendered_blocks_shortcodes .= $content;

			$content = preg_replace_callback('/(\")(url|src|map_address|alt|background-image|oxycode|value|attachment_id)(\":\"[^\"]*)\[oxygen ([^\]]*)\]([^\"\[\s]*)/i', 'ct_obfuscate_oxy_url', $content);
			
			if (oxygen_doing_oxygen_elements()) {
				global $oxygen_doing_oxygen_elements;
				$oxygen_doing_oxygen_elements = false;
				$result = do_shortcode($content);
				$oxygen_doing_oxygen_elements = true;
			}
			else {
				$result = do_shortcode($content);
			}

			// Support for CSS cache feature
            if( !in_array($found_reusable->ID, $oxygen_vsb_css_files_to_load) ) $oxygen_vsb_css_files_to_load[] = $found_reusable->ID;

			while(strpos($result, '&lt;oxygenberg') > -1 || strpos($result, '<oxygenberg') > -1) {

                $attributes1 = $this::parse_oxygenberg_tags( $result );
                $attributes2 = $this::parse_oxygenberg_encoded_tags( $result );

				$attributes_found = array();
				if ( is_array($attributes1) && is_array($attributes2)) {
					$attributes_found = array_merge($attributes1, $attributes2);
				}

				foreach ($attributes_found as $attribute_key => $attribute) {
                    $result = str_replace($attribute['full_tag'], empty($attributes[$attribute['id']]) ? $attribute['default'] : $attributes[$attribute['id']], $result);
                    // remove oxygenberg class, not needed on frontend
                    $result = str_replace('oxygenberg-'.$attribute['id'], '', $result);

                    if( !substr_compare($attribute['id'], '_icon', -5 ) && isset( $attributes[$attribute['id']] )){
                        $oxygen_svg_icons_to_load[] = $attributes[$attribute['id']];
                    }

                    if( !substr_compare($attribute['id'], '_background', -11 )){
                        $result = str_replace( 'style="background-image:auto"', '', $result);
                    }
                }

			}

			$result = str_replace("</oxygenberg>", "", $result);

            $oxygen_is_gutenberg_block = false;

			return $result;
		}
	}

	static function parse_oxygenberg_tags( $content )
    {
		$re = '/<oxygenberg.*?>/';
		$matches = null;
		preg_match_all($re, $content, $matches, PREG_SET_ORDER, 0);

		$oxygenberg_tags = array();
		$attributes_found = array();

		foreach ($matches as $m) {
			$oxygenberg_tags[] = $m[0];
		}

		foreach ($oxygenberg_tags as $oxygenberg_tag) {
			$att_regex = '/(\S+)=["\']([^\s]*?)["\']/';
			$att = array();
			$att_id = '';
			preg_match_all($att_regex, $oxygenberg_tag, $matches, PREG_SET_ORDER, 0);

			foreach ($matches as $match) {
				$att[$match[1]] = base64_decode($match[2]);
				if ($match[1] == 'id') $att_id = $att['id'];
			}
			$att['full_tag'] = $oxygenberg_tag;
			$attributes_found[] = $att;
		}

		return $attributes_found;
    }

	static function parse_oxygenberg_encoded_tags( $content )
    {
		$re = '/&lt;oxygenberg.*?&gt;/';
		$matches = null;
		preg_match_all($re, $content, $matches, PREG_SET_ORDER, 0);

		$oxygenberg_tags = array();
		$attributes_found = array();

		foreach ($matches as $m) {
			$oxygenberg_tags[] = $m[0];
		}

		foreach ($oxygenberg_tags as $oxygenberg_tag) {
			$att_regex = '/(\S+)=["\']([^\s]*?)["\']/';
			$att = array();
			$att_id = '';
			preg_match_all($att_regex, str_replace("%20", " ", $oxygenberg_tag), $matches, PREG_SET_ORDER, 0);

			foreach ($matches as $match) {
				$att[$match[1]] = base64_decode($match[2]);
			}
			$att['full_tag'] = $oxygenberg_tag;
			$attributes_found[] = $att;
		}


		return $attributes_found;
    }

	function generate_gutenberg_script()
	{

		if (!is_user_logged_in()) {
			die( 'Security check' );
		}
	
		$nonce  	= $_REQUEST['nonce'];
		$post_id 	= $_REQUEST['post_id'];
	
		// check nonce
		if ( ! wp_verify_nonce( $nonce, 'oxygen-nonce-' . $post_id ) ) {
			die( 'Security check' ); 
		}

		// Disable AOS inside gutenberg
		remove_action("oxygen_vsb_component_attr", array($GLOBALS['oxygen_vsb_aos'], "attributes") );

		global $oxygen_preview_post_id;
		$post_id = isset($_REQUEST["post"]) ? $_REQUEST["post"] : 0;
		$post_type = "post";
		$full_page_block = false;
		if( $post_id > 0 ) {
            $post = get_post( $post_id );
            if( $post ) $post_type = $post->post_type;
			$full_page_block = get_post_meta( $post_id, '_ct_oxygenberg_full_page_block', true );
		} else if( isset($_REQUEST['post_type']) ) {
		    $post_type = $_REQUEST['post_type'];
		}
		$full_page_block = $full_page_block === '1' ? true : false;

		$oxygen_preview_post_id = $post_id;

		header('Content-Type: application/javascript');
		foreach ($this->blocks as $block) {
		    $content = get_post_meta($block->ID, '_ct_builder_shortcodes', true);
			$content = preg_replace_callback('/(\")(url|src|map_address|alt|background-image|oxycode|value)(\":\"[^\"]*)\[oxygen ([^\]]*)\]([^\"\[\s]*)/i', 'ct_obfuscate_oxy_url', $content);
			?>registerOxygenBlock( '<?php echo base64_encode(do_shortcode($content)); ?>', '<?php echo 'ovsb-'.$block->post_name; ?>', <?php echo json_encode($block->post_title); ?>, <?php echo $block->post_type == 'page' ? 'true' : 'false'; ?> );<?php
			echo "\n";
		}
		echo "window.oxyBuilderUrl = '". get_site_url() . "?ct_builder=true';\n";
		echo "window.ctBuilderAjaxUrl = '" . admin_url( 'admin-ajax.php' ) . "';\n";
		$nonce = wp_create_nonce( 'oxygen-nonce-' . $post_id );
        echo "window.ctBuilderNonce = '" . $nonce . "';\n";
        echo "window.ctBuilderPost = '" . $post_id . "';\n";
		echo "window.ctBuilderPostType = '" . $post_type . "';\n";
		echo "window.ctBuilderFullPageBlock = " . ( $full_page_block ? 'true' : 'false' ) . ";\n";
		echo "window.ctBuilderFullPageBlockName = '" . ( $post_id > 0 ? $post->post_name : '' ) . "';\n";
        echo "window.oxygen_vsb_current_user_can_access = " . (oxygen_vsb_current_user_can_access() ? "true" : "false") . ";\n";
		exit;
	}

	function remove_oxygenberg_metadata($output, $tag)
    {
        // shortcodes rendered from the render_gutenberg_block (frontend) or generate_gutenberg_script (gutenberg) functions should be left decorated
		$dbt = debug_backtrace();
		foreach ($dbt as $debug_item) {
            if( isset($debug_item['function']) && ( $debug_item['function'] == 'render_gutenberg_block' || $debug_item['function'] == 'generate_gutenberg_script' ) ){
				// SRCSET won't be valid if user changed any image within gutenberg
                if( $tag == 'ct_image' ){
                    $output = str_replace( ' srcset=', ' srcset-disabled=', $output );
				}
                return $output;
			}
		}

        // Regular shortcodes, rendered on frontend, out of any gutenberg block.
        // If an oxygenberg decoration tag with an attribute description is found, replace it with it's default value as set in the Oxygen editor.
		$attributes1 = $this::parse_oxygenberg_tags( $output );
        $attributes2 = $this::parse_oxygenberg_encoded_tags( $output );
		$attributes_found = array();
		if ( is_array($attributes1) && is_array($attributes2)) {
			$attributes_found = array_merge($attributes1, $attributes2);
		}
        foreach ($attributes_found as $attribute_key => $attribute) {
            $output = str_replace($attribute['full_tag'], $attribute['default'], $output);
            // remove oxygenberg class, not needed on frontend
            $output = str_replace('oxygenberg-'.$attribute['id'], '', $output);
        }

        // Process shortcodes on dynamic data when not inside a gutenberg block
        if( $tag == "ct_span" ) {
            ob_start();
			$output = do_shortcode( $output ); //Shortcodes will be rendered later by Oxygen Gutenberg plugin, if activated
			$shortcode_output = ob_get_clean();
			$output .= $shortcode_output;
        }

        if( $tag == "ct_section" ) {
        	$output = str_replace( 'style="background-image:auto"', '', $output);
        }

		return $output;
    }

    function add_full_page_template_checkbox() {
    	global $post;
    	if(empty($post) || $post->post_type != 'page') return;
    	$full_page_block = get_post_meta( $post->ID, '_ct_oxygenberg_full_page_block', true );
    	$full_page_block = $full_page_block === '1' ? true : false;
    	?>
        <input type="hidden" name="ct_oxygenberg_full_page_block" value="">
    	<label for="ct_oxygenberg_full_page_block"><input type="checkbox" name="ct_oxygenberg_full_page_block" id="ct_oxygenberg_full_page_block" <?php echo ( $full_page_block ? 'checked' : '' ); ?>>Make this full page editable in Gutenberg<div class="oxy-tooltip"><div class="oxy-tooltip-text">With this checkbox enabled, all your gutenberg content will be replaced with a single block containing the full Oxygen-designed page.</div></div></label>
    	<?php
    }

	static function decorate_attribute( $options, $default, $type, $sub_item = "" )
    {
        if( !Oxygen_Gutenberg::$running ) return $default;
        $sub_item = empty($sub_item) ? "" : '_' . $sub_item;
		$gutenberg_data = "<oxygenberg ";
		$gutenberg_data .= "id='".base64_encode( $options['selector'] . $sub_item."_".$type)."' ";
		$gutenberg_data .= "default='".base64_encode( $default )."' ";
		$gutenberg_data .= "type='".base64_encode( $type )."' ";
		$gutenberg_data .= isset( $options['gutenberg_placeholder'] ) ? ( "gutenberg-placeholder='".$options['gutenberg_placeholder']."' " ) : "";
		$gutenberg_data .= "/>";
		return $gutenberg_data;
    }

    static function install() {
    	update_option( 'oxygen_vsb_enable_connection', "true" );
    }

}

new Oxygen_Gutenberg();
