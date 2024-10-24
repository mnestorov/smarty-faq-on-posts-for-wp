jQuery(document).ready(function($) {
    $(".smarty-faq-question-wrapper").on("click", function() {
        var icon = $(this).find(".smarty-faq-icon");
        $(this).next(".smarty-faq-answer").slideToggle();
        if (icon.hasClass("dashicons-plus")) {
            icon.removeClass("dashicons-plus").addClass("dashicons-minus");
        } else {
            icon.removeClass("dashicons-minus").addClass("dashicons-plus");
        }
    });
});
