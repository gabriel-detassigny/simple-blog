$(document).ready(function () {
    tinymce.init({
        selector:'.wysiwyg-input',
        plugins: "image",
        images_upload_url: '/admin/images/upload',
        images_upload_credentials: true
    });

    tinymce.activeEditor.uploadImages(function() {
        $.post('/admin/images/upload', tinymce.activeEditor.getContent()).done(function() {
            console.log("Uploaded images and posted content as an ajax request.");
        });
    });
});
