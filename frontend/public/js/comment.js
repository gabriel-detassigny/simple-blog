$(document).ready(function () {
    $("#new-comment").submit(function(e) {
        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(),
            success: function() {
                window.location.reload(true);
            },
            failure: function () {
                var json = JSON.parse(xhr.responseText);
                var errorDiv = $('#new-comment-error');
                errorDiv.text(json['errorDescription']);
                errorDiv.show();
            }
        });

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });
});