/**
 * This function allows to edit text field withoud page reload
 *
 * @option String ajaxMethod - name of ajaxMethod which will be called to save data
 * @option String recordId - record identifier
 * @option String input - sets type of input , which will replace text field. Now availible "textarea" and "default" (ordinary <input type="text">)
 * @option Function callback - function, which will be executed after text save
 * 
 * Pay attention, that changes will be saved automaticaly on "blur" event, if options "saveBut" and "cancelBut" are empty
 */     
         
(function($) {

$.fn.extend({

    editableByClick: function(settings) {
      settings = $.extend(
        {
          type: 'textarea',
          ajaxMethod: '',
          recordId: '',
          fieldName: '',
          tableName: '',
          template: ''
        },
        settings
      );
      var control = $(this);
      return control.click( function() { control.showEditor(settings, $(this).attr('id')); } );
    },

    showEditor: function(settings, id) {
      var control = $(this);

      var content = control.html();
      if (control.children().html()) {
          content = control.children().html().replace(/<br.*?>/gi, "\n");
      }

      content = content.replace(/"/g, "&quot;");

      control.unbind('click');

      if(settings.template != ''){
        tmpl = settings.template;
        tmpl = tmpl.replace(/\{\$field\.id\}/gi, id);
        tmpl = tmpl.replace(/\{\$field\.content\}/gi, content);
        control.html(tmpl);
      } else {
        if(settings.type == 'auto'){
          var re = /\n/;
          if(content && re.test(content)){
            control.html('<textarea style="width:100%; height:100%;" id="' + id + '_" name="' + id + '_">' + content + '</textarea>' +
                         '<input id="' + id + '_old" name="' + id + '_old" type="hidden" value="">' +
                         (!settings.autosave ? 
                             '<input id="' + id + '_save" name="' + id + '_save" type="button" class="editable_save" value="Save">' +
                             '<input id="' + id + '_cancel" name="' + id + '_cancel" type="button" class="editable_cancel" value="Cancel">' :
                         '')
                        );            
          }
          else{
            control.html('<input id="' + id + '_" name="' + id + '_" type="text" style="width:700px;" value="' + content + '"/>' +
                         '<input id="' + id + '_old" name="' + id + '_old" type="hidden" value="">' + 
                         (!settings.autosave ? 
                             '<input id="' + id + '_save" name="' + id + '_save" type="button" class="editable_save" value="Save">' +
                             '<input id="' + id + '_cancel" name="' + id + '_cancel" type="button" class="editable_cancel" value="Cancel">' :
                         '')
                        );
          }
        } else if (settings.type == 'textarea') {
          control.html('<textarea style="width:100%; height:100%;" id="' + id + '_" name="' + id + '_">' + content + '</textarea>' +
                       '<input id="' + id + '_old" name="' + id + '_old" type="hidden" value="">' +
                       (!settings.autosave ? 
                           '<input id="' + id + '_save" name="' + id + '_save" type="button" class="editable_save" value="Save">' +
                           '<input id="' + id + '_cancel" name="' + id + '_cancel" type="button" class="editable_cancel" value="Cancel">' :
                       '')
                      );
        } else if (settings.type == 'text') {
          control.html('<input id="' + id + '_" name="' + id + '_" type="text" style="width:700px;" value="' + content + '"/>' +
                       '<input id="' + id + '_old" name="' + id + '_old" type="hidden" value="">' + 
                       (!settings.autosave ? 
                           '<input id="' + id + '_save" name="' + id + '_save" type="button" class="editable_save" value="Save">' +
                           '<input id="' + id + '_cancel" name="' + id + '_cancel" type="button" class="editable_cancel" value="Cancel">' :
                       '')
                      );
        } else if (settings.type == 'combo') {
            var html =  '<select id="' + id + '_" name="' + id + '_" style="width:200px;">' +
                        '<option value="0" selected="selected" disabled="disabled">Please wait...</option>' +
                        '</select>';
            html += '<input id="' + id + '_old" name="' + id + '_old" type="hidden" value="">';
            if (!settings.autosave) {
                html += '<input id="' + id + '_save" name="' + id + '_save" type="button" class="editable_save" value="Save">' +
                '<input id="' + id + '_cancel" name="' + id + '_cancel" type="button" class="editable_cancel" value="Cancel">';
            }
            control.html(html);
            set_combo_options_list(id + '_', settings, get_combo_value(id, settings.combo_table));
            settings['fieldName'] = settings['combo_ext_key'];
       }
      }

      if (!settings.autosave) {
          control = $('#' + id + '_save');
          control.click( function() { control.hideEditor(settings, id, true); } );

          control = $('#' + id + '_cancel');
          control.click( function() { control.hideEditor(settings, id, false); } );
      } else {
          control = $('#' + id + '_');
          control.blur( function() { control.hideEditor(settings, id, true); } );
      }

      control = $('#' + id + '_old');
      control.val(content);

      control = $('#' + id + '_');
      control.focus();

      return control;
    },

    hideEditor: function(settings, id, save) {
      if (save) {
        var control = $('#' + id + '_');
        var value = control.val();

        var ajaxBaseUrl = document.location.href;
        var ajaxTemp = ajaxBaseUrl.lastIndexOf("/") + 1;
        ajaxBaseUrl = ajaxBaseUrl.substring(0, ajaxTemp);
        requestStr = ajaxBaseUrl + "?__ajaxMethod=" + settings.ajaxMethod;

        a = $.getJSON(
          requestStr,
          {
              value: value.replace(/\n/gi, '<br>')
            , recordId: settings.recordId
            , fieldName: settings.fieldName
            , tableName: settings.tableName
          }
          , function(data, textStatus) {
              var control = $('#' + id);
              if (data.result == 0) {
                control.html(data.value);
                return control.click( function() { control.showEditor(settings, id); } );
              } else
              if (data.result == 1) {
                alert(data.errorMessage);
                control.text(value);
                return control.click( function() { control.showEditor(settings, id); } );
              } else
              if (data.result == -1) {
                alert(data.errorMessage);
                control = $('#' + id + '_');
                control.focus();
              } else {
                control.text(value);
                return control.click( function() { control.showEditor(settings, id); } );
              }
            }
        );

      } else {
          var control = $('#' + id + '_old');
          var oldvalue = control.val();
          control = $('#' + id);
          control.html(oldvalue);
          return control.click( function() {
              $(this).click( function(){ control.showEditor(settings, id); } );
          });
      }
    }

})

})(jQuery);