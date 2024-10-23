<?php
/**
 * Plugin Name: Smarty - FAQ manager for WordPress
 * Plugin URI:  https://github.net/mnestorov/smarty-faq-manager-for-wp
 * Description: Generates google product and product review feeds for Google Merchant Center.
 * Version:     1.0.0
 * Author:      Martin Nestorov
 * Author URI:  https://github.net/mnestorov
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: smarty-faq-manager-for-wp
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

// Add a meta box for FAQs in the post edit screen.
function mn_add_faq_meta_box() {
    add_meta_box(
        'mn_faq_meta_box', 
        'FAQs', 
        'mn_faq_meta_box_callback', 
        'post', 
        'normal', 
        'high'
    );
}
add_action('add_meta_boxes', 'mn_add_faq_meta_box');

// Callback function to display and manage FAQs in the post edit screen.
function mn_faq_meta_box_callback($post) {
    // Nonce field for security
    wp_nonce_field('mn_save_faq_meta_box', 'mn_faq_meta_box_nonce');

    // Retrieve existing FAQs and custom FAQ title
    $faqs = get_post_meta($post->ID, '_mn_faqs', true);
    $faq_section_title = get_post_meta($post->ID, '_mn_faq_section_title', true);

    // Custom FAQ Section Title
    echo '<p><label for="mn_faq_section_title">FAQ Section Title:</label>';
    echo '<input type="text" name="mn_faq_section_title" id="mn_faq_section_title" value="' . esc_attr($faq_section_title) . '" style="width: 100%;"></p>';

    // Display existing FAQs
    if (!empty($faqs)) {
        foreach ($faqs as $index => $faq) {
            ?>
            <div class="mn-custom-faq <?php echo ($index % 2 == 0) ? 'even' : 'odd'; ?>" data-index="<?php echo $index; ?>">
                <div class="mn-faq-header">
                    <h4>FAQ <?php echo ($index + 1); ?> - <span class="faq-title"><?php echo esc_html($faq['question']); ?></span></h4>
                    <button type="button" class="button button-secondary mn-toggle-faq">Toggle</button>
                </div>
                <div class="mn-faq-content" style="display: none;">
                    <p>
                        <label>Question:</label><br>
                        <input type="text" name="_mn_faqs[<?php echo $index; ?>][question]" value="<?php echo esc_attr($faq['question']); ?>" style="width: 100%;">
                    </p>
                    <p>
                        <label>Answer:</label><br>
                        <textarea name="_mn_faqs[<?php echo $index; ?>][answer]" style="width: 100%;"><?php echo esc_textarea($faq['answer']); ?></textarea>
                    </p>
                    <input type="hidden" name="_mn_faqs[<?php echo $index; ?>][delete]" value="0" class="mn-delete-input">
                    <button type="button" class="button button-secondary mn-remove-faq-button">Remove FAQ</button>
                </div>
            </div>
            <?php
        }
    }
    ?>
    <div id="mn_faqs_container"></div>
    <button type="button" id="mn_add_faq_button" class="button button-primary">Add FAQ</button>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var faqIndex = <?php echo !empty($faqs) ? count($faqs) : 0; ?>;

        $('#mn_add_faq_button').on('click', function() {
            var newFAQ = `
            <div class="mn-custom-faq ` + (faqIndex % 2 == 0 ? 'even' : 'odd') + `">
                <div class="mn-faq-header">
                    <h4>FAQ ` + (faqIndex + 1) + ` - <span class="faq-title">New FAQ</span></h4>
                    <button type="button" class="button button-secondary mn-toggle-faq">Toggle</button>
                </div>
                <div class="mn-faq-content" style="display: none;">
                    <p>
                        <label>Question:</label><br>
                        <input type="text" name="_mn_faqs[` + faqIndex + `][question]" style="width: 100%;">
                    </p>
                    <p>
                        <label>Answer:</label><br>
                        <textarea name="_mn_faqs[` + faqIndex + `][answer]" style="width: 100%;"></textarea>
                    </p>
                    <input type="hidden" name="_mn_faqs[` + faqIndex + `][delete]" value="0" class="mn-delete-input">
                    <button type="button" class="button button-secondary mn-remove-faq-button">Remove FAQ</button>
                </div>
            </div>`;

            $('#mn_faqs_container').append(newFAQ);
            faqIndex++;
        });

        $(document).on('click', '.mn-toggle-faq', function() {
            $(this).closest('.mn-custom-faq').find('.mn-faq-content').slideToggle();
        });

        $(document).on('click', '.mn-remove-faq-button', function() {
            var faqDiv = $(this).closest('.mn-custom-faq');
            var deleteInput = faqDiv.find('.mn-delete-input');

            if (faqDiv.data('index') !== undefined) {
                deleteInput.val('1');
                faqDiv.hide();
            } else {
                faqDiv.remove();
            }
        });
    });
    </script>
    <style>
        .mn-custom-faq {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .mn-custom-faq.even { background-color: #fff; }
        .mn-faq-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        .mn-faq-content {
            display: none;
        }
        .mn-faq-content p {
            margin-top: 10px;
        }
    </style>
    <?php
}

// Save FAQs and custom title when the post is saved
function mn_save_faq_meta_box($post_id) {
    if (!isset($_POST['mn_faq_meta_box_nonce']) || !wp_verify_nonce($_POST['mn_faq_meta_box_nonce'], 'mn_save_faq_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $new_faqs = array();

    if (isset($_POST['_mn_faqs'])) {
        foreach ($_POST['_mn_faqs'] as $faq) {
            if (isset($faq['delete']) && $faq['delete'] == '0') {
                $new_faqs[] = $faq;
            }
        }
        update_post_meta($post_id, '_mn_faqs', $new_faqs);
    }

    if (isset($_POST['mn_faq_section_title'])) {
        update_post_meta($post_id, '_mn_faq_section_title', sanitize_text_field($_POST['mn_faq_section_title']));
    }
}
add_action('save_post', 'mn_save_faq_meta_box');

// Display FAQs on the front end
function mn_display_faqs($content) {
    if (is_singular('post')) {
        global $post;

        // Retrieve saved FAQs and custom title
        $faqs = get_post_meta($post->ID, '_mn_faqs', true);
		
        $faq_section_title = get_post_meta($post->ID, '_mn_faq_section_title', true);

        if (!empty($faqs)) {
            // Custom title or default title
            $content .= '<div class="mn-faqs">';
            if (!empty($faq_section_title)) {
                $content .= '<h3 class="mn-faq-title">' . esc_html($faq_section_title) . '</h3>';
            } else {
                $content .= '<h3 class="mn-faq-title">FAQs</h3>';
            }

            $faq_schema = array(
                "@context" => "https://schema.org",
                "@type" => "FAQPage",
                "mainEntity" => array()
            );

            foreach ($faqs as $faq) {
                if (!empty($faq['question']) && !empty($faq['answer'])) {
                    // Display each FAQ in the front end
                    $content .= '
                    <div class="mn-faq-item">
                        <div class="mn-faq-question-wrapper">
                            <div class="mn-faq-icon dashicons dashicons-plus"></div>
                            <h4 class="mn-faq-question">' . esc_html($faq['question']) . '</h4>
                        </div>
                        <div class="mn-faq-answer">' . wp_kses_post($faq['answer']) . '</div>
                    </div>';

                    // Add each FAQ to the Schema.org structure
                    $faq_schema['mainEntity'][] = array(
                        "@type" => "Question",
                        "name" => esc_html($faq['question']),
                        "acceptedAnswer" => array(
                            "@type" => "Answer",
                            "text" => wp_kses_post($faq['answer'])
                        )
                    );
                }
            }
            $content .= '</div>';

            // Output the FAQ schema in the <head> or body section
            add_action('wp_footer', function() use ($faq_schema) {
                echo '<script type="application/ld+json">' . json_encode($faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
            });

            // Add custom styles for the FAQ section
            $content .= '
            <style>
                .mn-faqs {
                    margin: 60px 0;
                    padding: 20px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                    background-color: #ffffff;
                }
                h3.mn-faq-title {
					font-family: "Alethia Pro", Sans-serif;
                    font-size: 26px;
					text-align: center;
                    margin-bottom: 20px;
                    color: #222222;
                }
                .mn-faq-item {
                    margin-bottom: 15px;
                    border-radius: 5px;
                    background-color: #f9f9f9;
                }
				.mn-faq-question-wrapper {
                    display: flex;
                    align-items: center;
                    cursor: pointer;
                    padding: 10px;
                }
                .mn-faq-icon {
                    margin-right: 10px;
                    color: inherit;
					transition: color 0.3s;
                }
                .mn-faq-question {
                    font-weight: bold;
                    cursor: pointer;
                    font-size: 16px;
                    transition: color 0.3s;
                }
				h4.mn-faq-question {
                    font-size: 20px;
					font-weight: normal;
					color: inherit;
					margin: 0;
                }
                .mn-faq-question-wrapper:hover .mn-faq-question,
                .mn-faq-question-wrapper:hover .mn-faq-icon {
                    color: #d2b885;
                }
                .mn-faq-answer {
                    font-size: 14px;
                    line-height: 1.5;
                    color: #222222;
					padding: 0 40px 10px 40px;
                    display: none;
                }
                .mn-faq-answer p {
                    margin-bottom: 10px;
                }
            </style>
            ';

            // jQuery to toggle FAQ answers
           	$content .= '
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                $(".mn-faq-question-wrapper").on("click", function() {
                    var icon = $(this).find(".mn-faq-icon");
                    $(this).next(".mn-faq-answer").slideToggle();
                    if (icon.hasClass("dashicons-plus")) {
                        icon.removeClass("dashicons-plus").addClass("dashicons-minus");
                    } else {
                        icon.removeClass("dashicons-minus").addClass("dashicons-plus");
                    }
                });
            });
            </script>
            ';
        }
    }

    return $content;
}
add_filter('the_content', 'mn_display_faqs');


// Add basic styles for the FAQs
function mn_faq_admin_styles() {
    echo '
    <style>
        .mn-faq-item {
            margin-bottom: 15px;
        }
        .mn-faq-question {
            cursor: pointer;
            font-weight: bold;
            color: #0073aa;
        }
        .mn-faq-answer {
            margin-top: 10px;
        }
    </style>
    ';
}
add_action('wp_head', 'mn_faq_admin_styles');