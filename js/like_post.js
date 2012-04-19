jQuery(document).ready( function($){
    
    $('a.plus-count').live( 'click', function() {
        var post_id = $(this).attr('data-id');
        var getData = 'pid=' + post_id + '&action=count';
        
        //$.facebox.init({ loadingImage: '' });
        $.facebox.settings.closeImage = likePath + 'css/facebox/closelabel.gif';
        $.facebox.settings.loadingImage = likePath + 'css/facebox/loading.gif';
        
        $.facebox(function(){
            $.ajax({
                url: ajaxurl + 'ajax.php',
                data: getData,
                type: 'GET',
                dataType: 'json',
                cache: true,
                success: function( response ) {
                    //console.log( response );
                    if( response.status === 'success' ) {
                        htmlData = '<h1>' + response.title + '</h1>';
                        htmlData = htmlData + '<div class="content">' + response.data + '</div>';
                    }
                    //$.colorbox({ html: htmlData, width: 300, initialWidth: 250, initialHeight: 100 });
                    //alert(htmlData);
                    $.facebox(htmlData);
                }
            });

        });
        
        return false;
    });
    
    $('a.like-action').live( 'click', function() {
        var post_id = $(this).attr('data-id');
        var type = $(this).attr('data-action');
        var that = $(this);
        
        var postData = 'pid=' + post_id + '&type=' + type + '&action=do_like';
        
        that.after('<span class="loading"></span>');
        
        $.ajax({
            url: ajaxurl + 'ajax.php',
            data: postData,
            type: 'POST',
            dataType: 'json',
            cache: true,
            success: function( response ) {
                //console.log( response );
                if( response.status == 'success' ) {
                    that.prev( '.plus-count').text( response.count );
                    that.attr( 'data-action', response.action ).attr('title', response.title).text( response.text );
                    
                    if( that.hasClass('icon-like') ) {
                        that.removeClass('icon-like').addClass('icon-unlike');
                    } else if( that.hasClass('icon-unlike') ) {
                        that.removeClass('icon-unlike').addClass('icon-like');
                    }
                    
                    that.next('.loading').remove();
                    //console.log( that );
                    //console.log( that.prev().text() );
                }
            }
        });
        
        return false;
    });

});
