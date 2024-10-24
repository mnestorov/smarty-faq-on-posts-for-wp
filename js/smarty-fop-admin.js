jQuery(document).ready(function($) {
    var faqIndex = $('div.smarty-custom-faq').length;

    $('#smarty_add_faq_button').on('click', function() {
        var newFAQ = `
        <div class="smarty-custom-faq ` + (faqIndex % 2 == 0 ? 'even' : 'odd') + `">
            <div class="smarty-faq-header">
                <h4>FAQ ` + (faqIndex + 1) + ` - <span class="faq-title">New FAQ</span></h4>
                <button type="button" class="button button-secondary smarty-toggle-faq">Toggle</button>
            </div>
            <div class="smarty-faq-content" style="display: none;">
                <p>
                    <label>Question:</label><br>
                    <input type="text" name="_smarty_faqs[` + faqIndex + `][question]" style="width: 100%;">
                </p>
                <p>
                    <label>Answer:</label><br>
                    <textarea name="_smarty_faqs[` + faqIndex + `][answer]" style="width: 100%;"></textarea>
                </p>
                <input type="hidden" name="_smarty_faqs[` + faqIndex + `][delete]" value="0" class="smarty-delete-input">
                <button type="button" class="button button-secondary smarty-remove-faq-button">Remove FAQ</button>
            </div>
        </div>`;

        $('#smarty_faqs_container').append(newFAQ);
        faqIndex++;
    });

    $(document).on('click', '.smarty-toggle-faq', function() {
        $(this).closest('.smarty-custom-faq').find('.smarty-faq-content').slideToggle();
    });

    $(document).on('click', '.smarty-remove-faq-button', function() {
        var faqDiv = $(this).closest('.smarty-custom-faq');
        var deleteInput = faqDiv.find('.smarty-delete-input');

        if (faqDiv.data('index') !== undefined) {
            deleteInput.val('1');
            faqDiv.hide();
        } else {
            faqDiv.remove();
        }
    });
});
