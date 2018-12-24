$(document).ready(function () {
    $("#new-comment").submit(function(e) {
        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: 'POST',
            url: url,
            data: form.serialize(),
            dataType: 'json',
            success: function() {
                window.location.reload(true);
            },
            error: function (xhr) {
                var json = JSON.parse(xhr.responseText);
                var errorDiv = $('#new-comment-error');
                errorDiv.text(json['errorDescription']);
                errorDiv.show();
            }
        });

        e.preventDefault();
    });
});