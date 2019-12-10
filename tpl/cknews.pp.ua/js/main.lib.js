function dynamic_width()
{
    var W = $(window).width();
    var P = 980;
    var MIN_W = 300;

    if( W < P )
    {
        if( W < MIN_W ){ W = MIN_W; }
        $('#page').css('width', W+'px' );

        if( !$('#page').hasClass( 'small' ) )
        {
            $('#page').addClass( 'small' );
        }

    }
    else
    {
        $('#page').css('width', P+'px' );
        $('#page').removeClass( 'small' ); 
    }

    $('#page .article.full .text img').css( 'max-width', $('#page .article.full .text').width()+'px' );
}

$(document).ready( function()
{
        $("a").attr('data-ajax', false);

        $('[data-role="dialog:window"]').dialog(
        {
            'modal' : true,
            'autoOpen' : false,
            'draggable' : true,
            'resizable' : false,
            'minWidth' : 100,
            'minHeight' : 100,
            'width' : 400,
            'position' : { my: "top", at: "top+15%", of: window },
            'dialogClass' : 'dialog-simple',
            'closeOnEscape' : true
        });
        $('[data-role="dialog:window"][data-dopts="1"]').each(function()
        {
            var params = {};
            if( $(this).attr( 'data-width' ) ){ params['width'] = $(this).attr( 'data-width' ); }
            $(this).dialog( params );
        });

        $('[data-role="dialog:close"]').on( "click", function(){ $(this).parents( '.ui-dialog[aria-describedby] .ui-dialog-content' ).dialog('close'); return false; } );

        $('[data-role="dialog:open"]').on( "click", function()
        {
            var id  = $(this).attr('data-dialog');
            var dlg = $('#'+id);

                dlg.dialog( "open" );
                return false;
        } );

        dynamic_width();

        $(window).resize(function()
        {
            dynamic_width();
        });

        $( '#page' ).on( "swiperight", function()
        {
            if( !$(this).hasClass('show_left') )
            {
                $(this).addClass('show_left');
            }
        } );

        $( '#page' ).on( "swipeleft", function()
        {
            $(this).removeClass('show_left');
        } );


});