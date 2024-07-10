const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const el = wp.element.createElement;

let getOxygenbergData = function( blockContent ) {

    let s = decodeURIComponent( escape( window.atob( blockContent ) ) );
    let regex = /<oxygenberg(.|\n)*?>/g;
    let m;
    let oxygenbergTags = [];

    while( ( m = regex.exec( s ) ) !== null ) {

        // This is necessary to avoid infinite loops with zero-width matches
        if ( m.index === regex.lastIndex ) regex.lastIndex++;
        oxygenbergTags.push(m[0]);
    }

    let attributes = {};
    for( let i =0; i<oxygenbergTags.length; i++ ){

        let attRegex = /(\S+)=["']([^\s]*?)["']/g;
        let match;
        let att = {};
        let attId = '';

        while( ( match = attRegex.exec( oxygenbergTags[ i ] ) ) !== null ) {

            att[ match[1] ] = decodeURIComponent( escape( window.atob( match[2] ) ) );
            if( match[1] == "id" ) attId = att[ 'id' ];
        }

        att["full_tag"] = oxygenbergTags[ i ];
        attributes[attId] = att;
    }

    return attributes;
};

function get_attribute_alt_key(attribute, type) {
    var alt_key = attribute.substring(0, attribute.length - type.length) + 'alt';
    return alt_key;
}

window.oxygenbergEventHandlers = {};

function registerOxygenBlock( blockContent, blockName, blockTitle, isFullPageBlock ){

    let attributes = getOxygenbergData( blockContent );
    attributes['align'] = {type: 'string', default: 'full'};

    registerBlockType( 'oxygen-vsb/' + blockName , {

        title: blockTitle,
        icon: 'paperclip',
        category: isFullPageBlock ? 'oxygen-vsb-full-page-blocks' : 'oxygen-vsb-blocks',
        supports: {
            align: ['full']
        },
        attributes: attributes,

        edit(props) {

            // Force full width alignment
            if( 'full' != props.attributes.align ){ props.setAttributes({ align: 'full' }); }
            // Hide the align-full toolbar button (doesn't work good)
            //if( jQuery('svg.dashicons-align-full-width').parent().hasClass('components-toolbar__control')) jQuery('svg.dashicons-align-full-width').parent().css('display', 'none');

            if( typeof window.oxygenMouseTip === 'undefined' ) window.oxygenMouseTip = new MouseTip();

            let blockHtml = decodeURIComponent( escape( window.atob( blockContent ) ) );

            let inspectorControls = [];

            if(window.oxygen_vsb_current_user_can_access) {
                if( !( window.ctBuilderFullPageBlock && window.ctBuilderFullPageBlockName == blockName.substring(5) ) ) inspectorControls.push(el(wp.element.RawHTML, {},
                    '<a class="components-button is-primery is-button" href="' + oxyBuilderUrl + '&oxy_user_library=' + blockName.substring(5) + '" target="_blank" class="oxy-edit-in-oxygen" data-block="' + blockName.substring(5) + '">Edit ' + blockTitle + ' in Oxygen</a><hr>'
                ));
            }

            for (let key in attributes) {
                if(key == 'align') continue;

                var focusFn = function(){
                    var targetElement = jQuery("#"+key.substring(0, key.length-attributes[key]['type'].length-1)+".oxygenberg-"+props.clientId);
                    targetElement.stop().finish();
                    targetElement.animate({"opacity": 0.2},400).animate({"opacity": 1},400);

                };
                var mouseOverFn = function(){
                    var targetElement = jQuery("#"+key.substring(0, key.length-attributes[key]['type'].length-1)+".oxygenberg-"+props.clientId);
                    targetElement.css({
                        "outline": "4px solid #21759b"
                    });
                }
                var mouseOutFn = function(){
                    var targetElement = jQuery("#"+key.substring(0, key.length-attributes[key]['type'].length-1)+".oxygenberg-"+props.clientId);
                    targetElement.css({
                        "outline": "inherit"
                    });
                }

                // The purpose of oxygenbergEventHandlers is to keep track of the events to avoid setting them more
                // than one time even though the edit function is run a thousand times in the block lifetime
                if( typeof oxygenbergEventHandlers[ props.clientId ] === 'undefined' ) oxygenbergEventHandlers[ props.clientId ] = {};

                // Events should be attached only the first time the block is rendered
                if( typeof oxygenbergEventHandlers[ props.clientId ][ key ] === 'undefined' ) {
                    switch( attributes[key]['type'] ) {

                        case 'link':
                            var linkText = props.attributes[key.substring(0, key.length-4)+'string'];
                            if( typeof linkText === 'undefined') {
                                linkText = "Link Wrapper";
                            } else {
                                linkText = '"' + linkText + '"';
                            }
                            inspectorControls.push(
                                el(wp.components.TextControl, {label:'Url for ' + linkText + ':',value: props.attributes[key], onChange:function(newValue){
                                    var newAttr = {};
                                    newAttr[key] = newValue;
                                    props.setAttributes(newAttr);
                                    // jQuery('#block-' + props.clientId + ' svg.dashicons-align-full-width').closest('button').trigger('click');
                                    window.oxyBlocksEdited=true;
                                },
                                className: 'sidebar-icon-box',
                                onFocus: focusFn,
                                onClick: focusFn,
                                onMouseOver: mouseOverFn,
                                onMouseOut: mouseOutFn,
                                readOnly: true
                                })
                            );
                            inspectorControls.push(
                                el(
                                    wp.components.Button,
                                    {
                                        isSecondary: true,
                                        className: 'sidebar-icon-button',
                                        onFocus: focusFn,
                                        onMouseOver: mouseOverFn,
                                        onMouseOut: mouseOutFn,
                                        onClick: function(event){
                                            wpActiveEditor=true;
                                            jQuery('<textarea>')
                                                .attr('id', 'ct-link-dialog-txt')
                                                .css('display', 'none')
                                                .appendTo('body');

                                            wpLink.open('ct-link-dialog-txt');
                                            jQuery('#wp-link-url').val(props.attributes[key]);
                                            jQuery('#wp-link .link-target').hide();
                                            jQuery('#wp-link .wp-link-text-field').hide();
                                            var submitButton = document.getElementById('wp-link-submit');
                                            submitButton.addEventListener('click', function linkSelected(){
                                                submitButton.removeEventListener('click', linkSelected);
                                                var attrs = wpLink.getAttrs();
                                                var newAttr = {};
                                                newAttr[key] = attrs.href;
                                                props.setAttributes(newAttr);
                                                // jQuery('#block-' + props.clientId + ' svg.dashicons-align-full-width').closest('button').trigger('click');
                                                wpLink.close();
                                            });
                                        },
                                    },
                                    el(wp.element.RawHTML, {}, "Change URL...")
                                )
                            );
                            // No function is added to oxygengergEventHandlers here because inspectorControls must be created each time the block is rendered
                            break;

                        case 'icon':
                            inspectorControls.push(
                                el(wp.components.TextControl, {label:'Icon:',value: props.attributes[key], onChange:function(newValue){
                                    var newAttr = {};
                                    newAttr[key] = newValue;
                                    props.setAttributes(newAttr);
                                    // jQuery('#block-' + props.clientId + ' svg.dashicons-align-full-width').closest('button').trigger('click');
                                },
                                className: 'sidebar-icon-box',
                                onFocus: focusFn,
                                onClick: focusFn,
                                onMouseOver: mouseOverFn,
                                onMouseOut: mouseOutFn,
                                readOnly: true
                                })
                            );
                            inspectorControls.push(
                                el(
                                    wp.components.Button,
                                    {
                                        isSecondary: true,
                                        className: 'sidebar-icon-button',
                                        onFocus: focusFn,
                                        onMouseOver: mouseOverFn,
                                        onMouseOut: mouseOutFn,
                                        onClick: function(event){
                                            showSvgBox(props.attributes[key]);
                                            var cancelButton = document.getElementById("svgBoxCancelBtn");
                                            var saveButton = document.getElementById("svgBoxSaveBtn");
                                            cancelButton.addEventListener("click", function cancelFunction(){
                                                cancelButton.removeEventListener("click", cancelFunction);
                                                hideSvgBox();
                                            });
                                            saveButton.addEventListener("click", function saveFunction(){
                                                saveButton.removeEventListener("click", saveFunction);
                                                hideSvgBox();
                                                var newAttr = {};
                                                newAttr[key] = jQuery(saveButton).data("icon");
                                                props.setAttributes(newAttr);
                                                // jQuery('#block-' + props.clientId + ' svg.dashicons-align-full-width').closest('button').trigger('click');
                                                window.oxyBlocksEdited=true;
                                            });
                                        },
                                    },
                                    el(wp.element.RawHTML, {}, "Browse Icons...")
                                )
                            );
                            // No function is added to oxygengergEventHandlers here because inspectorControls must be created each time the block is rendered
                            break;

                        case 'string':
                            // Create the event handler specific for this attribute
                            oxygenbergEventHandlers[ props.clientId ][ key ] = function( event ) {
                                props.attributes[key] = jQuery( this ).html();
                                window.oxyBlocksEdited=true;
                                // Calling setAtributes makes the CONTENTEDITABLE element lose focus
                                //props.setAttributes(newAttr);
                            };
                            // Attach the event handler to the body element because the first time this function is called there is no blocks markup yet
                            jQuery( 'body' ).on( 'input', '#' + key.substring(0, key.length-7) + '.oxygenberg-' + props.clientId, oxygenbergEventHandlers[ props.clientId ][ key ]);
                            // Additional event because gutenberg requires calling setAttributes()
                            jQuery( 'body' ).on( 'focusout', '#' + key.substring(0, key.length-7) + '.oxygenberg-' + props.clientId, function(){
                                var newAttr = {};
                                newAttr[key] = jQuery( this ).html();
                                props.setAttributes(newAttr);
                                // jQuery('#block-' + props.clientId + ' svg.dashicons-align-full-width').closest('button').trigger('click');
                                // hack to reflect the changes immetiately on the sidebar
                                // wp.data.dispatch( 'core/block-editor' ).toggleBlockMode(props.clientId);
                                // wp.data.dispatch( 'core/block-editor' ).toggleBlockMode(props.clientId);
                                wp.data.dispatch( 'core/block-editor' ).updateBlock(props.clientId, newAttr);
                                window.oxyBlocksEdited=true;
                            });
                            break;

                        case 'richtext':
                            oxygenbergEventHandlers[ props.clientId ][ key ] = function( event ) {
                                jQuery('body').append('<div id="oxygenVsbRichEditorBackdrop"><textarea id="oxygenVsbRichEditor"></textarea><div class="buttons_block"><div id="oxygenVsbRichEditor_save" class="button">Ok</div><div id="oxygenVsbRichEditor_cancel" class="button">Cancel</div></div></div>');
                                var originalTextEl = jQuery( event.target ).closest('.oxy-rich-text');
                                tinyMCE.init({
                                    target: document.getElementById('oxygenVsbRichEditor'),
                                    width : "50%",
                                    height: "300px",
                                    menubar: false,
                                    plugins: 'lists link',
                                    toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | link',
                                    'media_buttons': false
                                }).then(function(editors) {
                                    tinyMCE.get("oxygenVsbRichEditor").setContent( originalTextEl.html() );
                                });
                                jQuery('#oxygenVsbRichEditor_save').click(function(){
                                    var newContent = tinyMCE.get("oxygenVsbRichEditor").getContent();
                                    //props.attributes[jQuery( originalTextEl ).attr('id')+'_'+attributes[key]['type']] = newContent;
                                    //props.setAttributes(props.attributes);
                                    var newAttr = {};
                                    newAttr[key] = newContent;
                                    props.setAttributes(newAttr);
                                    // jQuery('#block-' + props.clientId + ' svg.dashicons-align-full-width').closest('button').trigger('click');
                                    tinymce.remove('#oxygenVsbRichEditor');
                                    jQuery('#oxygenVsbRichEditorBackdrop').remove();
                                    originalTextEl.html(newContent);
                                    window.oxyBlocksEdited=true;
                                });
                                jQuery('#oxygenVsbRichEditor_cancel').click(function(){
                                    tinymce.remove('#oxygenVsbRichEditor');
                                    jQuery('#oxygenVsbRichEditorBackdrop').remove();
                                });
                            };
                            jQuery( 'body' ).on( 'click', '#' + key.substring(0, key.length-9) + '.oxygenberg-' + props.clientId, oxygenbergEventHandlers[ props.clientId ][ key ]);
                            break;

                        case 'image':
                            oxygenbergEventHandlers[ props.clientId ][ key ] = function( event ) {
                                event.preventDefault();
                                event.stopPropagation();
                                var imageElement = this;
                                var file_frame = wp.media.frames.file_frame = wp.media({
                                    title: 'Select a replacement image',
                                    button: {
                                        text: 'Use this image',
                                    },
                                    multiple: false
                                });
                                file_frame.on( 'select', function() {
                                    var attachment = file_frame.state().get('selection').first().toJSON();
                                    
                                    var newAttr = {};
                                    newAttr[key] = attachment.url;
                                    
                                    jQuery( imageElement ).attr('src', attachment.url);
                                    jQuery( imageElement ).removeAttr('srcset');
                                    
                                    if (attachment.alt) {
                                        var alt_key = get_attribute_alt_key(key, 'image');
                                        if (alt_key && attributes.hasOwnProperty(alt_key)) {
                                            newAttr[alt_key] = attachment.alt;
                                        }
                                    }
                                    
                                    props.setAttributes(newAttr);
                                    
                                    window.oxyBlocksEdited=true;
                                });
                                file_frame.open();
                            };
                            jQuery( 'body' ).on( 'click', '#' + key.substring(0, key.length-6) + '.oxygenberg-' + props.clientId, oxygenbergEventHandlers[ props.clientId ][ key ]);
                            break;
                        case 'background':
                            inspectorControls.push(
                                el(wp.components.TextControl, {label:'Background Image URL:',value: props.attributes[key], onChange:function(newValue){
                                        var newAttr = {};
                                        newAttr[key] = newValue;
                                        props.setAttributes(newAttr);
                                        // jQuery('#block-' + props.clientId + ' svg.dashicons-align-full-width').closest('button').trigger('click');
                                    },
                                    className: 'sidebar-icon-box',
                                    onFocus: focusFn,
                                    onClick: focusFn,
                                    onMouseOver: mouseOverFn,
                                    onMouseOut: mouseOutFn,
                                    readOnly: true
                                })
                            );
                            inspectorControls.push(
                                el(
                                    wp.components.Button,
                                    {
                                        isSecondary: true,
                                        className: 'sidebar-icon-button',
                                        onFocus: focusFn,
                                        onMouseOver: mouseOverFn,
                                        onMouseOut: mouseOutFn,
                                        onClick: function(event){
                                            //var imageElement = this;
                                            var file_frame = wp.media.frames.file_frame = wp.media({
                                                title: 'Select a background image',
                                                button: {
                                                    text: 'Use this image',
                                                },
                                                multiple: false
                                            });
                                            file_frame.on( 'select', function() {
                                                var attachment = file_frame.state().get('selection').first().toJSON();
                                                var newAttr = {};
                                                newAttr[key] = 'url('+attachment.url+')';
                                                props.setAttributes(newAttr);
                                                // jQuery('#block-' + props.clientId + ' svg.dashicons-align-full-width').closest('button').trigger('click');
                                                window.oxyBlocksEdited=true;
                                            });
                                            file_frame.open();

                                        },
                                    },
                                    el(wp.element.RawHTML, {}, "Set Background Image...")
                                )
                            );
                            inspectorControls.push(
                                el(
                                    wp.components.Button,
                                    {
                                        isSecondary: true,
                                        className: 'sidebar-icon-button',
                                        onFocus: focusFn,
                                        onMouseOver: mouseOverFn,
                                        onMouseOut: mouseOutFn,
                                        onClick: function(event){
                                            var newAttr = {};
                                            newAttr[key] = attributes[key]['default'];//'auto';
                                            props.setAttributes(newAttr);
                                            // jQuery('#block-' + props.clientId + ' svg.dashicons-align-full-width').closest('button').trigger('click');
                                            window.oxyBlocksEdited=true;
                                        },
                                    },
                                    el(wp.element.RawHTML, {}, "Clear")
                                )
                            );
                            break;
    
                        case 'alt':
                            inspectorControls.push(
                                el(wp.components.TextControl, {
                                    label:'Alt Text',
                                    value: props.attributes[key],
                                    onChange: function(newValue){
                                        var newAttr = {};
                                        newAttr[key] = newValue;
                                        props.setAttributes(newAttr);
                                    },
                                    className: 'sidebar-icon-box',
                                    onFocus: focusFn,
                                    onClick: focusFn,
                                    onMouseOver: mouseOverFn,
                                    onMouseOut: mouseOutFn,
                                    // readOnly: true
                                })
                            );
                            break;

                    }
                }

                // Initializations that should be run each time the block is rendered
                setTimeout(function(){
                    window.oxygenMouseTip.stop();
                    switch( attributes[key]['type'] ) {
                        case 'link':
                            // buttons or links should not be followed when clicked
                            jQuery( '#' + key.substring(0,key.length-5) + '.oxygenberg-' + props.clientId ).click( function( event ){
                                event.preventDefault();
                                event.stopPropagation();
                            });
                            break;
                        case 'string':
                            
                            if( jQuery( '#' + key.substring(0,key.length-7) + '.oxygenberg-' + props.clientId ).html().includes('<oxygenberg') ) break;

                            jQuery( '#' + key.substring(0,key.length-7) + '.oxygenberg-' + props.clientId ).attr('contenteditable','true');
                            jQuery( '#' + key.substring(0,key.length-7) + '.oxygenberg-' + props.clientId ).css('cursor','text');
                            break;
                        case 'richtext':
                            jQuery( '#' + key.substring(0,key.length-9) + '.oxygenberg-' + props.clientId ).attr('mousetip', '');
                            jQuery( '#' + key.substring(0,key.length-9) + '.oxygenberg-' + props.clientId ).attr('mousetip-msg', 'Click to edit text');
                            jQuery( '#' + key.substring(0,key.length-9) + '.oxygenberg-' + props.clientId ).css('cursor','pointer');
                            break;
                        case 'image':

                            jQuery( '#' + key.substring(0,key.length-6) + '.oxygenberg-' + props.clientId ).removeAttr('srcset');
                            jQuery( '#' + key.substring(0,key.length-6) + '.oxygenberg-' + props.clientId ).attr('mousetip', '');
                            jQuery( '#' + key.substring(0,key.length-6) + '.oxygenberg-' + props.clientId ).attr('mousetip-msg', 'Click to change the image');
                            jQuery( '#' + key.substring(0,key.length-6) + '.oxygenberg-' + props.clientId ).css('cursor','pointer');
                            break;

                    }
                    window.oxygenMouseTip.start();
                }, 50);

                // Identify an editable component with it's container block. Because repeated blocks of the same type will have repeated elements ID's
                blockHtml = blockHtml.replace( 'oxygenberg-' + (key.substring(0, key.length-attributes[key]['type'].length-1) ), 'oxygenberg-' + props.clientId );
                // Replace the full oxygenberg meta tag with the custom content
                blockHtml = blockHtml.replace( attributes[key]["full_tag"], props.attributes[key] );
            }

            setTimeout( function(){
                let event = new Event('oxygenVSBInitJs');
                document.dispatchEvent(event);
                jQuery('.ct-slider-script').each(function(index, element){
                    // Initialize unslider only if it was not initialized since the last time DOM was rebuilt for the current block
                    eval(element.innerHTML);
                });
            }, 100 );

            return [
                el(
                    wp.blockEditor.BlockControls,
                    { key: 'controls' },
                    el(
                        wp.components.ToolbarGroup,
                        {

                        },
                        el(
                            wp.components.ToolbarButton,
                            {
                                icon: 'trash',
                                label: 'Delete Block',
                                onClick: function(event){
                                    wp.data.dispatch( 'core/block-editor' ).removeBlock(props.clientId,false);
                                }
                            }
                        )
                    )
                ),
                el( wp.blockEditor.InspectorControls, {},
                    el( wp.components.PanelBody,{ title: 'Block Settings', initialOpen: true},
                        inspectorControls
                    )
                ),
                el( wp.element.RawHTML, { },
                    blockHtml
                )
            ];
        },

        save({attributes, className}) {
            // Maybe save the markup so it's rendered even if Oxygen is not installed? Or let the user know that he needs Oxygen?
            return null;
        },

    } );

}


document.addEventListener("DOMContentLoaded", function() {
    //jQuery(document).on("click", ".oxy-edit-in-oxygen", function () {
    //    jQuery("body").append('<iframe id="oxy-edit-block-iframe" src="' + oxyBuilderUrl + '&oxy_user_library=' + jQuery(this).data("block") + '" style="position:absolute; z-index: 100000; top:0; left:0; right:0; bottom:0;width:100%;height:100%;background-color:white;">');
    //});

    var params = {
        action: 'ct_get_svg_icon_sets',
        post_id : ctBuilderPost,
        nonce : ctBuilderNonce,
    };
    initSvgBox(jQuery);
    jQuery.post( ctBuilderAjaxUrl, params)
        .done(function( data ) {
            $svgBoxList = jQuery("#svgBoxList");
            var index = 0;
            for (var svgFamily in data) {
                if (data.hasOwnProperty(svgFamily)) {
                    for( icon in data[svgFamily].defs.symbol ){

                        if(!Array.isArray(data[svgFamily].defs.symbol[icon].path)) data[svgFamily].defs.symbol[icon].path = [data[svgFamily].defs.symbol[icon].path];
                        var path = '';
                        for( var i = 0; i< data[svgFamily].defs.symbol[icon].path.length; i++){
                            path += '<path d="' + data[svgFamily].defs.symbol[icon].path[i]["@attributes"].d + '"/>'
                        }
                        var newIcon = {
                            "family": svgFamily,
                            "title":data[svgFamily].defs.symbol[icon].title,
                            "id": svgFamily.replace(" ", "") + data[svgFamily].defs.symbol[icon]['@attributes'].id,
                            "path": path
                        };
                        var option = jQuery('<svg class="svgBoxListItem" data-icon-title="'+newIcon.title+'" data-icon-id="'+newIcon.id+'"></svg>').html(newIcon.path);
                        $svgBoxList.append(option);
                    }
                }
            }

        });

    jQuery("#svgBoxList").on("click", ".svgBoxListItem", function(){
        jQuery('.svgBoxListItem[selected="selected"]').removeAttr("selected");
        jQuery(this).attr("selected","selected");

        jQuery("#svgBoxName").html(jQuery(this).data('icon-id'));
        jQuery("#svgBoxSaveBtn").data("icon", jQuery(this).data('icon-id'));
    });

    window.svgboxinputTimeout = null;
    jQuery("#svgboxinput").keyup(function(){
        clearTimeout(window.svgboxinputTimeout);
        var inputText = jQuery(this).val().toLowerCase().trim();
        window.svgboxinputTimeout = setTimeout(function(){
            jQuery('.svgBoxListItem').each(function(){
                var haystack = jQuery(this).data('icon-title').toLowerCase();
                if(haystack.indexOf( inputText ) != -1){
                    jQuery(this).removeClass("filteredout");
                }else{
                    jQuery(this).addClass("filteredout");
                }
            });
        },150);
    });

    jQuery("body").on("click", ".editor-post-publish-button", function(){
        window.oxyBlocksEdited = false;

        var $publish_button = jQuery(this);
        if( $publish_button.attr('aria-disabled') == 'true' ) return;
        
        var $checkbox_input = jQuery('#ct_oxygenberg_full_page_block');
        if( !$checkbox_input.length || $checkbox_input.is(':checked') == window.ctBuilderFullPageBlock ) return;

        var intervalToken = setInterval(function(){
            if ( $publish_button.attr('aria-disabled') != 'true' ) {
                clearInterval(intervalToken)
                window.location.href = window.location.href+'&refreshed=1';                
            }
        }, 300);

    });

    jQuery(window).on('beforeunload', function(){
        if(window.oxyBlocksEdited) return 'Changes on your Oxygen Blocks may not be saved. Are you sure you want to leave?'; //This text is not displayed on modern browsers anyways
    });

    jQuery('body').on('click', '#wp-link-cancel, #wp-link-close, #wp-link-backdrop', function(e) {
        jQuery('body #ct-link-dialog-txt').remove();
        wpLink.close();
    });

    var fullPageBlockInputChanged = false,
        fullPageBlockInputOriginal = jQuery("#ct_oxygenberg_full_page_block").is(":checked");

    // strip text formatting when paste from other sources
    jQuery('body').on('paste', '.oxygenberg-element[contenteditable=true]', function(e) {
        e.preventDefault();
        // get text representation of clipboard
        var text = (e.originalEvent || e).clipboardData.getData('text');
        // insert text manually
        document.execCommand("insertHTML", false, text);
    })

    // track if checkbox checked state is different from saved in the DB
    .on('change', '#ct_oxygenberg_full_page_block', function(){
        fullPageBlockInputChanged = !fullPageBlockInputChanged

        if ( fullPageBlockInputOriginal && fullPageBlockInputChanged ) {
            alert("Any content in the Gutenberg design on this page will be lost after you save.")
        }
    })

    .on('click', '.editor-post-publish-button', function(){
        if ( fullPageBlockInputChanged ) {
            // hacky way to wait until page is saved before force reload
            setTimeout(() => {
                const oxyGutenbergReloadTimer = setInterval(() => {
                    if (jQuery(this).attr('aria-disabled')=="false") {
                        window.location.reload()
                        clearInterval(oxyGutenbergReloadTimer);
                    }
                }, 100);

            }, 100);
        }
    })
});

//window.addEventListener('message',function(message){
//    if(message.data.type=="blockSaved"){
//        jQuery("#oxy-edit-block-iframe").remove();
//    }
//});

function initSvgBox($) {

    var svgbox = ''+
        '<div class="svgBoxBackground">'+
        '  <div id="svgBox">\n' +
        '    <label>Browse icons</label>\n' +
        '    <input id="svgboxinput" type="text">\n' +
        '    <div id="svgBoxList">\n' +
        '    </div>'+
        '    <span id="svgBoxName"></span>'+
        '    <div id="svgBoxButtons">'+
        '      <div id="svgBoxSaveBtn" class="svgBoxButton">Save'+
        '      </div>'+
        '      <div id="svgBoxCancelBtn" class="svgBoxButton">Cancel'+
        '      </div>'+
        '    </div>'+
        '  </div>'+
        '</div>';
    $("body").append(svgbox);
}

showSvgBox = function(icon){
    var icon = jQuery('.svgBoxListItem[data-icon-id="'+icon+'"]');
    jQuery("#svgboxinput").val(icon.data('icon-title')).keyup();
    icon.click();
    jQuery(".svgBoxBackground").css("display", "flex");
};
hideSvgBox = function(){
    jQuery(".svgBoxBackground").css("display", "none");
};