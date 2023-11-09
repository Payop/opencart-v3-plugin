Opencart v3 Payop Payment Gateway
=====================

## Brief Description

The OpenCart v3 Payop Payment Gateway is a plugin that enables you to accept payments in OpenCart version 3 via Payop.com.

## Requirements

- OpenCart 3.0

## Installation

**Method 1: Installation via OpenCart Store (Recommended)**

1. Go to your OpenCart admin dashboard.

2. In the left-hand menu, navigate to the "Extensions" section.

3. Click on "Extension Installer."

4. Click the "Upload" button and select the plugin file you downloaded from the OpenCart store with the .ocmod extension.

5. After the upload is complete, click on "Continue."

6. Go back to the "Extensions" section in the admin dashboard.

7. Click on "Modifications" in the "Extensions" menu.

8. Find the Payop plugin listed in the modifications list. Click the refresh icon to refresh your modification cache. This step ensures that your new plugin is recognized.

9. Now, go to the "Extensions" menu and click on "Payments."

10. You should see the Payop plugin listed among the payment methods. Click the "Install" button to activate the Payop Payment Gateway.

11. After installation, click the "Edit" option to access the plugin settings.

12. Configure the settings to match your requirements and save your changes.

**Method 2: Manual Installation via Uploading .ocmod File**

1. Download the latest release of the Payop plugin from the OpenCart store or GitHub in .ocmod format.

2. Log in to your OpenCart admin dashboard.

3. Navigate to the "Extensions" section on the left-hand menu.

4. Click on "Extension Installer."

5. Click the "Upload" button and select the .ocmod file you downloaded.

6. After the upload is complete, click on "Continue."

7. Go back to the "Extensions" section in the admin dashboard.

8. Click on "Modifications" in the "Extensions" menu.

9. Find the Payop plugin listed in the modifications list. Click the refresh icon to refresh your modification cache.

10. Proceed to the "Extensions" menu and click on "Payments."

11. Look for the Payop plugin among the payment methods. Click the "Install" button to activate the Payop Payment Gateway.

12. After installation, click "Edit" to access the plugin settings.

13. Configure the settings as needed and save your changes.

## Configuration

After installing the Payop plugin, configure the settings as follows:

- **Public Key:** Obtain from your Payop client panel after registering as a merchant on [Payop.com](https://payop.com).

- **Secret Key:** Obtain from your Payop client panel after registering as a merchant on [Payop.com](https://payop.com).

- **Callback/IPN URL:** https://{replace-with-your-domain}/index.php?route=extension/payment/payop/callback

## Support

If you encounter issues with this plugin, please consider the following support options:

- Open an issue if you are having problems with this plugin.
- Refer to Payop Documentation.
- Contact [Payop support](https://payop.com/en/contact-us) and provide the following details to help them assist you effectively:
   - OpenCart Version
   - Configuration settings for the plugin (consider taking screen grabs)
   - Any log files that may assist
   - Web server error logs
   - Screenshots of error messages if applicable

## Contribute

If you'd like to contribute to this project, you don't necessarily need to be a developer. Here's how you can help:

- Report bugs or suggest improvements by opening an issue.

- If you're a developer and want to contribute an enhancement, bug fix, or other patch, please fork this repository and submit a pull request with your changes.

This open-source project is released under the MIT license, which means you are free to use the code in your own projects.

## License

Please refer to the 
[LICENSE](https://github.com/Payop/opencart-v3-plugin/blob/master/LICENSE)
file that came with this project.
