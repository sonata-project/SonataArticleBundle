(function ($, window, document, undefined) {
    // Create the defaults once
    var pluginName = 'fragmentList',
        defaults = {
            propertyName: 'value',
            activeState: 'is-active',
            errorState: 'has-errors',
            tmpState: 'is-tmp',
            formSource: '#forms',
            formIdentifier: 'data-fragment-form',
            formRemoveName: '[name*="[_delete]"]',
            formPositionName: '[name*="[position]"]',
            addButtonSource: '#addButtonSource',
            pageScrollOffset: 50,
            pageScrollDuration: 1000,
            getAddUrl: null,
            addedCallback: function(html) {}
        };

    // The actual plugin constructor
    function Plugin(element, options) {
        this.element = element;
        this.options = $.extend({}, defaults, options) ;
        this._defaults = defaults;
        this._name = pluginName;
        this.init();
    }

    Plugin.prototype = {
        /**
         * Constructor
         */
        init: function() {
            this.setElements();
            this.build();
            this.setEvents();
            this.setDraggableElements();
        },

        /**
         * Cache DOM elements to limit DOM parsing
         */
        setElements: function() {
            this.$list = $(this.element);
            this.$form = this.$list.closest('form');
            this.$formSource = $(this.options.formSource);
            this.$addButton = $(this.options.addButtonSource);
            this.setFormListElement();
            this.$activeFragment = null;
        },

        setFormListElement: function() {
            this.$formsList = this.$formSource.find('[' + this.options.formIdentifier + ']');
        },

        /**
         * Set events and delegates
         */
        setEvents: function() {
            this.$list.on('click', '[data-fragment]', $.proxy(this.setActiveOnClick, this));
            this.$list.on('click', '[data-frag-remove]', $.proxy(this.remove, this));
            this.$addButton.on('click', $.proxy(this.add, this));
        },

        /**
         * Items are draggable
         */
        setDraggableElements: function() {
            var self = this;

            this.$list.sortable({
                axis: 'y',
                cursor: 'move',
                helper: 'clone',
                opacity: '.5',
                forcePlaceholderSize: true,
                update: function(event, ui) {
                    self.resetOrder();
                }
            });
        },

        /**
         * Create a new fragment
         */
        add: function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Before adding, be sure the current fragment is valid
            if(!this.checkValidity()) return;

            // Submit the new fragment
            var self = this;
            jQuery(this.$form).ajaxSubmit({
                url: this.options.getAddUrl(this.$list.children().length),
                type: 'POST',
                dataType: 'html',
                data: { _xml_http_request: true },
                success: function(html) {
                    if(!html.length) return;
                    // Append the new fragment form
                    self.$formSource.append(html);
                    // Trigger callback
                    self.options.addedCallback();
                    // Append the new fragment
                    self.added(html);
                }
            });

            return false;
        },

        /**
         * A new fragment was added
         */
        added: function(node) {
            var $node = $(node),
                id = $node.attr(this.options.formIdentifier),
                data = $node.data('formdata');

            // Update form list
            this.setFormListElement();
            // Append the new fragment
            this.appendFragment(id, data, true);
            // Reorder
            this.resetOrder();
        },

        /**
         * Remove an item (cross link clicked)
         */
        remove: function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $target = $(e.currentTarget),
                $frag = $target.closest('[data-fragment]'),
                id = $frag.data('fragId'),
                $form = this.getFormByFragmentId(id),
                $cb = $form.find(this.options.formRemoveName);

            // Remove a persisted fragment (just check delete checkbox)
            if($form.data('formTmp')) {
                // Check the delete checkbox
                $cb.prop('checked', true).attr('checked', true);
                console.log($form.find(this.options.formRemoveName));

                // Hide form block
                $form.hide();
            // Remove a new fragment (not already saved)
            } else {
                $form.remove();
                this.setFormListElement();
            }

            // Remove list element
            $frag.remove();

            // Removed the last element ?
            if(!this.$list.children().length) {
                this.$activeFragment = null;
            // Removed the current visible item ?
            } else if(id === this.$activeFragment.data('fragId')) {
                this.setActive(this.$list.children(':first-child'));
            }

            // Reorder
            this.resetOrder();

        },

        /**
         * Set an item as active
         */
        setActiveOnClick: function(e) {
            var $target = $(e.currentTarget);

            // Before adding, be sure the current fragment is valid
            if(this.checkValidity()) this.setActive($target);
        },

        /**
         * Get form matching to a fragment
         */
        getFormByFragmentId: function(fragId) {
            var self = this,
                found = false;

            this.$formsList.each(function() {
                var $this = $(this);

                if(fragId === $this.attr(self.options.formIdentifier)) {
                    found = $this;
                }
            });

            return found;
        },

        /**
         * Set an item as active
         */
        setActive: function($target) {
            // List element
            if(this.$activeFragment) this.$activeFragment.removeClass(this.options.activeState);

            this.$activeFragment = $target;
            this.$activeFragment.addClass(this.options.activeState);

            // Form element
            this.$formsList.hide();
            this.getFormByFragmentId($target.data('fragId')).show();
        },

        /**
         * Build initial list contents
         */
        build: function() {
            var self = this;

            this.$formsList.each(function() {
                var $this = $(this),
                    formdata = $this.data('formdata'),
                    errors = parseInt($this.data('errors'), 10);

                self.appendFragment($this.attr(self.options.formIdentifier), formdata, false, errors);
            });
        },

        /**
         * Append a new fragment to the list
         */
        appendFragment: function(id, data, isNew, hasErrors) {
            var $fragment = $($('#fragmentTpl').clone()).removeClass('hide').removeAttr('id');

            // Fill template strings
            for (var item in data) {
                $fragment.find('[data-' + item + ']').text(data[item]);
            }

            // Set current template id
            $fragment.attr('data-frag-id', id);

            // New item, add temporary fragment state
            if(isNew) {
                $fragment.addClass(this.options.tmpState);
            }

            // Show errors ?
            if(hasErrors) {
                $fragment.addClass(this.options.errorState);
            }

            // Is it the first item OR force active state
            if(!this.$activeFragment || isNew) this.setActive($fragment);

            // Append to the DOM
            this.$list.append($fragment);
        },

        /**
         * Reset order after and item is dropped or removed
         */
        resetOrder: function() {
            var self = this;

            this.$list.children('li').each(function(index) {
                var $this = $(this),
                    $form = self.getFormByFragmentId($this.data('fragId')),
                    $positionField = $form.find(self.options.formPositionName);

                $positionField.val(index);
            });
        },

        /**
         * Check validity for a fragment (HTML5 validation only)
         */
        checkValidity: function() {
            var isValid = true,
                $form;

            // Reset validity
            if(this.$invalidFields) this.setValid(this.$invalidFields, true);

            // Currently no fragments, current fragment is valid
            if(!!this.$activeFragment) {
                // Check current visible form
                $form = this.getFormByFragmentId(this.$activeFragment.data('fragId'));
                // Get invalid fields for this form
                this.$invalidFields = $form.find('input:invalid, textarea:invalid');
                // Is the fragment valid ?
                isValid = !this.$invalidFields.length;
                // Show invalid fields
                if(!isValid) this.setValid(this.$invalidFields, false);
            }

            return isValid;
        },

        /**
         * Set a field valid / invalid into a form
         */
        setValid: function($fields, isValid) {
            var $group = $fields.closest('.form-group');

            if(!isValid) {
                $group.addClass('has-error');
                this.scrollTo($group);
            } else {
                $group.removeClass('has-error');
            }
        },

        /**
         * Scroll to a DOM element
         */
        scrollTo: function($element) {
            var offset = this.options.pageScrollOffset;

            $('html, body').animate({
                scrollTop: $element.offset().top - offset
            }, this.options.pageScrollDuration);
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
            }
        });
    };
})(jQuery, window, document);
