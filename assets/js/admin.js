jQuery(document).ready(function($) {

    function toggleButtonLink() {
        var $buttonFunction = $('#price_guard_button_function');
        var $customButtonLink = $('#price_guard_custom_button_link').closest('tr');

        if ($buttonFunction.val() === 'normal_link') {
            $customButtonLink.show();
        } else {
            $customButtonLink.hide();
        }
    }

    $('#price_guard_button_function').on('change', toggleButtonLink);
    toggleButtonLink();

    function toggleConditionalFields() {
        var $hidePrice = $('#price_guard_hide_price');
        var $hidePriceText = $('#price_guard_hide_price_text').closest('tr');
        var $hideAddToCart = $('#price_guard_hide_add_to_cart');
        var $customButtonText = $('#price_guard_custom_button_text').closest('tr');
        var $customButtonLink = $('#price_guard_custom_button_link').closest('tr');
        var $applyGlobally = $('input[name="price_guard_apply_globally"]');
        var $categories = $('#price_guard_categories').closest('tr');

        if ($hidePrice.is(':checked')) {
            $hidePriceText.show();
        } else {
            $hidePriceText.hide();
        }

        if ($hideAddToCart.is(':checked')) {
            $customButtonText.show();
            $customButtonLink.show();
        } else {
            $customButtonText.hide();
            $customButtonLink.hide();
        }

        if ($applyGlobally.filter(':checked').val() === 'yes') {
            $categories.hide();
        } else {
            $categories.show();
        }
    }

    $('#price_guard_hide_price, #price_guard_hide_add_to_cart, input[name="price_guard_apply_globally"]').on('change', toggleConditionalFields);

    toggleConditionalFields();
});