define([], function () {
    var mageJsComponent = function(config)
    {
        window.Emarsys = window.Emarsys || {};
        window.Emarsys.Magento2 = window.Emarsys.Magento2 || {};
        window.Emarsys.Magento2.orderData = config.orderData;
    };

    return mageJsComponent;
});
