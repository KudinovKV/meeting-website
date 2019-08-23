// привет, хабр!
jQuery('.upload-photo').change(function() {
    if ($(this).val() != '') 
    {
        $(this).prev().text('Selected files: ' + $(this)[0].files.length);
    }   
    else 
        $(this).prev().text('Browse photo...');
});
