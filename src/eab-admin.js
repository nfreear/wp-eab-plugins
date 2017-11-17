/*!
  © Nick Freear, 07-November-2017.
*/

window.jQuery(function ($) {
  'use strict';

  var $eabAdminPage = $('.wp-admin.post-type-eab_bulletin');
  var $eabJson = $('#eab-admin-json');

  var $editor = $eabAdminPage.find('.wp-editor-area');
  var $postTitle = $eabAdminPage.find('input[ name = post_title ]');
  var $postName = $eabAdminPage.find('input[ name = post_name ]');
  // var $slug = $eabAdminPage.find('#edit-slug-box a');

  var config = JSON.parse($eabJson.text() || null); // '{}');

  $postTitle.attr({
    pattern: '[A-Z][a-z]+ 20\\d{2}',
    title: "Month YEAR, for example, 'November 2017'"
  });

  if (config && config.use_template) {
    if (!$editor.val()) { $editor.val(config.template); }
    if (!$postTitle.val()) { $postTitle.val(config.default_title); }
    if (!$postName.val()) { $postName.val(config.default_name); }

    // $slug.text(config.site_url + config.slug);
  }

  labelEditorMenuItems($);

  console.warn('eab.', $editor, config);
});

function labelEditorMenuItems ($) {
  if ($) return;

  window.setTimeout(function () {
    var $eabAdminPage = $('.wp-admin.post-type-eab_bulletin');
    var $btn = $('.mce-menubtn button');
    // var $menu_items = $('.mce-menu .mce-menu-item');

    console.warn('>> btn.', $btn); //, $menu_items);

    $eabAdminPage.on('click', '.mce-menubtn button', function () {
      var $menuItems = $('.mce-menu .mce-menu-item'); // $eabAdminPage.find('.mce-menu .mce-menu-item');

      $menuItems.each(function (idx, el) {
        var $item = $(el);
        var text = $item.text().replace(/&nbsp;|\(.+/g, '');

        $item.attr('data-text', text);

        console.warn($item);
      });

      console.warn('>> timeout');
    });
  }, 3000);
}
