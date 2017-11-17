/*!
  © Nick Freear, 07-November-2017.
*/

window.jQuery(function ($) {
  'use strict';

  var $eab_admin_page = $('.wp-admin.post-type-eab_bulletin');
  var $eab_json = $('#eab-admin-json');

  var $editor = $eab_admin_page.find('.wp-editor-area');
  var $post_title = $eab_admin_page.find('input[ name = post_title ]');
  var $post_name = $eab_admin_page.find('input[ name = post_name ]');
  // var $slug = $eab_admin_page.find('#edit-slug-box a');

  var config = JSON.parse($eab_json.text() || null); // '{}');

  $post_title.attr({
    pattern: '[A-Z][a-z]+ 20\\d{2}',
    title: "Month YEAR, for example, 'November 2017'"
  });

  if (config && config.use_template) {
    if (!$editor.val()) { $editor.val(config.template); }
    if (!$post_title.val()) { $post_title.val(config.default_title); }
    if (!$post_name.val()) { $post_name.val(config.default_name); }

    // $slug.text(config.site_url + config.slug);
  }

  label_editor_menu_items($);

  console.warn('eab.', $editor, config);
});

function label_editor_menu_items ($) {
  if ($) return;

  window.setTimeout(function () {
    var $eab_admin_page = $('.wp-admin.post-type-eab_bulletin');
    var $btn = $('.mce-menubtn button');
    // var $menu_items = $('.mce-menu .mce-menu-item');

    console.warn('>> btn.', $btn); //, $menu_items);

    $eab_admin_page.on('click', '.mce-menubtn button', function () {
      var $menu_items = $('.mce-menu .mce-menu-item'); // $eab_admin_page.find('.mce-menu .mce-menu-item');

      $menu_items.each(function (idx, el) {
        var $item = $(el);
        var text = $item.text().replace(/&nbsp;|\(.+/g, '');

        $item.attr('data-text', text);

        console.warn($item);
      });

      console.warn('>> timeout');
    });
  }, 3000);
}
