jQuery(document).ready(function($) {

    // Add note functionality
    $('#cgc-add-note').on('click', function() {
        const button = $(this);
        const postId = button.data('post-id');
        const content = $('#cgc-new-note').val().trim();
        const type = $('#cgc-note-type').val();

        if (!content) {
            alert('Voer een notitie in');
            return;
        }

        button.prop('disabled', true).text('Toevoegen...');

        $.ajax({
            url: cgcCRM.ajaxUrl,
            type: 'POST',
            data: {
                action: 'cgc_add_note',
                nonce: cgcCRM.nonce,
                post_id: postId,
                content: content,
                type: type
            },
            success: function(response) {
                if (response.success) {
                    // Clear textarea
                    $('#cgc-new-note').val('');

                    // Add note to list
                    const note = response.data;
                    const typeIcons = {
                        'note': '📝',
                        'call': '📞',
                        'email': '📧',
                        'meeting': '🤝'
                    };
                    const icon = typeIcons[note.type] || '📝';

                    const noteHtml = `
                        <div style="background: white; padding: 15px; border-left: 3px solid #0073aa; margin-bottom: 10px; border-radius: 4px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <div style="font-size: 13px; color: #666;">
                                    <span style="font-size: 16px; margin-right: 5px;">${icon}</span>
                                    <strong>${note.user}</strong>
                                </div>
                                <div style="font-size: 12px; color: #999;">
                                    ${formatDate(note.date)}
                                </div>
                            </div>
                            <div style="line-height: 1.6;">
                                ${escapeHtml(note.content).replace(/\n/g, '<br>')}
                            </div>
                        </div>
                    `;

                    // Remove "no notes" message if exists
                    $('#cgc-notes-list p').remove();

                    // Prepend new note
                    $('#cgc-notes-list').prepend(noteHtml);

                    // Show success message
                    button.after('<span class="cgc-success-msg" style="color: #46b450; margin-left: 10px;">✓ Notitie toegevoegd</span>');
                    setTimeout(function() {
                        $('.cgc-success-msg').fadeOut(function() {
                            $(this).remove();
                        });
                    }, 2000);
                } else {
                    alert('Fout bij toevoegen: ' + (response.data || 'Unknown error'));
                }
            },
            error: function() {
                alert('Fout bij toevoegen van notitie');
            },
            complete: function() {
                button.prop('disabled', false).text('Notitie Toevoegen');
            }
        });
    });

    // Enter key in textarea
    $('#cgc-new-note').on('keydown', function(e) {
        if (e.ctrlKey && e.keyCode === 13) {
            $('#cgc-add-note').click();
        }
    });

    // Helper functions
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${day}-${month}-${year} ${hours}:${minutes}`;
    }
});
