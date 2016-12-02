jQuery(document).ready(function() {
    // Add a new element
    var $selector = jQuery('select.fragmentCreator__select');
    var form = $selector.closest('form');

    var id = $selector.data('id');
    var fragmentAddUrl = $selector.data('add-fragment-url');
    var translations = $selector.data('translations');

    /**
     * Get URL value to POST a new fragment
     */
    var getFragmentAddUrl = function(fragCount) {
        var fragCode = $selector.val();

        return fragmentAddUrl+'&type='+fragCode+'&fragCount='+fragCount;
    };

    /**
     * Callback to trigger when a fragment is added
     */
    var addFragmentCallback = function() {
        if(jQuery('input[type="file"]', form).length > 0) {
            jQuery(form).attr('enctype', 'multipart/form-data');
            jQuery(form).attr('encoding', 'multipart/form-data');
        }
        jQuery('#sonata-ba-field-container-'+id).trigger('sonata.add_element');
        jQuery('#field_container_'+id).trigger('sonata.add_element');
    };

    /**
     * Initialize the sortable list
     */
    jQuery('#sortable_list_'+id).fragmentList({
        formSource: '#field_widget_'+id,
        addButtonSource: '#field_actions_'+id,
        getAddUrl: getFragmentAddUrl,
        addedCallback: addFragmentCallback,
        translations: translations
    });
});
