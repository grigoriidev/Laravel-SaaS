$(document).ready(function () {
    $('.app-box').click(function () {
        if ($(this).attr('data-app') == 'visma-eaccounting') {
            document.location.href = $(this).attr('data-app-connect');
        }
        if ($(this).attr('data-app') == 'woocommerce') {
            document.location.href = $(this).attr('data-app-connect');
        }
        if ($(this).attr('data-app') == 'shopify') {
            document.location.href = $(this).attr('data-app-connect');
        }
        if ($(this).attr('data-app') == 'stripe') {
            document.location.href = $(this).attr('data-app-connect');
        }
        if ($(this).attr('data-app') == 'app-setting') {
            document.location.href = $(this).attr('data-app-connect');
        }
    });
    $('.app-setting').click(function () {
 
        if ($(this).attr('app-data') == 'app-setting-visma') {
            document.location.href = $(this).attr('data-app-connect');
        }
        if ($(this).attr('app-data') == 'app-setting-woocommerce') {
            document.location.href = $(this).attr('data-app-connect');
        }
        if ($(this).attr('app-data') == 'app-setting-stripe') {
            document.location.href = $(this).attr('data-app-connect');
        }

    })
    $("#app_search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        for (var i = 0; i < $(".box-body > div.appdiv").length; i++) {
            var bfilter0 = $(".box-body > div.appdiv")[i].getElementsByTagName("b")[0].innerText.toLowerCase().indexOf(value);
            var bfilter1 = $(".box-body > div.appdiv")[i].getElementsByTagName("b")[1].innerText.toLowerCase().indexOf(value);
            if (bfilter0 > -1 || bfilter1 > -1) {
                $(".box-body > div.appdiv")[i].style.display = "";
            } else {
                $(".box-body > div.appdiv")[i].style.display = "none";
            }
        }
    });
});