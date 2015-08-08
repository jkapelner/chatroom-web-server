$(function() {     
    $("input.spinner").spinner();

    //destroy all qtip popups when a dialog is opened
    $("*").on( "dialogopen", function( event, ui ) {               
        $('.qtip').each(function(){
            $(this).data('qtip').destroy();
        });
    });

    //destroy all qtip popups when a dialog is closed
    $("*").on( "dialogclose", function( event, ui ) {
        $('.qtip').each(function(){
            $(this).data('qtip').destroy();
        });
    });

    //destroy all qtip popups when a tabbed form changes tabs
    $("*").on( "tabsbeforeactivate", function( event, ui ) {
        $('.qtip').each(function(){
            $(this).data('qtip').destroy();
        });
    });
});

$.validator.setDefaults({ 
    errorClass: 'error',
    validClass: 'valid',
    errorPlacement: function(error, element)
    {
        // Set positioning based on the elements position in the form
        var elem = $(element),
            corners = ['bottom left', 'top center'];

        //check if element is a tinymce textarea
        if (elem.is('textarea')) {
            var id = elem.attr('id');
            
            //if this is a tinymce textarea, select the tinymce container as the element
            if (id && (typeof(tinyMCE) === 'object') && tinyMCE.editors[id]) {
                elem = $(tinyMCE.editors[id].getContainer());
            }
        }
        
        // Check we have a valid error message
        if(!error.is(':empty')) {
            // Apply the tooltip only if it isn't valid
            elem.filter(':not(.valid)').qtip({
                overwrite: false,
                content: {
                    text: error,
                    attr: 'oldtitle'
                },
                position: {
                    my: corners[0],
                    at: corners[1],
                    viewport: $(window)
                },
                show: {
                    event: false,
                    ready: true
                },
                hide: false,
                style: {
                    classes: 'qtip-red' // Make it red... the classic error colour!
                }
            })

            // If we have a tooltip on this element already, just update its content
            .qtip('option', 'content.text', error);
        }

        // If the error is empty, remove the qTip
        else { elem.qtip('destroy'); }
    },
    success: function(error, placement) {
        $('.qtip-active:visible').fadeOut('fast');
    }
});
