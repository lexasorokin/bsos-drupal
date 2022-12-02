var $activeCanvas, $currentParent;
(function(Drupal, debounce, $, drupalSettings, CKEDITOR) {
  'use strict';
  var gmapLoaded = 0;
  var gmapApiKey = '';
  var gmapMarkerIcon = '';
  Drupal.VisualEditor = [];
  $.fn.VisualEditor = function(format) {
    var $textarea = $(this);
    var editorIndex = Drupal.VisualEditor.length;
    var VisualEditor = {
      textarea: null,
      storage: [],
      settings: null,
      currentParent: null,
      ele: {
        document: $(document)
      },
      init: function($textarea) {
        var $this = this;
        this.settings = format.editorSettings;
        this.ele.textarea = $textarea;
        try {
          this.storage = (this.ele.textarea.attr('data-editor-value-original') != '') ? JSON.parse(this.ele.textarea.attr('data-editor-value-original')) : [];
        } catch (e) {
          this.storage = [];
        }
        this.cacheDOM();
        this.bindEvents();
        this.ele.textarea.after(this.ele.canvas);
        this.renderComponents(this.storage, this.ele.canvas);
        this.ele.canvas.find('.canvas-footer').append(this.ele.addRowButton);
        this.ele.canvas.find('.canvas-footer').append(this.ele.addCodeButton);
        this.ele.canvas.attr("data-index", editorIndex);
        this.ele.canvas.attr("data-textarea", "#"+this.ele.textarea.attr("id"));
      },
      cacheDOM: function() {
        var $this = this;
        this.ele.canvas = $("<div class=\"ve-canvas component\"><div class=\"canvas-body\"></div><div class=\"canvas-footer\"></div></div>");
        this.ele.addRowButton = $("<button type=\"button\" title=\"Add row\" class=\"btn btn-circle btn-primary btn-sm\"><i class=\"fa fa-plus\" aria-hidden=\"true\"></i></button>");
        this.ele.addCodeButton = $("<button type=\"button\" title=\"Add Css/Js\" class=\"btn btn-circle btn-primary btn-sm cssjs config-component\"><i class=\"fa fa-code\" aria-hidden=\"true\"></i></button>");
        var lastItem = this.storage[this.storage.length-1];
        var cssJsSettings = this.settings.buttons.code[0];
        if(this.storage.length > 0 && lastItem.type == 'code'){
          lastItem.editorIndex = editorIndex;
          cssJsSettings = lastItem;
        }
        this.ele.addCodeButton.attr('data-settings', JSON.stringify(cssJsSettings));
        this.ele.addButton = $("<button type=\"button\" title=\"Add component\" class=\"btn btn-primary pull-right btn-xs\"><i class=\"fa fa-plus\" aria-hidden=\"true\"></i></button>");
        this.ele.componentButton = $("<button type=\"button\" class=\"btn btn-default add-component\"><i class=\"fa fa-plus\" aria-hidden=\"true\"></i></button");
        this.ele.cloneButton = $("<button type=\"button\" class=\"btn btn-xs btn-default clone-component pull-right\"><i class=\"fa fa-clone\" aria-hidden=\"true\"></i></button");
        this.ele.deleteButton = $("<button type=\"button\" class=\"btn btn-xs btn-danger delete-component pull-right\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></button");
        this.ele.configButton = $("<button type=\"button\" class=\"btn btn-xs btn-info config-component pull-right\"><i class=\"fa fa-gear\" aria-hidden=\"true\"></i></button");
        this.ele.minMaxButton = $("<button type=\"button\" class=\"btn btn-xs btn-default minmax-component pull-right\"><i class=\"fa fa-window-minimize\" aria-hidden=\"true\"></i></button");
        this.ele.components = $("<div class=\"components\">");
        this.ele.panel = $("<div class=\"component\"><div class=\"panel panel-default\">\n\
                                        <div class=\"panel-heading clearfix\"><h3 class=\"panel-title pull-left\"></h3></div>\n\
                                        <div class=\"panel-body\"></div>\n\
                                        </div></div>");
        this.ele.row = $('<div id="ve-rows" class="components rows">');
        this.ele.column = $('<div id="ve-columns" class="components columns">');
        this.ele.widget = $('<div id="ve-widgets" class="components widgets">');
        $.each(this.settings.buttons.row, function(i, item) {
          var button = $this.ele.componentButton.clone();
          item.editorIndex = editorIndex;
          button.attr('data-settings', JSON.stringify(item));
          button.html('<span class="icon '+item.iconClass+'"></span><span class="label">'+item.name+'</span>');
          $this.ele.row.append(button);
        });
        $.each(this.settings.buttons.column, function(i, item) {
          var button = $this.ele.componentButton.clone();
          item.editorIndex = editorIndex;
          button.attr('data-settings', JSON.stringify(item));
          button.html('<span class="icon '+item.iconClass+'"></span><span class="label">'+item.name+'</span>');
          $this.ele.column.append(button);
        });
        $.each(this.settings.buttons.widget, function(i, item) {
          var button = $this.ele.componentButton.clone();
          item.editorIndex = editorIndex;
          button.attr('data-settings', JSON.stringify(item));
          button.html('<span class="icon '+item.iconClass+'"></span><span class="label">'+item.name+'</span>');
          $this.ele.widget.append(button);
        });
        this.renderComponentButtons();
      },
      bindEvents: function() {
        var $this = this;
        this.ele.document.once("addComponent").on("click", ".add-component", function(e) {
          var settings = $(e.currentTarget).data("settings");
          $activeCanvas = $(e.currentTarget).closest(".ve-canvas");
          settings.adminClass = '';
          if (settings.type == 'column') {
            var grid = settings.grid.split('_');
            for (var i = 0; i < grid.length; i++) {
              var colSettings = settings;
              colSettings.adminClass += ' col col-lg-' + grid[i];
              colSettings.name = 'Col '+grid[i];
              colSettings.content = {
                device: [{
                    lg: grid[i],
                    md: grid[i],
                    sm: grid[i],
                    xs: 12
                  }]
              };
              $this.renderComponents([colSettings], $(e.currentTarget).closest(".component"));
            }
          } else {
            $this.renderComponents([settings], $(e.currentTarget).closest(".component"));
            if(settings.type == 'widget'){
              $(e.currentTarget).closest(".component").find(".panel-body .config-component:last()").trigger("click");
            }
          }
          $this.updateStorage($this.ele.canvas.find("> .canvas-body > .component"), []);
        });
        this.ele.document.once("deleteComponent").on("click", ".delete-component", this.deleteComponent.bind(this));
        this.ele.document.once("cloneComponent").on("click", ".clone-component", this.cloneComponent.bind(this));
        this.ele.document.once("configComponent").on('click', ".config-component", this.openComponentForm.bind(this));
        this.ele.document.once("minMaxComponent").on('click', ".minmax-component", this.toggleMinMax.bind(this));
      },
      toggleMinMax: function(e){
        $(e.currentTarget).closest(".panel").find(".panel-body").toggleClass('hide');
        $(e.currentTarget).find(".fa").toggleClass("fa-window-minimize").toggleClass("fa-window-maximize");
      },
      renderComponentButtons: function() {
        var $this = this;
        this.ele.addRowButton.popover({
          html: true,
          content: $this.ele['row'],
          placement: 'auto top',
          trigger: 'focus',
          delay: {"hide": 200}
        });
      },
      renderComponents: function(items, $target) {
        var $this = this;
        $(items).each(function(i, item) {
          if(item.type == 'code'){ return; }
          var $panel = $this.ele.panel.clone();
          var $cloneButton = $this.ele.cloneButton.clone();
          var $deleteButton = $this.ele.deleteButton.clone();
          var $configButton = $this.ele.configButton.clone();
          var $minMaxButton = $this.ele.minMaxButton.clone();
          var componentTitle = item.attributes.title ? item.attributes.title : item.name;
          $panel.attr('data-settings', JSON.stringify(item));
          $panel.find("> .panel > .panel-heading > .panel-title").text(componentTitle);
          $panel.find("> .panel > .panel-heading").append($minMaxButton);
          $panel.find("> .panel > .panel-heading").append($deleteButton);
          $panel.find("> .panel > .panel-heading").append($cloneButton);
          $panel.find("> .panel > .panel-heading").append($configButton);
          if (item.type == 'widget') {
            $panel.find('.panel-body').html('<p><strong>Widget: </strong>' + item.name + '</p>');
          }
          item.adminClass = item.adminClass? item.adminClass + ' ve-'+item.type : 've-'+item.type ;
          $panel.addClass(item.adminClass);
          if (item.accepts && item.accepts.length) {
            var $wrapper = $this.ele.components.clone();
            var $addButton = $this.ele.addButton.clone();
            $addButton.popover({
              html: true,
              content: $this.ele[item.accepts],
              placement: 'auto bottom',
              trigger: 'focus',
              delay: {"hide": 200}
            });
            
            if(item.type == 'column'){
              $panel.addClass("col");
              if(item.content.device){
                $.each(item.content.device[0], function(key, val){
                  $panel.addClass('col-'+key+'-'+val);
                });
              }else{
                $.each(item.content, function(key, val){
                  var classStr = key.replace(/_/g, "-");
                  $panel.addClass(classStr+"-"+val);
                });
              }
              if(item.attributes.class){
                $panel.addClass(item.attributes.class);
              }
            }
            $panel.find("> .panel > .panel-heading").append($addButton);
          } else {
            $panel.find(".panel-footer").remove();
          }
          if (item.children && item.children.length) {
            $this.renderComponents(item.children, $panel);
          }
          if (item.type != 'row') {
            $target.find("> .panel > .panel-body").append($panel);
            $target.find(".col").parent().addClass("row");
          } else {
            $target.find(".canvas-body").append($panel);
          }
        });

        $('.ve-row > .panel > .panel-body, .canvas-body').sortable({
          cursor: 'move',
          update: function( event, ui ){
            $this.updateStorage($this.ele.canvas.find("> .canvas-body > .component"), []);
          }
        });
        $('.ve-column > .panel > .panel-body').sortable({
          cursor: 'move',
          connectWith: '.ve-column > .panel > .panel-body',
          update: function( event, ui ){
            $this.updateStorage($this.ele.canvas.find("> .canvas-body > .component"), []);
          }
        });
      },
      deleteComponent: function(e) {
        var $ele = $(e.target).closest(".component");    
        $activeCanvas = $ele.closest(".ve-canvas");
        $ele.remove();
        this.updateStorage($activeCanvas.find("> .canvas-body > .component"), []);
      },
      cloneComponent: function(e) {
        var $ele = $(e.target).closest(".component");        
        $activeCanvas = $ele.closest(".ve-canvas");
        var settings = JSON.parse($ele.attr("data-settings"));
        this.renderComponents([settings], $ele.parent().parent().parent());
        this.updateStorage($activeCanvas.find("> .canvas-body > .component"), []);
      },
      openComponentForm: function(e) {
        e.preventDefault();
        var $ele = ($(e.currentTarget).hasClass("cssjs"))?$(e.currentTarget):$(e.currentTarget).closest(".component");
        var settings = $ele.attr('data-settings');    
        $activeCanvas = $ele.closest(".ve-canvas");
        $(".visual-editor-component-utility-form").remove();
        $currentParent = $ele;
        $(e.currentTarget).parent().append(this.settings.componentUtilityForm);
        this.ele.document
                .find('.' + this.settings.componentUtilityFormWrap + ' .visual-editor-settings')
                .val(settings);
        Drupal.behaviors.AJAX.attach($('.use-ajax-submit'), []);
        this.ele.document.find('.use-ajax-submit').once('useAjaxSubmit').trigger('click');
      },
      updateStorage: function($selector, storage) {
        var $this = this;
        $selector.each(function(i) {
          var settings = JSON.parse($(this).attr('data-settings'));
          settings.children = [];
          if ($(this).find('>.panel>.panel-body>.component').length > 0) {
            settings.children = $this.updateStorage($(this).find('>.panel>.panel-body>.component'), []);
          }
          $(this).attr('data-settings', JSON.stringify(settings));
          storage.push(settings);
        });
        if(storage.length == 0 || storage[0].type == 'row'){
          storage.push(JSON.parse(this.ele.addCodeButton.attr('data-settings')));
        }
        $($activeCanvas.data("textarea")).val(JSON.stringify(storage)).trigger('change');
        $($activeCanvas.data("textarea")).attr('data-editor-value-original', JSON.stringify(storage));
        return storage;
      },
      destroy: function() {
        this.updateStorage($activeCanvas.find("> .canvas-body > .component"), []);
        this.ele.canvas.remove();
        $(".btn").unbind("click");
      },
      getColClasses: function(colClasses){
        var classes = 'component col';
        $.each(colClasses.device[0], function(i, val){
          if(val != ''){
            var className = 'col-'+i+'-'+val;
            classes += ' '+className;
          }
        });
        return classes;
      }
    };
    VisualEditor.init($textarea);
    return VisualEditor;
  };

  /**
   * @namespace
   */
  Drupal.editors.visual_editor = {
    /**
     * Editor attach callback.
     *
     * @param {HTMLElement} element
     *   The element to attach the editor to.
     * @param {string} format
     *   The text format for the editor.
     *
     * @return {bool}
     *   Whether the call to `CKEDITOR.replace()` created an editor or not.
     */
    attach: function(element, format) {
      gmapApiKey = format.editorSettings.ve_config.gmap.key ? format.editorSettings.ve_config.gmap.key : '';
      gmapMarkerIcon = format.editorSettings.ve_config.gmap.icon ? format.editorSettings.ve_config.gmap.icon : false;
      Drupal.VisualEditor.push($(element).VisualEditor(format));
    },
    /**
     * Editor detach callback.
     *
     * @param {HTMLElement} element
     *   The element to detach the editor from.
     * @param {string} format
     *   The text format used for the editor.
     * @param {string} trigger
     *   The event trigger for the detach.
     *
     * @return {bool}
     *   Whether the call to `CKEDITOR.dom.element.get(element).getEditor()`
     *   found an editor or not.
     */
    detach: function(element, format, trigger) {
      if (trigger !== 'serialize') {
        $activeCanvas = $(element).parent().find("> .ve-canvas");
        Drupal.VisualEditor[$activeCanvas.data("index")].destroy();
      }
    },
    /**
     * Reacts on a change in the editor element.
     *
     * @param {HTMLElement} element
     *   The element where the change occured.
     * @param {function} callback
     *   Callback called with the value of the editor.
     *
     * @return {bool}
     *   Whether the call to `CKEDITOR.dom.element.get(element).getEditor()`
     *   found an editor or not.
     */
    onChange: function(element, callback) {

    }
  };

  $.fn.updateComponent = function(settings) {
    $(this).val(JSON.stringify(settings));
    settings = JSON.parse(settings);
    var editor = Drupal.VisualEditor[settings.editorIndex];
    if(settings.type != 'code'){
      var componentTitle = settings.attributes.title ? settings.attributes.title : settings.name;
      $currentParent.find("> .panel > .panel-heading > .panel-title").text(componentTitle);
      if (settings.type == 'widget') {
        $currentParent.find('.panel-body').html('<p><strong>Widget: </strong>' + settings.name + '</p>');
      }
      if (settings.type == 'column') {
        var colClasses = editor.getColClasses(settings.content);
        $currentParent.attr("class", colClasses);
        settings.adminClass = colClasses;
      }
      $currentParent.attr('data-settings', JSON.stringify(settings));
      editor.updateStorage($currentParent.closest(".canvas-body").find("> .component"), []);
      editor.storage.push(settings);
    }else{
      var storageLen = editor.storage.length;
      editor.ele.addCodeButton.attr('data-settings', JSON.stringify(settings));
      if(editor.storage.length > 0 && editor.storage[storageLen-1].type == 'code'){
        editor.storage[storageLen-1] = settings;
      }else{
        editor.storage[storageLen] = settings;
      }
      editor.updateStorage(editor.ele.canvas.find("> .canvas-body > .component"), []);
    }
  };

  Drupal.behaviors.visualEditorBehavior = {
    attach: function(context, settings) {
      if(typeof CodeMirror != 'undefined'){
        setTimeout(function(){
          $(".codemirror").once('editor').each(function(){
            var $codeArea = $(this);
            var mode = $codeArea.data("mode");
            var editor = CodeMirror.fromTextArea($codeArea.get(0), {
              mode: mode,
              tabSize: 2,
              gutters: ['CodeMirror-linenumbers'],
              lineNumbers: true
            });
            editor.on("change", function(cm){
              $codeArea.val(cm.getValue()).trigger("change");
            });
          });
        }, 500);
      }
      if (Drupal.VisualEditor[0] && Drupal.VisualEditor[0].settings) {
        var editorPath = drupalSettings.path.baseUrl + Drupal.VisualEditor[0].settings.editorPath;
        if ($('.icp-auto').length > 0) {
          $('.icp-auto').once('pickicon').pickicon({iconSets: [editorPath + '/js/json/fa.json']});
        }
        if(typeof google === 'object' && typeof google.maps === 'object'){
          gmapLoaded = 1;
        }
        if (gmapLoaded == 0 && gmapApiKey != '') {
          $.getScript('//maps.googleapis.com/maps/api/js?key=' + gmapApiKey, function(data, textStatus, jqxhr) {
            $(".map-canvas").each(function() {
              $(this).veGmap({
                gmapKey: gmapApiKey,
                gmapMarkerIcon: gmapMarkerIcon
              });
            });
          });
        } else {
          $(".map-canvas").each(function() {
            $(this).veGmap({
              gmapKey: gmapApiKey,
              gmapMarkerIcon: gmapMarkerIcon
            });
          });
        }
        gmapLoaded++;
      }
      $.each(CKEDITOR.instances, function(key, editor){
        editor.on('key',function(e){
          $("#"+key).attr("data-editor-value-is-changed", "true");
        });
      });
    }
  };
})(Drupal, Drupal.debounce, jQuery, drupalSettings, CKEDITOR);