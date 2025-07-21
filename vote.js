jQuery(document).ready(function ($) {
    $('.provide-data').on('change', function () {
        var form = $(this).closest('.vote-form');
        var fields = form.find('.vote-data-fields');
        var gdpr = form.find('.gdpr-field input');
        if ($(this).is(':checked')) {
            fields.slideDown();
            gdpr.prop('required', true);
        } else {
            fields.slideUp();
            gdpr.prop('required', false).prop('checked', false);
            fields.find('input[type="text"], input[type="email"]').val('');
        }
    });

    $('.vote-form').submit(function (e) {
        e.preventDefault();

        var form = $(this);
        var button = form.find('.vote-button');
        var ideaID = button.data('id');
        var name = $('#vote-name-' + ideaID).val().trim();
        var email = $('#vote-email-' + ideaID).val().trim();
        var website = form.find('input[name="website"]').val(); // <-- honeypot mező beolvasása

        var board = form.data('board');
        if (email && board === 'dolgozo' && !email.endsWith('@' + ideaVote.employee_domain)) {
            alert('Csak ' + ideaVote.employee_domain + ' végű e-mail címmel szavazhatsz ezen az ötletnél.');
            return;
        }

        $.post(ideaVote.ajax_url, {
            action: 'idea_vote',
            idea_id: ideaID,
            name: name,
            email: email,
            website: website
        }, function (response) {
            if (response.success) {
                $('#vote-count-' + ideaID).text(response.data.votes);
                form.find('input, button').prop('disabled', true);
                alert('Köszönjük a szavazatot!');
            } else {
                alert(response.data);
            }
        });
    });
});
