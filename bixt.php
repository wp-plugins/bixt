<?php
/*
  Plugin Name: Bixt - Turns Keyword Phrases into Your Affiliate Links
  Plugin URI: http://club.orbisius.com/products/wordpress-plugins/bixt/
  Description: Bixt replaces keywords or keyword phrases with affiliate links that you define per keyword.
  Version: 1.0.5
  Author: Svetoslav Marinov (Slavi)
  Author URI: http://orbisius.com
 */

/*  Copyright 2012-2050 Svetoslav Marinov (Slavi) <slavi@orbisius.com>

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Set up plugin
add_action('init', 'bixt_init');
add_action('wp_head', 'bixt_insert_code');
add_action('admin_init', 'bixt_admin_init');
add_action('admin_menu', 'bixt_setup_admin');
add_action('network_admin_menu', 'bixt_setup_admin');
add_action('wp_footer', 'bixt_add_plugin_credits', 1000); // be the last in the footer

register_activation_hook( __FILE__, 'bixt_on_activate' );

/**
 *
 */
function bixt_init() {
    //bixt_on_activate();
}

/**
 * Tries to guess the domain name and sets the time when the plugin was installed.
 */
function bixt_on_activate() {
    $opts = bixt_get_options();

    // Let's set the activation time so we can hide the notice in the plugins area.
    if (empty($opts['setup_time'])) {
        $opts['setup_time'] = time();

        if (empty($opts['domain'])) {
            $dom = $_SERVER['HTTP_HOST'];
            $dom = preg_replace('#^(?:ww+\d*|blog|dev|static|s\d*|img\d*)\.#si', '', $dom);
            $opts['domain'] = $dom;
        }

        bixt_set_options($opts);
    }
}

/**
 * Inserts the async JS code in the header only if the plugin is active and domain is set.
 */
function bixt_insert_code() {
    $opts = bixt_get_options();

    if ( defined( 'DOING_AJAX' ) || empty($opts['status']) 
			|| empty($opts['user_id']) || empty($opts['domain']) ) {
        echo "<!-- Bixt not enabled, User ID or domain weren't set or it's ajax -->\n";
        return ;
    }

    $user_id_esc = esc_attr($opts['user_id']);
    $dom_esc = esc_attr($opts['domain']);
    $env = empty($_SERVER['DEV_ENV']) ? 'live' : 'dev';
    $env_esc = esc_attr($env);
    $api_key = sha1('bixt-' . $opts['user_id'] . '-' . $dom_esc);

    if (!empty($opts['use_hosted'])) { // to be added as an option or not?
        $ssl_url = 'https://ssl.orbisius.com/apps/bixt/public/2.0/';
        $url = 'http://cdn.bixt.net/2.0/';
    } else {
        $suffix = empty($_SERVER['DEV_ENV']) ? '.min' : '';
        $js_url = plugins_url("/assets/2.0/app$suffix.js", __FILE__);

        // let's add file modif.time.
        $js_url .= '?m=' . filemtime( plugin_dir_path( __FILE__ ) . "/assets/2.0/app$suffix.js" );
        $js_ssl_url = $js_url;
    }

    $bixt_cfg_json = json_encode(array(
        'user_id' =>  $user_id_esc,
        'domain' =>  $dom_esc,
        'env' =>  $env_esc,
        'api_key' =>  $api_key,
        'branding' => !empty($opts['branding']),
    ));

    $code = <<<CODE_EOF
    <!-- Start of Bixt.net - Words2AffLinks Bixt Widget 2.0 (Async Load) -->
    <script type="text/javascript">//<![CDATA[
        var bixt_cfg = $bixt_cfg_json;

        (function() {
            function bixt_async_load() {
                var s = document.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.src = ('https:' == document.location.protocol ? '$js_ssl_url' : '$js_url');
                var x = document.getElementsByTagName('script')[0];
                x.parentNode.insertBefore(s, x);
            }

            if (window.attachEvent) {
                window.attachEvent('onload', bixt_async_load);
            } else {
                window.addEventListener('load', bixt_async_load, false);
            }
        })();
    //]]></script>
    <!-- End of Bixt.net - Words2AffLinks Bixt Widget 2.0 (Async Load) -->
CODE_EOF;

    echo $code;
}

/**
 * @package Bixt
 * @since 1.0
 */
function bixt_admin_init() {
    bixt_register_settings();
}

/**
 * Sets the setting variables
 */
function bixt_register_settings() { // whitelist options
    register_setting('bixt_settings', 'bixt_options', 'bixt_validate_settings');
}

/**
 * This is called by WP after the user hits the submit button.
 * The variables are trimmed first and then passed to the who ever wantsto filter them.
 * @param array the entered data from the settings page.
 * @return array the modified input array
 */
function bixt_validate_settings($input) { // whitelist options
    $input = array_map('trim', $input);

    // let extensions do their thing
    $input_filtered = apply_filters('bixt_ext_filter_settings', $input);

    // did the extension break stuff?
    $input = is_array($input_filtered) ? $input_filtered : $input;

    return $input;
}

/**
 * Retrieves the plugin options. It inserts some defaults.
 * The saving is handled by the settings page. Basically, we submit to WP and it takes
 * care of the saving.
 *
 * @return array
 */
function bixt_get_options() {
    $defaults = array(
        'status' => 0,
        'branding' => 1,
        'setup_time' => '',
        'user_id' => '',
        'domain' => '',
    );

    $opts = get_option('bixt_options');

    $opts = (array) $opts;
    $opts = array_merge($defaults, $opts);

    return $opts;
}

/**
* Updates options but it merges them unless $override is set to 1
* that way we could just update one variable of the settings.
*/
function bixt_set_options($opts = array(), $override = 0) {
    if (!$override) {
        $old_opts = bixt_get_options();
        $opts = array_merge($old_opts, $opts);
        array_unique($opts); // sometimes some false values get added ?!?
    }

    update_option('bixt_options', $opts);

    return $opts;
}

/**
 * Set up administration
 *
 * @package Bixt
 * @since 0.1
 */
function bixt_setup_admin() {
    add_options_page('Bixt', 'Bixt', 'manage_options', 'bixt_settings_page', 'bixt_settings_page');

    // when plugins are show add a settings link near my plugin for a quick access to the settings page.
    add_filter('plugin_action_links', 'bixt_add_plugin_settings_link', 10, 2);
}

// Add the ? settings link in Plugins page very good
function bixt_add_plugin_settings_link($links, $file) {
    if ($file == plugin_basename(__FILE__)) {
        $link = bixt_util::get_settings_link();
        $link_html = "<a href='$link'>Settings</a>";
        array_unshift($links, $link_html);
    }

    return $links;
}

// Generates Options for the plugin
function bixt_settings_page() {
    $opts = bixt_get_options();
    ?>

    <div class="wrap bixt_container">

        <div id="icon-options-general" class="icon32"></div>
        <h2>Bixt - Words into Affiliate Links</h2>

        <div id="poststuff">

            <div id="post-body" class="metabox-holder columns-2">

                <!-- main content -->
                <div id="post-body-content">

                    <div class="meta-box-sortables ui-sortable">

                        <div class="postbox">

                            <h3><span>Usage / Help</span></h3>
                            <div class="inside">
                                <ul>
                                    <li>Create an account at <a href='http://bixt.net/?utm_source=bixt&utm_medium=plugin-settings&utm_campaign=product' target="_blank">Bixt.net</a></li>
                                    <li>Create a list of keywords for a given domain</li>
                                    <li>Enable the plugin, enter the User ID and the domain in the settings below.</li>
                                    <li>Click on Save Changes.</li>
                                    <li>Create a page/post which has keywords with defined affiliate links.</li>
                                </ul>

                                <iframe width="560" height="315" src="http://www.youtube.com/embed/Pc_rBrhoKbg" frameborder="0" allowfullscreen></iframe>

                            </div> <!-- .inside -->

                        </div> <!-- .postbox -->

                        <div class="postbox">

                            <h3><span>Settings</span></h3>
                            <div class="inside">
                                <form method="post" action="options.php">
                                    <?php settings_fields('bixt_settings'); ?>
                                    <table class="form-table">
                                        <tr valign="top">
                                            <th scope="row">Plugin Status</th>
                                            <td>
                                                <label for="radio1">
                                                    <input type="radio" id="radio1" name="bixt_options[status]"
                                                        value="1" <?php echo empty($opts['status']) ? '' : 'checked="checked"'; ?> /> Enabled
                                                </label>
                                                <br/>
                                                <label for="radio2">
                                                    <input type="radio" id="radio2" name="bixt_options[status]"
                                                        value="0" <?php echo!empty($opts['status']) ? '' : 'checked="checked"'; ?> /> Disabled
                                                </label>
                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row">User ID</th>
                                            <td>
                                                <label for="bixt_options_user_id">
                                                    <input type="text" id="bixt_options_user_id" size='4'
                                                           name="bixt_options[user_id]"
                                                        value='<?php echo esc_attr($opts['user_id']); ?>' />
                                                        Example: 123
                                                </label>
                                                <div>This is the ID that is shown after you login into
                                                    <a href='http://bixt.net/?utm_source=bixt&utm_medium=plugin-settings&utm_campaign=product' target="_blank">Bixt.net</a>.</div>
                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <th scope="row">Domain</th>
                                            <td>
                                                <label for="bixt_options_domain">
                                                    <input type="text" id="bixt_options_domain"
                                                           name="bixt_options[domain]"
                                                        value='<?php echo esc_attr($opts['domain']); ?>' />
                                                    Example: orbisius.com
                                                </label>
                                                <div>
                                                    Each domain at bixt can have different keywords but you can also use one domain for all your sites.
                                                    That way you can share the same keywords.
                                                </div>
                                            </td>
                                        </tr>

                                        <tr valign="top">
                                            <th scope="row">Show Branding in the footer (Affiliate Links Generated by Bixt.net)</th>
                                            <td>
                                                <label for="branding_radio1">
                                                    <input type="radio" id="branding_radio1" name="bixt_options[branding]"
                                                        value="1" <?php echo empty($opts['branding']) ? '' : 'checked="checked"'; ?> /> Enabled
                                                </label>
                                                <br/>
                                                <label for="branding_radio2">
                                                    <input type="radio" id="branding_radio2" name="bixt_options[branding]"
                                                        value="0" <?php echo !empty($opts['branding']) ? '' : 'checked="checked"'; ?> /> Disabled
                                                </label>
                                            </td>
                                        </tr>
                                    </table>

                                    <p class="submit">
                                        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                                    </p>
                                </form>
                            </div> <!-- .inside -->

                        </div> <!-- .postbox -->
                        
                    </div> <!-- .meta-box-sortables .ui-sortable -->

                </div> <!-- post-body-content -->

                <!-- sidebar -->
                <div id="postbox-container-1" class="postbox-container">

                    <div class="meta-box-sortables">

                        <div class="postbox">
                            <h3><span>Hire Us</span></h3>
                            <div class="inside">
                                Hire us to create a plugin/web/mobile app for your business.
                                <br/><a href="http://orbisius.com/page/free-quote/?utm_source=bixt&utm_medium=plugin-settings&utm_campaign=product"
                                   title="If you want a custom web/mobile app/plugin developed contact us. This opens in a new window/tab"
                                    class="button-primary" target="_blank">Get a Free Quote</a>
                            </div> <!-- .inside -->
                        </div> <!-- .postbox -->

                        <div class="postbox">
                            <h3><span>Newsletter</span></h3>
                            <div class="inside">
                                <!-- Begin MailChimp Signup Form -->
                                <div id="mc_embed_signup">
                                    <?php
                                        $current_user = wp_get_current_user();
                                        $email = empty($current_user->user_email) ? '' : $current_user->user_email;
                                    ?>

                                    <form action="http://WebWeb.us2.list-manage.com/subscribe/post?u=005070a78d0e52a7b567e96df&amp;id=1b83cd2093" method="post"
                                          id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
                                        <input type="hidden" value="settings" name="SRC2" />
                                        <input type="hidden" value="bixt" name="SRC" />

                                        <span>Get notified about cool plugins we release</span>
                                        <!--<div class="indicates-required"><span class="app_asterisk">*</span> indicates required
                                        </div>-->
                                        <div class="mc-field-group">
                                            <label for="mce-EMAIL">Email <span class="app_asterisk">*</span></label>
                                            <input type="email" value="<?php echo esc_attr($email); ?>" name="EMAIL" class="required email" id="mce-EMAIL">
                                        </div>
                                        <div id="mce-responses" class="clear">
                                            <div class="response" id="mce-error-response" style="display:none"></div>
                                            <div class="response" id="mce-success-response" style="display:none"></div>
                                        </div>	<div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button-primary"></div>
                                    </form>
                                </div>
                                <!--End mc_embed_signup-->
                            </div> <!-- .inside -->
                        </div> <!-- .postbox -->

                        <div class="postbox">
                            <div class="inside">
                                <!-- Twitter: code -->
                                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="http://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
                                <!-- /Twitter: code -->

                                <!-- Twitter: Orbisius_Follow:js -->
                                    <a href="https://twitter.com/orbisius" class="twitter-follow-button"
                                       data-align="right" data-show-count="false">Follow @orbisius</a>
                                <!-- /Twitter: Orbisius_Follow:js -->

                                &nbsp;

                                <!-- Twitter: Tweet:js -->
                                <a href="https://twitter.com/share" class="twitter-share-button"
                                   data-lang="en" data-text="Checkout Bixt #WordPress #plugin and make more money with affiliate commissions"
                                   data-count="none" data-via="orbisius" data-related="orbisius"
                                   data-url="http://club.orbisius.com/products/wordpress-plugins/bixt/">Tweet</a>
                                <!-- /Twitter: Tweet:js -->

                                <br/>
                                <span>Support: <a href="http://club.orbisius.com/forums/forum/community-support-forum/wordpress-plugins/bixt/?utm_source=bixt&utm_medium=plugin-settings&utm_campaign=product"
                                    target="_blank" title="[new window]">Forums</a>
                                    |
                                    More <a href="http://club.orbisius.com/products/?utm_source=bixt&utm_medium=plugin-settings-support&utm_campaign=product"
                                    target="_blank" title="[new window]">Products</a>
                                    <!--|
                                     <a href="http://docs.google.com/viewer?url=https%3A%2F%2Fdl.dropboxusercontent.com%2Fs%2Fwz83vm9841lz3o9%2FOrbisius_LikeGate_Documentation.pdf" target="_blank">Documentation</a>
                                    -->
                                </span>
                            </div>
                        </div> <!-- .postbox -->

                        <div class="postbox"> <!-- quick-contact -->
                            <?php
                            $current_user = wp_get_current_user();
                            $email = empty($current_user->user_email) ? '' : $current_user->user_email;
                            $quick_form_action = is_ssl()
                                    ? 'https://ssl.orbisius.com/apps/quick-contact/'
                                    : 'http://apps.orbisius.com/quick-contact/';

                            if (!empty($_SERVER['DEV_ENV'])) {
                                $quick_form_action = 'http://localhost/projects/quick-contact/';
                            }
                            ?>
                            <script>
                                var bixt_quick_contact = {
                                    validate_form : function () {
                                        try {
                                            var msg = jQuery('#bixt_msg').val().trim();
                                            var email = jQuery('#bixt_email').val().trim();

                                            email = email.replace(/\s+/, '');
                                            email = email.replace(/\.+/, '.');
                                            email = email.replace(/\@+/, '@');

                                            if ( msg == '' ) {
                                                alert('Enter your message.');
                                                jQuery('#bixt_msg').focus().val(msg).css('border', '1px solid red');
                                                return false;
                                            } else {
                                                // all is good clear borders
                                                jQuery('#bixt_msg').css('border', '');
                                            }

                                            if ( email == '' || email.indexOf('@') <= 2 || email.indexOf('.') == -1) {
                                                alert('Enter your email and make sure it is valid.');
                                                jQuery('#bixt_email').focus().val(email).css('border', '1px solid red');
                                                return false;
                                            } else {
                                                // all is good clear borders
                                                jQuery('#bixt_email').css('border', '');
                                            }

                                            return true;
                                        } catch(e) {};
                                    }
                                };
                            </script>
                            <h3><span>Quick Question or Suggestion</span></h3>
                            <div class="inside">
                                <div>
                                    <form method="post" action="<?php echo $quick_form_action; ?>" target="_blank">
                                        <?php
                                            global $wp_version;
											$plugin_data = get_plugin_data(__FILE__);

                                            $hidden_data = array(
                                                'site_url' => site_url(),
                                                'wp_ver' => $wp_version,
                                                'first_name' => $current_user->first_name,
                                                'last_name' => $current_user->last_name,
                                                'product_name' => $plugin_data['Name'],
                                                'product_ver' => $plugin_data['Version'],
                                                'woocommerce_ver' => defined('WOOCOMMERCE_VERSION') ? WOOCOMMERCE_VERSION : 'n/a',
                                            );
                                            $hid_data = http_build_query($hidden_data);
                                            echo "<input type='hidden' name='data[sys_info]' value='$hid_data' />\n";
                                        ?>
                                        <textarea class="widefat" id='bixt_msg' name='data[msg]' required="required"></textarea>
                                        <br/>Your Email: <input type="text" class=""
                                               id="bixt_email" name='data[sender_email]' placeholder="Email" required="required"
                                               value="<?php echo esc_attr($email); ?>"
                                               />
                                        <br/><input type="submit" class="button-primary" value="<?php _e('Send Feedback') ?>"
                                                    onclick="return bixt_quick_contact.validate_form();" />
                                        <br/>
                                        What data will be sent
                                        <a href='javascript:void(0);'
                                            onclick='jQuery(".bixt_data_to_be_sent").toggle();'>(show/hide)</a>
                                        <div class="hide app_hide bixt_data_to_be_sent">
                                            <textarea class="widefat" rows="4" readonly="readonly" disabled="disabled"><?php
                                            foreach ($hidden_data as $key => $val) {
                                                if (is_array($val)) {
                                                    $val = var_export($val, 1);
                                                }

                                                echo "$key: $val\n";
                                            }
                                            ?></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div> <!-- .inside -->
                         </div> <!-- .postbox --> <!-- /quick-contact -->

                         <div class="postbox">
                            <?php
                                $plugin_data = bixt_get_plugin_data();

                                $app_link = urlencode($plugin_data['PluginURI']);
                                $app_title = urlencode($plugin_data['Name']);
                                $app_descr = urlencode($plugin_data['Description']);
                                ?>
                                <h3>Share</h3>
                                <p>
                                    <!-- AddThis Button BEGIN -->
                                <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                                    <a class="addthis_button_facebook" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_twitter" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_google_plusone" g:plusone:count="false" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_linkedin" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_email" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_myspace" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_google" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_digg" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_delicious" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_stumbleupon" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_tumblr" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_favorites" addthis:url="<?php echo $app_link ?>" addthis:title="<?php echo $app_title ?>" addthis:description="<?php echo $app_descr ?>"></a>
                                    <a class="addthis_button_compact"></a>
                                </div>
                                <!-- The JS code is in the footer -->

                                <script type="text/javascript">
                                    var addthis_config = {"data_track_clickback": true};
                                    var addthis_share = {
                                        templates: {twitter: 'Check out {{title}} #WordPress #plugin at {{lurl}} (via @orbisius)'}
                                    }
                                </script>
                                <!-- AddThis Button START part2 -->
                                <script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=lordspace"></script>
                                <!-- AddThis Button END part2 -->
                        </div> <!-- .postbox -->

                    </div> <!-- .meta-box-sortables -->

                </div> <!-- #postbox-container-1 .postbox-container -->

            </div> <!-- #post-body .metabox-holder .columns-2 -->

            <br class="clear">
        </div> <!-- #poststuff -->

    </div> <!-- .wrap -->

    <!--<h2>Support & Feature Requests</h2>
    <div class="updated"><p>
            ** NOTE: ** Support is handled on our site: <a href="http://club.orbisius.com/forums/forum/community-support-forum/wordpress-plugins/bixt/?utm_source=orbisius-child-theme-editor&utm_medium=action_screen&utm_campaign=product" target="_blank" title="[new window]">http://club.orbisius.com/support/</a>.
            Please do NOT use the WordPress forums or other places to seek support.
    </p></div>-->

    <?php //bixt_generate_ext_content(); ?>
    <?php
}

/**
 * Returns some plugin data such name and URL. This info is inserted as HTML
 * comment surrounding the embed code.
 * @return array
 */
function bixt_get_plugin_data() {
    // pull only these vars
    $default_headers = array(
        'Name' => 'Plugin Name',
        'PluginURI' => 'Plugin URI',
        'Description' => 'Description',
    );

    $plugin_data = get_file_data(__FILE__, $default_headers, 'plugin');

    $url = $plugin_data['PluginURI'];
    $name = $plugin_data['Name'];

    $data['name'] = $name;
    $data['url'] = $url;

    $data = array_merge($data, $plugin_data);

    return $data;
}

/**
 * Outputs or returns the HTML content for IFRAME promo content.
 */
function bixt_generate_ext_content($echo = 1) {
    $plugin_slug = basename(__FILE__);
    $plugin_slug = str_replace('.php', '', $plugin_slug);
    $plugin_slug = strtolower($plugin_slug); // jic

    $domain = !empty($_SERVER['DEV_ENV']) ? 'http://orbclub.com.clients.com' : 'http://club.orbisius.com';

    $url = $domain . '/wpu/content/wp/' . $plugin_slug . '/';

    $buff = <<<BUFF_EOF
    <iframe style="width:100%;min-height:300px;height: auto;" width="100%" height="480"
            src="$url" frameborder="0" allowfullscreen></iframe>

BUFF_EOF;

    if ($echo) {
        echo $buff;
    } else {
        return $buff;
    }
}

/**
 * It seems WP intentionally adds slashes for consistency with php.
 * Please note: WordPress Core and most plugins will still be expecting slashes, and the above code will confuse and break them.
 * If you must unslash, consider only doing it to your own data which isn't used by others:
 * @see http://codex.wordpress.org/Function_Reference/stripslashes_deep
 */
function bixt_get_request() {
    $req = $_REQUEST;
    $req = stripslashes_deep($req);

    return $req;
}

/**
 * adds some HTML comments in the page so people would know that this plugin powers their site.
 */
function bixt_add_plugin_credits() {
    // pull only these vars
    $default_headers = array(
        'Name' => 'Plugin Name',
        'PluginURI' => 'Plugin URI',
    );

    $plugin_data = get_file_data(__FILE__, $default_headers, 'plugin');

    $url = $plugin_data['PluginURI'];
    $name = $plugin_data['Name'];

    printf(PHP_EOL . PHP_EOL . '<!-- ' . "Powered by $name | URL: $url " . '-->' . PHP_EOL . PHP_EOL);
}

/**
 * Util funcs
 */
class bixt_util {
    /**
     * This cleans filenames but leaves some of the / because some files can be dir1/file.txt.
     * $jail_root must be added because it will also prefix the path with a directory i.e. jail
     *
     * @param type $file_name
     * @param type $jail_root
     * @return string
     */
    public static function sanitize_file_name($file_name = null, $jail_root = '') {
        if (empty($jail_root)) {
            $file_name = sanitize_file_name($file_name); // wp func
        } else {
            $file_name = str_replace('/', '__SLASH__', $file_name);
            $file_name = sanitize_file_name($file_name); // wp func
            $file_name = str_replace('__SLASH__', '/', $file_name);
        }

        $file_name = preg_replace('#(?:\/+|\\+)#si', '/', $file_name);
        $file_name = ltrim($file_name, '/'); // rm leading /

        if (!empty($jail_root)) {
            $file_name = $jail_root . $file_name;
        }

        return $file_name;
    }

    /**
     * Uses wp_kses to sanitize the data
     * @param  str/array $value
     * @return mixed: str/array
     * @throws Exception
     */
    public static function sanitize_data($value = null) {
        if (is_scalar($value)) {
            $value = wp_kses($value, array());
            $value = preg_replace('#\s+#si', ' ', $value);
            $value = trim($value);
        } else if (is_array($value)) {
            $value = array_map(__METHOD__, $value);
        } else {
            throw new Exception(__METHOD__.  " Cannot sanitize because of invalid input data.");
        }

        return $value;
    }

    /**
     * Returns the link to the Theme Editor e.g. when a theme_1 or theme_2 is supplied.
     * @param type $params
     * @return string
     */
    static public function get_settings_link($params = array()) {
        $rel_path = 'options-general.php?page=bixt_settings_page';

        if (!empty($params)) {
            $rel_path = bixt_html::add_url_params($rel_path, $params);
        }

        $link = is_multisite()
                    ? network_admin_url($rel_path)
                    : admin_url($rel_path);

        return $link;
    }

    /**
     * Recursive function to copy (all subdirectories and contents).
     * It doesn't create folder in the target folder.
     * Note: this may be slow if there are a lot of files.
     * The native call might be quicker.
     *
     * Example: src: folder/1/ target: folder/2/
     * @see http://stackoverflow.com/questions/5707806/recursive-copy-of-directory
     */
    static public function copy($src, $dest, $perm = 0775) {
        if (!is_dir($dest)) {
            mkdir($dest, $perm, 1);
        }

        if (is_dir($src)) {
            $dir = opendir($src);

            while ( false !== ( $file = readdir($dir) ) ) {
                if ( $file == '.' || $file == '..' || $file == '.git'  || $file == '.svn' ) {
                    continue;
                }

                $new_src = rtrim($src, '/') . '/' . $file;
                $new_dest = rtrim($dest, '/') . '/' . $file;

                if ( is_dir( $new_src ) ) {
                    self::copy( $new_src, $new_dest );
                } else {
                    copy( $new_src, $new_dest );
                }
            }

            closedir($dir);
        } else { // can also handle simple copy commands
            copy($src, $dest);
        }
    }

    /**
     * Create an zip file. Requires ZipArchive class to exist.
     * Usage: $result = create_zip($files_to_zip, 'my-archive.zip', true, $prefix_to_strip, 'Slavi created this archive at ' . date('r') );
     *
     * @param array $files
     * @param str $destination zip file
     * @param str $overwrite
     * @param str $prefix_to_strip
     * @param str $comment
     * @return boolean
     */
    function create_zip($files = array(), $destination = '', $overwrite = false, $prefix_to_strip = '', $comment = '' ) {
        if ((file_exists($destination) && !$overwrite) || !class_exists('ZipArchive')) {
            return false;
        }

        $zip = new ZipArchive();

        if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }

        foreach ($files as $file) {
            // if we specify abs path to the dir we'll add a relative folder in the archive.
            $file_in_archive = str_ireplace($prefix_to_strip, '', $file);
            $zip->addFile($file, $file_in_archive);
        }

        if (!empty($comment)) {
            $zip->setArchiveComment($comment);
        }

        $zip->close();

        return file_exists($destination);
    }

    /**
     * Loads files from a directory and skips . and ..
     * By default it retuns files relativ to the theme's folder.
     * 
     * @since 1.1.3 it supports recusiveness
     * @param bool $ret_full_paths
     */
    public static function load_files($dir, $ret_full_paths = 0) {
        $files = array();

        $dir = rtrim($dir, '/') . '/';
        $all_files = scandir($dir);

        foreach ($all_files as $file) {
            if ($file == '.' || $file == '..' || substr($file, 0, 1) == '.') { // skip hidden files
                continue;
            }

            if (is_dir($dir . $file)) {
                $dir_in_themes_folder = $file;
                $sub_dir_files = self::load_files($dir . $dir_in_themes_folder, $ret_full_paths);
                
                foreach ($sub_dir_files as $sub_dir_file) {
                    $files[] = $ret_full_paths ? $sub_dir_file : $dir_in_themes_folder . '/' . $sub_dir_file;
                }
            } else {
                $files[] = ($ret_full_paths ? $dir : '') . $file;
            }
        }

        return $files;
    }

    /**
     * Outputs a message (adds some paragraphs).
     */
    static public function msg($msg, $status = 0) {
        $msg = join("<br/>\n", (array) $msg);

        if (empty($status)) {
            $cls = 'app-alert-error';
        } elseif ($status == 1) {
            $cls = 'app-alert-success';
        } else {
            $cls = 'app-alert-notice';
        }

        $str = "<div class='$cls'><p>$msg</p></div>";

        return $str;
    }
}

/**
 * HTML related methods
 */
class bixt_html {

    /**
     *
     * Appends a parameter to an url; uses '?' or '&'. It's the reverse of parse_str().
     * If no URL is supplied no prefix is added (? or &)
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public static function add_url_params($url, $params = array()) {
        $str = $query_start = '';

        $params = (array) $params;

        if (empty($params)) {
            return $url;
        }

        if (!empty($url)) {
            $query_start = (strpos($url, '?') === false) ? '?' : '&';
        }

        $str = $url . $query_start . http_build_query($params);

        return $str;
    }

    // generates HTML select
    public static function html_select($name = '', $sel = null, $options = array(), $attr = '') {
        $name = trim($name);
        $elem_name = $name;
        $elem_name = strtolower($elem_name);
        $elem_name = preg_replace('#[^\w]#si', '_', $elem_name);
        $elem_name = trim($elem_name, '_');

        $html = "\n" . '<select id="' . esc_attr($elem_name) . '" name="' . esc_attr($name) . '" ' . $attr . '>' . "\n";

        foreach ($options as $key => $label) {
            $selected = $sel == $key ? ' selected="selected"' : '';

            // if the key contains underscores that means these are labels
            // and should be readonly
            if (strpos($key, '__') !== false) {
                $selected .= ' disabled="disabled" ';
            }

            // This makes certain options to have certain CSS class
            // which can be used to highlight the row
            // the key must start with __sys_CLASS_NAME
            if (preg_match('#__sys_([\w-]+)#si', $label, $matches)) {
                $label = str_replace($matches[0], '', $label);
                $selected .= " class='$matches[1]' ";
            }

            $html .= "\t<option value='$key' $selected>$label</option>\n";
        }

        $html .= '</select>';
        $html .= "\n";

        return $html;
    }
}
