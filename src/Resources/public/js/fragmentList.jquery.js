(function($) {
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
            translations: {},
            addedCallback: function(html) {}
        };

    // The actual plugin constructor
    function Plugin(element, options) {
        this.element = element;
        this.options = $.extend({}, defaults, options) ;
        this._defaults = defaults;
        this._name = pluginName;
        this._elementsIndex = 0;
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
            var divElement = $('<div>');
            divElement.attr('id', 'deleted-fragments');
            divElement.attr('style', 'display:none;');
            $('body')[0].appendChild(divElement[0]);
        },

        /**
         * Translates given label.
         *
         * @param {String} label
         * @return {String}
         */
        translate: function (label) {
            if (this.options.translations[label]) {
                return this.options.translations[label];
            }

            return label;
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

            if (this._elementsIndex === 0) {
                var maxIndex = 0;

                this.$list.children().each(function(i, child) {
                    var fragId = $(child).data('frag-id');
                    fragId = fragId.split('_');
                    var fragmentId = fragId.length > 0 ? fragId[fragId.length - 1] : null;

                    if (!isNaN(fragmentId) && fragmentId > maxIndex) {
                        maxIndex = parseInt(fragmentId);
                    }
                });

                this._elementsIndex = maxIndex;
            }

            this._elementsIndex++;
            Admin.log('[fragments|add] current index is ' + this._elementsIndex);

            // Submit the new fragment
            var self = this;
            jQuery(this.$form).ajaxSubmit({
                url: this.options.getAddUrl(this._elementsIndex),
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
            // Trigger admin shared setup on new form
            var addedForm = this.getFormByFragmentId(id);
            Admin.shared_setup(addedForm);
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

            if (!confirm(this.translate('remove_confirm'))) {
                return;
            }

            var $target = $(e.currentTarget),
                $frag = $target.closest('[data-fragment]'),
                id = $frag.data('fragId'),
                $form = this.getFormByFragmentId(id),
                $cb = $form.find(this.options.formRemoveName);

            // Remove a persisted fragment (just check delete checkbox)
            if (!$frag.hasClass('is-tmp')) {
                // Hide form block
                $form.hide();
                this.moveSelectedFragmentOutsideOfForm(id, $frag, $form);

            // Remove a new fragment (not already saved)
            } else {
                $frag.remove();
                $form.remove();
                this.setFormListElement();
            }

            //Removed the last element ?
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
         * This method moves the selected fragment and form elements in div#deleted-fragments
         *
         * @param {string} id
         * @param {object} fragmentElement
         * @param {object} formElement
         */
        moveSelectedFragmentOutsideOfForm: function (id, fragmentElement, formElement) {
            var divElement = $('<div>')[0];
            divElement.setAttribute('id', id);
            fragmentElement.clone().appendTo(divElement);
            divElement.append(formElement[0]);

            // Generate the elements who contains the fragment title and the cancel button.
            var resetRemovedElement = $('<span>');
            var fragmentTitle = fragmentElement.find('.fragment__title').text();

            var cancelIcon = $('<i>');
            cancelIcon.attr('class', 'fa fa-repeat');
            cancelIcon.on('click', this.cancelFragmentDeletion);

            resetRemovedElement.append(cancelIcon);
            resetRemovedElement.append(' <i>' +  fragmentTitle + '</i>');

            // Insert the fragment's title and the reset button in the place of removed fragment.
            fragmentElement.empty();
            fragmentElement.append(resetRemovedElement);

            // Insert the selected fragment and form into #deleted-fragments.
            $('#deleted-fragments').append(divElement);
        },

        /**
         * This method cancels the fragment deletion.
         *
         * @param e
         */
        cancelFragmentDeletion: function (e) {
            e.preventDefault();
            e.stopPropagation();

            var $target = $(e.currentTarget),
                $frag = $target.closest('[data-fragment]'),
                id = $frag.data('fragId');

            // Get the deleted fragment by id.
            var canceledFragment = $('div#deleted-fragments');

            // Get the fragment list.
            var fragment = $('ul.fragmentList').find('[data-frag-id="' + id + '"]');

            // Get forms.
            var forms = $("[id~=field_widget_" + id.slice(0, -2) + "]");

            // Reset clear and append the fragments list element.
            fragment.empty();
            fragment.append(canceledFragment.find('[data-frag-id="' + id + '"]').children());

            // Move the deleted form to the original place.
            forms.append(canceledFragment.find('[data-fragment-form="' + id + '"]'));
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

    $(document).ready(function() {

        // A really lightweight plugin wrapper around the constructor,
        // preventing against multiple instantiations
        $.fn[pluginName] = function ( options ) {
            return this.each(function () {
                if (!$.data(this, 'plugin_' + pluginName)) {
                    $.data(this, 'plugin_' + pluginName, new Plugin(this, options));
                }
            });
        };

    });
})(jQuery);
