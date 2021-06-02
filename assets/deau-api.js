(function($) {
  var new_deau_api_shortcode = '#new-deau_api_shortcode';
  var deau_api_shortcode_one = 'deau_api-shortcode-one';
  $(document).on('click', new_deau_api_shortcode, function() {
    var code_count = Number( $(this).attr('data-deau_api_shortcodes-count') ); /* 動的に取得するため data() ではなく attr() が必要 */
    var placeholder_slug = "name";
    var placeholder_code = "<dl><dt>法人名</dt><dd>[\'法人名\']</dd><dt>法人番号</dt><dd>[\'法人番号\']</dd><dt>本店所在地</dt><dd>[\'本店所在地\']</dd></dl>";
    var code_delete_text = $(this).data('deau_api_shortcode-delete_button_text');
    $("#deau_api-shortcodes").append('<div class="'+deau_api_shortcode_one+'"><input type="text" id="deau_api-shortcode-slug-'+code_count+'" name="deau_api_shortcodes['+code_count+'][slug]" placeholder="'+placeholder_slug+'"><textarea id="deau_api-shortcode-code-'+code_count+'" name="deau_api_shortcodes['+code_count+'][code]" cols="100" rows="10" placeholder="'+placeholder_code+'"></textarea><span class="deau_api-shortcode-delete button">'+code_delete_text+'</span></div>');
    $(this).attr('data-deau_api_shortcodes-count', Number( code_count + 1 ) ); /* 動的に変更するため data() ではなく attr() が必要 */
  });
  $(document).on('click', '.deau_api-shortcode-delete', function() {
    var code_delete_confirm = $(new_deau_api_shortcode).data('deau_api_shortcode-delete_confirm');
    var code_slug = $(this).closest('.'+deau_api_shortcode_one).find('input').val();
    var result = window.confirm( code_slug + code_delete_confirm );
    if(result){
      $(this).closest('.'+deau_api_shortcode_one).remove();
    }
  });
  $(document).on('click', '.deau_api-key-copy', function() {
    $(this).addClass('deau-copied');
    var clipboard = $('<textarea></textarea>');
    clipboard.addClass('clipboard');
    clipboard.html( $(this).text() );
    $(this).append(clipboard);
    clipboard.select();
    document.execCommand('copy');
    clipboard.remove();
    $(this).removeClass('deau-copied');
  });
})(jQuery);
