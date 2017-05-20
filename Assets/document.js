var $editor_textarea = $('#editor-textarea');
var $editor_preview = $('#editor-preview');
var $edit_link = $('#editor-edit-link');
var $preview_link = $('#editor-preview-link');
var $editor_loader = $('#editor-loader');
var $page_delete_btn = $('#page-delete-btn');
var $mask = $('.mask');
var $summary = $('#document-summary');
var $list_btn = $('#document-list');

$editor_textarea.focus();

$edit_link.click(function (e) {
    $editor_textarea.show();
    $editor_preview.hide();
    $editor_loader.hide();
    $preview_link.removeClass('active');
    $edit_link.addClass('active');
});

$preview_link.click(function (e) {
    $editor_textarea.hide();
    $editor_preview.hide();
    $editor_loader.show();
    $edit_link.removeClass('active');
    $preview_link.addClass('active');
    $.post($preview_link.attr('link'), {
        content: $editor_textarea.val()
    }, function (result) {
        $editor_loader.hide();
        $editor_preview.show();
        $editor_preview.html(result);
    });
});

$page_delete_btn.click(function () {
    if (!confirm('确认删除此页面 ?')) {
        return false;
    }
});

$('#page-links').insertBefore($summary.find('> ul > li:first-child'));

$list_btn.click(function () {
    $summary.show();
    $mask.show();
});

$mask.click(function () {
    $summary.hide();
    $mask.hide();
});

