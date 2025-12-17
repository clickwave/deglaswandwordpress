jQuery(document).ready(function($) {

    // Add line item
    $('#cgc-add-line-item-btn').on('click', function() {
        const description = $('#cgc-new-item-description').val();
        const quantity = $('#cgc-new-item-quantity').val();
        const unitPrice = $('#cgc-new-item-price').val();
        const notes = $('#cgc-new-item-notes').val();
        const postId = $(this).data('post-id');

        if (!description || !quantity || !unitPrice) {
            alert('Vul alle vereiste velden in');
            return;
        }

        const button = $(this);
        button.prop('disabled', true).text('Bezig...');

        $.ajax({
            url: cgcAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'cgc_add_line_item',
                nonce: cgcAdmin.nonce,
                post_id: postId,
                description: description,
                quantity: quantity,
                unit_price: unitPrice,
                notes: notes
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Fout bij toevoegen item');
                    button.prop('disabled', false).html('<span class="dashicons dashicons-plus-alt"></span> Item Toevoegen');
                }
            },
            error: function() {
                alert('AJAX fout');
                button.prop('disabled', false).html('<span class="dashicons dashicons-plus-alt"></span> Item Toevoegen');
            }
        });
    });

    // Quick add buttons
    $('.cgc-quick-add').on('click', function() {
        const description = $(this).data('description');
        const price = $(this).data('price');

        $('#cgc-new-item-description').val(description);
        $('#cgc-new-item-price').val(price);
        $('#cgc-new-item-quantity').val(1);
        $('#cgc-new-item-notes').val('');

        // Scroll to form
        $('html, body').animate({
            scrollTop: $('#cgc-new-item-description').offset().top - 100
        }, 500);

        // Highlight the description field
        $('#cgc-new-item-description').focus();
    });

    // Remove line item
    $('.cgc-remove-line-item').on('click', function() {
        if (!confirm('Weet je zeker dat je dit item wilt verwijderen?')) {
            return;
        }

        const index = $(this).data('index');
        const postId = $(this).data('post-id');
        const button = $(this);

        button.prop('disabled', true);

        $.ajax({
            url: cgcAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'cgc_remove_line_item',
                nonce: cgcAdmin.nonce,
                post_id: postId,
                index: index
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Fout bij verwijderen item');
                    button.prop('disabled', false);
                }
            },
            error: function() {
                alert('AJAX fout');
                button.prop('disabled', false);
            }
        });
    });

    // Configuration checkbox styling
    $('.cgc-edit-config input[type="checkbox"]').on('change', function() {
        const label = $(this).closest('label');
        if ($(this).is(':checked')) {
            label.css('border-color', '#10b981');
        } else {
            label.css('border-color', '#e0e0e0');
        }
    });
});
