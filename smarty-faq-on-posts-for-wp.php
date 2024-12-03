<?php
/**
 * Plugin Name: SM - FAQ on Posts for WordPress
 * Plugin URI:  https://github.com/mnestorov/smarty-faq-on-posts-for-wp
 * Description: This plugin allows you to easily add and manage FAQs within individual WordPress posts.
 * Version:     1.0.0
 * Author:      Martin Nestorov
 * Author URI:  https://github.com/mnestorov
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: smarty-faq-on-post-for-wp
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!function_exists('smarty_fop_load_textdomain')) {
	/**
	 * Load plugin textdomain for translations.
	 */
	function smarty_fop_load_textdomain() {
		load_plugin_textdomain('smarty-faq-on-post-for-wp', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	add_action('init', 'smarty_fop_load_textdomain');
}

if (!function_exists('smarty_fop_enqueue_public_assets')) {
	/**
	 * Enqueue public styles and scripts.
	 */
	function smarty_fop_enqueue_public_assets() {
		if (is_singular('post')) {
			wp_enqueue_style('smarty-fop-public-css', plugin_dir_url(__FILE__) . 'css/smarty-fop-public.css', array(), '1.0.0');
			wp_enqueue_script('smarty-fop-public-js', plugin_dir_url(__FILE__) . 'js/smarty-fop-public.js', array('jquery'), '1.0.0', true);
		}
	}
	add_action('wp_enqueue_scripts', 'smarty_fop_enqueue_public_assets');
}

if (!function_exists('smarty_fop_enqueue_admin_assets')) {
	/**
	 * Enqueue admin styles and scripts.
	 */
	function smarty_fop_enqueue_admin_assets() {
		global $pagenow;

		if ($pagenow == 'post.php' || $pagenow == 'post-new.php') {
			wp_enqueue_style('smarty-fop-admin-css', plugin_dir_url(__FILE__) . 'css/smarty-fop-admin.css', array(), '1.0.0');
			wp_enqueue_script('smarty-fop-admin-js', plugin_dir_url(__FILE__) . 'js/smarty-fop-admin.js', array('jquery'), '1.0.0', true);
		}
	}
	add_action('admin_enqueue_scripts', 'smarty_fop_enqueue_admin_assets');
}

if (!function_exists('smarty_fop_add_faq_meta_box')) {
	/**
	 * Add a meta box for FAQs in the post edit screen.
	 */
	function smarty_fop_add_faq_meta_box() {
		add_meta_box(
			'smarty_faq_meta_box', 
			__('FAQs', 'smarty-faq-on-post-for-wp'),
			'smarty_faq_meta_box_callback', 
			'post', 
			'normal', 
			'high'
		);
	}
	add_action('add_meta_boxes', 'smarty_fop_add_faq_meta_box');
}

if (!function_exists('smarty_fop_faq_meta_box_callback')) {
	/**
	 * Callback function to display and manage FAQs in the post edit screen.
	 */
	function smarty_fop_faq_meta_box_callback($post) {
		// Nonce field for security
		wp_nonce_field('smarty_save_faq_meta_box', 'smarty_faq_meta_box_nonce');

		// Retrieve existing FAQs and custom FAQ title
		$faqs = get_post_meta($post->ID, '_smarty_faqs', true);
		$faq_section_title = get_post_meta($post->ID, '_smarty_faq_section_title', true);

		// Custom FAQ Section Title
		echo '<p><label for="smarty_faq_section_title">' . __('FAQ Section Title:', 'smarty-faq-on-post-for-wp') . '</label>';
		echo '<input type="text" name="smarty_faq_section_title" id="smarty_faq_section_title" value="' . esc_attr($faq_section_title) . '" style="width: 100%;"></p>';

		// Display existing FAQs
		if (!empty($faqs)) {
			foreach ($faqs as $index => $faq) {
				?>
				<div class="smarty-custom-faq <?php echo ($index % 2 == 0) ? 'even' : 'odd'; ?>" data-index="<?php echo $index; ?>">
					<div class="smarty-faq-header">
						<h4><?php printf(__('FAQ %d -', 'smarty-faq-on-post-for-wp'), ($index + 1)); ?> <span class="faq-title"><?php echo esc_html($faq['question']); ?></span></h4>
						<button type="button" class="button button-secondary smarty-toggle-faq"><?php _e('Toggle', 'smarty-faq-on-post-for-wp'); ?></button>
					</div>
					<div class="smarty-faq-content" style="display: none;">
						<p>
							<label><?php _e('Question:', 'smarty-faq-on-post-for-wp'); ?></label><br>
							<input type="text" name="_smarty_faqs[<?php echo $index; ?>][question]" value="<?php echo esc_attr($faq['question']); ?>" style="width: 100%;">
						</p>
						<p>
							<label><?php _e('Answer:', 'smarty-faq-on-post-for-wp'); ?></label><br>
							<textarea name="_smarty_faqs[<?php echo $index; ?>][answer]" style="width: 100%;"><?php echo esc_textarea($faq['answer']); ?></textarea>
						</p>
						<input type="hidden" name="_smarty_faqs[<?php echo $index; ?>][delete]" value="0" class="smarty-delete-input">
						<button type="button" class="button button-secondary smarty-remove-faq-button"><?php _e('Remove FAQ', 'smarty-faq-on-post-for-wp'); ?></button>
					</div>
				</div>
				<?php
			}
		}
		?>
		<div id="smarty_faqs_container"></div>
		<button type="button" id="smarty_add_faq_button" class="button button-primary"><?php _e('Add FAQ', 'smarty-faq-on-post-for-wp'); ?></button><?php
	}
}

if (!function_exists('smarty_fop_save_faq_meta_box')) {
	/**
	 * Save FAQs and custom title when the post is saved.
	 */
	function smarty_fop_save_faq_meta_box($post_id) {
		if (!isset($_POST['smarty_faq_meta_box_nonce']) || !wp_verify_nonce($_POST['smarty_faq_meta_box_nonce'], 'smarty_save_faq_meta_box')) return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!current_user_can('edit_post', $post_id)) return;

		$new_faqs = array();

		if (isset($_POST['_smarty_faqs'])) {
			foreach ($_POST['_smarty_faqs'] as $faq) {
				if (isset($faq['delete']) && $faq['delete'] == '0') {
					$new_faqs[] = $faq;
				}
			}
			update_post_meta($post_id, '_smarty_faqs', $new_faqs);
		}

		if (isset($_POST['smarty_faq_section_title'])) {
			update_post_meta($post_id, '_smarty_faq_section_title', sanitize_text_field($_POST['smarty_faq_section_title']));
		}
	}
	add_action('save_post', 'smarty_fop_save_faq_meta_box');
}

if (!function_exists('smarty_fop_display_faqs')) {
	/**
	 * Display FAQs on the front end.
	 */
	function smarty_fop_display_faqs($content) {
		if (is_singular('post')) {
			global $post;

			// Retrieve saved FAQs and custom title
			$faqs = get_post_meta($post->ID, '_smarty_faqs', true);

			$faq_section_title = get_post_meta($post->ID, '_smarty_faq_section_title', true);

			if (!empty($faqs)) {
				// Custom title or default title
				$content .= '<div class="smarty-faqs">';
				if (!empty($faq_section_title)) {
					$content .= '<h3 class="smarty-faq-title">' . esc_html($faq_section_title) . '</h3>';
				} else {
					$content .= '<h3 class="smarty-faq-title">' . __('FAQs', 'smarty-faq-on-post-for-wp') . '</h3>';
				}

				$faq_schema = array(
					"@context"   => "https://schema.org",
					"@type"      => "FAQPage",
					"mainEntity" => array()
				);

				foreach ($faqs as $faq) {
					if (!empty($faq['question']) && !empty($faq['answer'])) {
						// Display each FAQ in the front end
						$content .= '
						<div class="smarty-faq-item">
							<div class="smarty-faq-question-wrapper">
								<div class="smarty-faq-icon dashicons dashicons-plus"></div>
								<h4 class="smarty-faq-question">' . esc_html($faq['question']) . '</h4>
							</div>
							<div class="smarty-faq-answer">' . wp_kses_post($faq['answer']) . '</div>
						</div>';

						// Add each FAQ to the Schema.org structure
						$faq_schema['mainEntity'][] = array(
							"@type" => "Question",
							"name"  => esc_html($faq['question']),
							"acceptedAnswer" => array(
								"@type" => "Answer",
								"text"  => wp_kses_post($faq['answer'])
							)
						);
					}
				}
				$content .= '</div>';

				// Output the FAQ schema in the <head> or body section
				add_action('wp_footer', function() use ($faq_schema) {
					echo '<script type="application/ld+json">' . json_encode($faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
				});
			}
		}

		return $content;
	}
	add_filter('the_content', 'smarty_fop_display_faqs');
}