(function ($) {
    $.fn.pickicon = function (options) {
        // This is the easiest way to have default options.
        var settings = $.extend({
            iconSets: {}
        }, options);
        var $ele = $(this);
        var methods = {
            ele: {},
            icons: [],
            init: function () {
                var $this = this;
                this.setIcons();
                this.cacheDOM();
                this.ele.container.on('keyup', '.input-filter', function(){
                    var filter = $(this).val().toLowerCase();
                    if(filter != ''){
                        $('.pickicon-wrapper a.icon').hide();
                        $('.pickicon-wrapper a.icon[title*="'+filter+'"').show();
                    }else{
                        $('.pickicon-wrapper a.icon').show();
                    }
                });
                this.ele.container.on('click', 'a.icon', function(){
                    $ele.val($(this).data("value"));
                    $this.ele.selectedIcon.html('<i class="'+$(this).data("value")+'"></i>');
                    $('.pickicon-wrapper a.icon').removeClass('active');
                    $(this).addClass('active');
                });
            },
            cacheDOM: function () {
                $ele.wrap('<div class="pickicon-container">');
                $ele.after('<div class="pickicon-wrapper">');
                this.ele.selectedIcon = $('<a href="javascript:void(0);" class="icon selected-icon">');
                $ele.after(this.ele.selectedIcon);
                this.ele.container = $ele.siblings(".pickicon-wrapper");
                this.ele.filterWrap = $('<div class="pick-icon-filter">');
                this.ele.iconsWrap = $('<div class="pick-icon-icons">');
                this.ele.icon = $('<a href="javascript:void(0);" class="icon">');
                this.ele.iconContent = $('<i>');
                this.ele.filter = $('<input type="text" size="60" name="filter" class="input-filter" placeholder="Enter keyword to search icon">');
            },
            setIcons: function(){
                var $this = this;
                for( var key in settings.iconSets){
                    $.getJSON(settings.iconSets[key], function(data){
                        $this.icons.push(data);
                        $this.render();
                    });
                }
            },
            render: function () {
                var $this = this;
                this.ele.iconsWrap.empty();
                this.ele.filterWrap.append(this.ele.filter);
                this.ele.container.append(this.ele.filterWrap);
                this.ele.container.append(this.ele.iconsWrap);
                $(this.icons).each(function(i, icons){
                    for (var key in icons.icons){
                        var icon = $this.ele.icon.clone();
                        var iconContent = $this.ele.iconContent.clone();
                        icon.attr('title', icons.icons[key]);
                        icon.attr('data-value', icons.commonClass+' '+icons.icons[key]);
                        iconContent.addClass(icons.commonClass);
                        iconContent.addClass(icons.icons[key]);
                        icon.append(iconContent);
                        $this.ele.iconsWrap.append(icon);
                    }
                });
                $this.ele.selectedIcon.html('<i class="'+$ele.val()+'"></i>');
            }
        };
        methods.init();
        return this;
    };
})(jQuery);
