$(document).ready(function () {
    $('.delete-link').on('click', function() {
        var button = $(this);
        $.ajax('/external-links/' + button.data('id'), {
            method: 'DELETE',
            error: function (xhr) {
                var json = JSON.parse(xhr.responseText);
                alert(json['errorDescription']);
            },
            success: function () {
                button.parents('li').remove();
            }
        });
    })
});