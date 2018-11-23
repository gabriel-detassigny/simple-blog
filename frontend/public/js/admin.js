$(document).ready(function () {
    tinymce.init({
        selector:'.wysiwyg-input',
        toolbar: 'image',
        plugins: 'image imagetools',
        images_upload_credentials: true,
        images_upload_handler: function (blobInfo, success, failure) {
            var formData = new FormData();
            formData.append('file', blobInfo.blob());
            $.ajax('/admin/images/upload', {
                method: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                error: function (xhr) {
                    if (xhr.status !== 200) {
                        failure('HTTP Error: ' + xhr.status);
                    }
                },
                success: function (data) {
                    success(data.location);
                }
            });
        }
    });
});
