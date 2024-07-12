<?php
/*
Plugin Name: EDD GA4 Server-Side Tracking
Description: Implements server-side tracking for Easy Digital Downloads with Google Analytics 4
Version: 1.0.0
Author: Sharifur Rahman
*/

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class EDD_GA4_Server_Side_Tracking {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('edd_complete_purchase', array($this, 'edd_server_side_ga4_tracking'));
    }

    public function add_plugin_page() {
        add_options_page(
            'EDD GA4 Tracking Settings',
            'EDD GA4 Tracking',
            'manage_options',
            'edd-ga4-tracking',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $this->options = get_option('edd_ga4_tracking_options');
        ?>
        <div class="wrap">
            <h1>EDD GA4 Server-Side Tracking Settings</h1>
            <div class="edd-ga4-tracking-wrapper" style="display: flex;">
                <div class="edd-ga4-tracking-settings" style="flex: 2; margin-right: 20px;">
                    <form method="post" action="options.php">
                        <?php
                        settings_fields('edd_ga4_tracking_option_group');
                        do_settings_sections('edd-ga4-tracking-admin');
                        submit_button();
                        ?>
                    </form>
                </div>
                <div class="edd-ga4-tracking-instructions" style="flex: 1; background: #f9f9f9; padding: 20px; border: 1px solid #ccc;">
                    <h2>Configuration Instructions</h2>
                    <ol>
                        <li>Set up a Google Analytics 4 property if you haven't already.</li>
                        <li>In your GA4 property, go to Admin > Data Streams > Select your stream.</li>
                        <li>Copy the Measurement ID and paste it in the field on the left.</li>
                        <li>In the same Data Stream settings, go to "Measurement Protocol API secrets" and create a new secret.</li>
                        <li>Copy the API secret and paste it in the field on the left.</li>
                        <li>Save the settings.</li>
                        <li>Ensure your Google Ads account is linked to your GA4 property.</li>
                        <li>In Google Ads, set up a conversion action that imports the purchase event from GA4.</li>
                    </ol>
                    <p>After configuration, test by making a purchase and checking your GA4 real-time reports and Google Ads conversions.</p>
                </div>
            </div>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'edd_ga4_tracking_option_group',
            'edd_ga4_tracking_options',
            array($this, 'sanitize')
        );

        add_settings_section(
            'edd_ga4_tracking_setting_section',
            'Settings',
            array($this, 'section_info'),
            'edd-ga4-tracking-admin'
        );

        add_settings_field(
            'measurement_id',
            'GA4 Measurement ID',
            array($this, 'measurement_id_callback'),
            'edd-ga4-tracking-admin',
            'edd_ga4_tracking_setting_section'
        );

        add_settings_field(
            'api_secret',
            'GA4 API Secret',
            array($this, 'api_secret_callback'),
            'edd-ga4-tracking-admin',
            'edd_ga4_tracking_setting_section'
        );
    }

    public function sanitize($input) {
        $sanitary_values = array();
        if (isset($input['measurement_id'])) {
            $sanitary_values['measurement_id'] = sanitize_text_field($input['measurement_id']);
        }
        if (isset($input['api_secret'])) {
            $sanitary_values['api_secret'] = sanitize_text_field($input['api_secret']);
        }
        return $sanitary_values;
    }

    public function section_info() {
        echo 'Enter your settings below:';
    }

    public function measurement_id_callback() {
        printf(
            '<input type="text" class="regular-text" id="measurement_id" name="edd_ga4_tracking_options[measurement_id]" value="%s">',
            isset($this->options['measurement_id']) ? esc_attr($this->options['measurement_id']) : ''
        );
        echo '<p class="description">Enter your GA4 Measurement ID (e.g., G-XXXXXXXXXX)</p>';
    }

    public function api_secret_callback() {
        printf(
            '<input type="text" class="regular-text" id="api_secret" name="edd_ga4_tracking_options[api_secret]" value="%s">',
            isset($this->options['api_secret']) ? esc_attr($this->options['api_secret']) : ''
        );
        echo '<p class="description">Enter your GA4 API Secret</p>';
    }

    public function edd_server_side_ga4_tracking($payment_id) {
        $options = get_option('edd_ga4_tracking_options');
        $measurement_id = $options['measurement_id'] ?? '';
        $api_secret = $options['api_secret'] ?? '';

        if (empty($measurement_id) || empty($api_secret)) {
            error_log('GA4 Measurement ID or API Secret is missing');
            return;
        }

        $payment = edd_get_payment($payment_id);
        if ($payment->status != 'complete') {
            return;
        }

        $client_id = $_COOKIE['_ga'] ?? uniqid('ga4_', true);

        $data = [
            'client_id' => $client_id,
            'events' => [
                [
                    'name' => 'purchase',
                    'params' => [
                        'transaction_id' => $payment_id,
                        'value' => $payment->total,
                        'currency' => edd_get_currency(),
                        'items' => [],
                    ],
                ],
            ],
        ];

        foreach ($payment->cart_details as $item) {
            $data['events'][0]['params']['items'][] = [
                'item_id' => $item['id'],
                'item_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ];
        }

        $postdata = json_encode($data);

        $opts = ['http' =>
            [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/json',
                'content' => $postdata,
            ]
        ];

        $context  = stream_context_create($opts);
        $result = file_get_contents("https://www.google-analytics.com/mp/collect?measurement_id={$measurement_id}&api_secret={$api_secret}", false, $context);

        error_log('GA4 Server-Side Tracking Result: ' . $result);
    }
}

if (class_exists('EDD_GA4_Server_Side_Tracking')) {
    $edd_ga4_server_side_tracking = new EDD_GA4_Server_Side_Tracking();
}