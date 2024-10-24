<p align="center"><a href="https://smartystudio.net" target="_blank"><img src="https://smartystudio.net/wp-content/uploads/2023/06/smarty-green-logo-small.png" width="100" alt="SmartyStudio Logo"></a></p>

# Smarty Studio - FAQ On Posts for WordPress

[![Licence](https://img.shields.io/badge/LICENSE-GPL2.0+-blue)](./LICENSE)

- Developed by: [Smarty Studio](https://smartystudio.net) | [Martin Nestorov](https://github.com/mnestorov)
- Plugin URI: https://github.com/mnestorov/smarty-faq-on-posts-for-wp

## Overview

The **Smarty Studio - FAQ on Posts for WordPress** plugin allows you to easily add, manage, and display FAQs (Frequently Asked Questions) on any WordPress post. With an intuitive admin interface, custom styling, and Schema.org support for rich results in search engines, this plugin is perfect for improving user engagement and SEO performance.

## Description

This plugin provides a seamless way to integrate FAQs into your WordPress posts. You can define custom questions and answers directly from the post editor, allowing for easy management of frequently asked questions that are specific to each post. The FAQs are displayed in a visually appealing, collapsible format, enhancing user experience while providing search engine-friendly structured data.

Additionally, the plugin supports custom FAQ section titles and organizes the FAQ entries using the official [Schema.org FAQPage](https://schema.org/FAQPage) schema, making your content eligible for rich snippets in Google search results.

## Features

- Add FAQs to individual posts through the WordPress admin panel.
- Customizable FAQ section titles for each post.
- Collapsible FAQ sections with toggle buttons.
- Schema.org markup for better SEO and rich results in search engines.
- Easy-to-use admin interface for adding and managing FAQs.
- No coding required for adding questions and answers.
- Fully responsive and customizable layout.

## Installation

1. Download or clone this repository into your WordPress plugin directory (`/wp-content/plugins/`).
2. Navigate to the WordPress admin dashboard, go to **Plugins** > **Installed Plugins**.
3. Find "Smarty Studio - FAQ on Posts" and click **Activate**.
4. Once activated, you will see the FAQ meta box on the post editor screen for adding FAQs.

Alternatively, you can install the plugin through the WordPress admin:

1. Navigate to **Plugins** > **Add New**.
2. Click on **Upload Plugin**.
3. Choose the `smarty-faq-on-posts-for-wp.zip` file and click **Install Now**.
4. Once installed, click **Activate**.

## Usage

1. Navigate to any post editor (or create a new post) in WordPress.
2. You will find a meta box titled "FAQs" on the editor screen.
3. Add your questions and answers using the intuitive fields.
4. Optionally, customize the title of your FAQ section.
5. Once you publish or update the post, the FAQ section will be displayed on the front-end.

The FAQs are displayed as a collapsible list, and the styling is customizable via the included CSS files (`smarty-fop-public.css`).

## Error Handling and Debugging

If you encounter any issues with the plugin:

1. Ensure that your WordPress installation is up to date.
2. Check if the plugin is correctly activated in the **Plugins** section of your WordPress admin panel.
3. Review your browser console for any JavaScript errors, and ensure that jQuery is properly loaded.
4. Enable `WP_DEBUG` in your `wp-config.php` to display any PHP errors.

For further assistance, feel free to open an issue on the [GitHub repository](https://github.com/mnestorov/smarty-faq-on-posts-for-wp).

## Frequently Asked Questions

**1. How do I add a custom FAQ section title?**

In the post editor, simply fill out the "FAQ Section Title" field in the FAQ meta box. This title will be displayed above the FAQ section.

**2. Can I customize the look and feel of the FAQ section?**

Yes! You can modify the styles by editing the `smarty-fop-public.css` file in the plugin’s `css/` directory, or you can add custom CSS via your theme’s stylesheet.

**3. Is the plugin compatible with page builders?**

Yes, the FAQ section should work on most page builders as it integrates directly into the post content.

**4. Does the plugin support FAQ schema for SEO?**

Absolutely! The plugin automatically includes Schema.org markup for FAQs, making your content eligible for rich results in Google search.

## Changelog

For a detailed list of changes and updates made to this project, please refer to our [Changelog](./CHANGELOG.md).

---

## License

This project is released under the [GPL-2.0+ License](http://www.gnu.org/licenses/gpl-2.0.txt).
