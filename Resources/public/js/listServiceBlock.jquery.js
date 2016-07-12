(function ($, window, document, undefined) {
    "use strict";

    var pluginName = "listServiceBlock",
        defaults = {};

    var removeService = function (event) {
        var $element = jQuery(event.target);
        var $main = $element.closest('.box-body');
        var $serviceListContainer=$element.closest('.service-collection');
        var count=$serviceListContainer.data('current-count');
        if ($main.find('.service-container').length == 0) {return false;}
        $element.closest('.service-container').closest('.form-group').remove();
        $main.find('.add-service-block-button').show();
        event.preventDefault();
    };

    var addService = function(event) {
        var $element = jQuery(event.target);
        var $main = $element.closest('.box-body');
        var $serviceListContainer = $main.find('.service-collection');
        var newWidget = $serviceListContainer.attr('data-prototype');
        var count = $serviceListContainer.data('current-count');
        newWidget = newWidget.replace(/__name__/g, count++);
        var $newWidget = $(newWidget);
        $newWidget.find('.remove-service-block-button').on("click", removeService);
        $newWidget.appendTo($serviceListContainer);
        $serviceListContainer.data('current-count', count);
        if ($main.find('.service-container').length >= 4) {$element.hide();}
        event.preventDefault();
    };

    function Plugin (element, options) {
        this.element = element;
        this.settings = $.extend({}, defaults, options);
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    $.extend(Plugin.prototype, {
        init: function () {
            this.setElements();
            this.setEvents();
        },
        setElements: function() {
            this.$addButtons    = $('[data-button-name="add-service-block-button"]').removeAttr('data-button-name');
            this.$removeButtons = $('.remove-service-block-button').removeClass('remove-service-block-button');
            this.$addButtons.each(function() {
                var $main = $(this).closest('.box-body');
                if ($main.find('.service-container').length >= 4) {$(this).hide();}
            });

        },
        setEvents: function() {
            this.$addButtons.on("click", addService);
            this.$removeButtons.on("click", removeService);
        }
    });

    /*!
     * Activate add and remove buttons on ListServiceBlock
     *
     */
    $.fn[ pluginName ] = function (options) {
        this.each(function() {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin(this, options));
            }
        });
        return this;
    };

})(jQuery, window, document);
