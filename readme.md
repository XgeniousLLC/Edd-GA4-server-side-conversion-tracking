# EDD GA4 Server-Side Conversion Tracking

This WordPress plugin implements server-side tracking for Easy Digital Downloads (EDD) with Google Analytics 4 (GA4). It allows for more accurate conversion tracking by sending purchase data directly from your server to Google Analytics.

## Features

- Server-side tracking for EDD purchases
- Easy configuration through WordPress admin panel
- Real-time purchase event tracking in GA4
- Supports Google Ads conversion tracking via GA4

## Installation

1. Download the ZIP file from this repository.
2. Log in to your WordPress admin panel.
3. Go to Plugins > Add New > Upload Plugin.
4. Choose the downloaded ZIP file and click "Install Now".
5. After installation, click "Activate Plugin".

## Configuration

1. In your WordPress admin panel, go to Settings > EDD GA4 Tracking.
2. Enter your GA4 Measurement ID (format: G-XXXXXXXXXX).
3. Enter your GA4 API Secret.
4. Save the settings.

To find your GA4 Measurement ID and API Secret:

1. Go to your Google Analytics 4 property.
2. Navigate to Admin > Data Streams > Select your web stream.
3. Copy the Measurement ID.
4. In the same Data Stream settings, go to "Measurement Protocol API secrets" and create a new secret.


## Setting Up Google Ads Conversion Tracking

To track conversions in Google Ads using this plugin, follow these steps:

1. Ensure your Google Ads account is linked to your GA4 property:
    - In GA4, go to Admin > Property Settings > Product Links > Google Ads Links
    - Click on "Link" and follow the prompts to connect your Google Ads account

2. Import GA4 conversions to Google Ads:
    - Log in to your Google Ads account
    - Go to Tools & Settings > Measurement > Conversions
    - Click the "+" button to add a new conversion action
    - Select "Import" and choose "Google Analytics 4 properties"
    - Select your GA4 property and choose the "purchase" event
    - Configure the attribution model and other settings as needed
    - Click "Import and Continue" to complete the setup

3. Wait for data:
    - It may take up to 24 hours for conversion data to appear in your Google Ads account
    - Ensure you're making actual purchases (or test purchases) on your site for data to be recorded

4. Verify in Google Ads:
    - After 24-48 hours, check your Google Ads conversions report
    - You should see the imported GA4 purchase events as conversions

Note: The value of the conversions in Google Ads will match the purchase amounts sent by this plugin to GA4.

## Troubleshooting Google Ads Conversions

If you're not seeing conversions in Google Ads:

1. Verify that the plugin is correctly sending data to GA4 (check GA4 real-time reports)
2. Ensure your Google Ads and GA4 accounts are properly linked
3. Check that you've correctly imported the GA4 purchase event as a conversion in Google Ads
4. Allow sufficient time for data to propagate (up to 48 hours in some cases)
5. Verify that you're looking at the correct date range in Google Ads reports

For persistent issues, check the Google Ads support documentation or contact Google Ads support.


## Usage

Once configured, the plugin will automatically send purchase data to GA4 whenever an EDD purchase is completed. No further action is required.

## Verification

To verify that the plugin is working:

1. Make a test purchase on your site.
2. Check your GA4 real-time reports to see if the purchase event is recorded.
3. After 24-48 hours, check your GA4 Monetization > Overview report to see if the purchase data is being aggregated correctly.

## Troubleshooting

If you're not seeing purchase data in GA4:

1. Check your WordPress error logs for any error messages related to the plugin.
2. Verify that your GA4 Measurement ID and API Secret are correct.
3. Ensure that your GA4 property and data stream are set up correctly.
4. Check the GA4 DebugView (Configure > DebugView) immediately after making a test purchase.

## Support

For issues, questions, or contributions, please open an issue in this GitHub repository.

## License

This project is licensed under the GPL-2.0 License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- [Easy Digital Downloads](https://easydigitaldownloads.com/)
- [Google Analytics 4](https://developers.google.com/analytics/devguides/collection/ga4)