For development environment information refer to [dev README](dev/README.md).


# Magento2 integration for the Emarsys Marketing Platform installation guide
[ ![Codeship Status for emartech/magento2-extension](https://app.codeship.com/projects/df2056c0-3b1f-0136-0a27-1e274df65f02/status?branch=master)](https://app.codeship.com/projects/290273)

1. ### Composer install the package
    ```
    $ composer require ematech/emarsys-magento2-extension
    ```
1.  ### Run Magento setup to install the module
    ```
    $ bin/magento setup:upgrade
    ```
1. ### Get your connect token
   The connect token contains a token for Magento webapi and your current hostname, so that we can send requests to your instance.

   To get your token, navigate to **Stores > Configuration** page in your Magento admin, expand the **Emarsys** panel on the left side and click **Connect**. Your token will be displayed in a textarea, copy that string.

1.  ### Connect your Emarsys account
    Navigate to Emarsys Suite, from the top menu choose **Add-ons** and click the **Magento 2** menu item.

    On the settings page click **Connect**, paste your connect token and click the **Connect** button.

    Next, you have to click the **Stores** button. Your store and website data will be loaded, so first choose the Magento website you want to connect with your Emarsys account. A list of the stores on the website will be populated, choose any of the stores you want to connect. You may change the **Slug** field for something meaningful for you (please keep the slug format, no spaces, special characters, etc). Click the **Connect** button.

1.  ### Turn on the features
    Now you can start turning on the provided features. Note that some features and depending on each other, so for example you will not be able to turn on **Orders** until the initial **Customers** upload is finished.

    Also note, that if your store does not use the Magento provided frontend, the **Web behavior tracking** feature will not work. You will have to implement tracking as described [here](https://help.emarsys.com/hc/en-us/articles/360005884393-tracking-code-Web-Extend).
