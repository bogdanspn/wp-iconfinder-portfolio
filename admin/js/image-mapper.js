;(function( $ ) {
    'use strict';

    var image_hider = {

        $button: $('.hide-in-use'),

        ss_kay: 'mapper.hide-in-use',

        is_hidden: function() {
            return parseInt(sessionStorage.getItem(this.ss_key)) == 1;
        },

        show_used_images: function() {
            $('.in-use').show();
            image_hider.$button.text('Hide Used Images');
            sessionStorage.setItem( image_hider.ss_key, 0 );
        },

        hide_used_images: function() {
            $('.in-use').hide();
            image_hider.$button.text('Show Hidden');
            sessionStorage.setItem( image_hider.ss_key, 1 );
        },

        init: function() {

            image_hider.$button = $('.hide-in-use');

            if ( image_hider.is_hidden() ) {
                image_hider.hide_used_images();
            }
            else {
                image_hider.show_used_images();
            }

            image_hider.$button.on( 'click', function(e) {
                e.preventDefault();
                if ( image_hider.is_hidden() ) {
                    image_hider.show_used_images();
                }
                else {
                    image_hider.hide_used_images();
                }
            });
        }
    };

    image_hider.init();

    function clear_dialog( event, ui ) {

        $('#image-mapper li').removeClass('selected');
    };

    var media_uploader = null;

    /**
     * @see http://qnimate.com/post-series/using-wordpress-media-uploader-in-plugins-and-themes/
     * @see
     */
    function open_media_uploader_gallery(state) {

        if (typeof(state) == 'undefined') {
            state = 'insert';
        }

        media_uploader = wp.media({
            frame:    'post',
            state:    state,
            multiple: true
        });

        media_uploader.on( 'update', function() {
            var length = media_uploader.state().attributes.library.length;
            var images = media_uploader.state().attributes.library.models;

            for (var i = 0; i < length; i++) {

                console.log( images[i].changed );
                append_user_image( images[i].changed );
            }

            init_user_images();
        });

        media_uploader.on( 'insert', function() {
            var attachment = media_uploader.state().get('selection').first().toJSON();

            $(".suggestions .content").append(
                $('<img/>').attr('src', attachment.url)
            );
        });

        media_uploader.on( 'select', function() {
            var attachment = media_uploader.state().get('selection').first().toJSON();

            $(".suggestions .content").append(
                $('<img/>').attr('src', attachment.url)
            );
        });

        media_uploader.open();
    };

    $(function() {

        var $win    = $(window);
        var margin  = 200;
        var width   = $win.width();
        var height  = $win.height();

        $("#icf-media-uploader").on('click', function(e) {
            open_media_uploader_gallery( 'gallery-edit' );
        });

        var $mapper = $('#image-mapper');

        $mapper.dialog({
            'title'         : 'Image Mapper',
            'dialogClass'   : 'wp-dialog image-mapper-dialog',
            'close'         : clear_dialog,
            'modal'         : true,
            'autoOpen'      : false,
            'closeOnEscape' : true
        });

        $('.image-mapper').on('click', function(e) {

            e.preventDefault();

            var $button    = $(this);
            var properties = $button.data('properties');

            var data = {
                'action': 'get_icon_previews',
                'iconset_post_id': properties.iconset,
                'security': $('#image-mapper-nonce').val()
            };

            var $api_content = $("#iconfinder-api-images .content");

            jQuery.post(ajax_object.ajax_url, data, function(response) {

                $api_content.empty().append(response);

                // init_user_images();
                init_api_images();
            });

            $mapper.dialog('open');

            resize_mapper();
            image_hider.init();
            $('.suggestions .content').empty();

            var $user_images = $('#user-uploaded-images');
            var $user_items  = $('.content ul li', $user_images);

            $user_items.on('click', function(e) {

                var $this = $(this);

                if ($this.hasClass('selected')) {
                    $this.removeClass('selected');
                }
                else {
                    $user_items.removeClass('selected');
                    $this.addClass('selected');
                }
            });

            // init_user_images();
            init_api_images();
        });


        /**
         * Attach behavior to Image Mapper 'Update' button.
         */

        $('a.image-updater').on( 'click', function(e) {

            var $message = $("#image-mapper-message");

            var icon_post_id = -1;
            var attachment_id = -1;

            var $user_images = $('#user-uploaded-images');
            var $api_images  = $('#iconfinder-api-images');

            var $api_selected  = $('.selected img', $api_images);
            var $user_selected = $('.selected img', $user_images);

            var $api_image_data  = $api_selected.data('properties');
            var $user_image_data = $user_selected.data('properties');

            console.log($api_selected.data('properties'));
            console.log($user_selected.data('properties'));

            if ( typeof($api_image_data.post_id) == 'undefined' ) {
                $message.text('No valid Icon Post ID indicated');
                $message.addClass('error');
                return;
            }

            if ( typeof($user_image_data.attachment_id) == 'undefined' ) {
                $message.text('No valid Attachment ID indicated');
                $message.addClass('error');
                return;
            }

            var data = {
                'action': 'set_icon_preview',
                'icon_post_id': $api_image_data.post_id,
                'attachment_id': $user_image_data.attachment_id,
                'security': $('#image-mapper-nonce').val()
            };

            jQuery.post(ajax_object.ajax_url, data, function(response) {


                console.log(response);
                console.log(JSON.parse(response));

                response = JSON.parse(response);

                $message.fadeOut( 'fast', function() {
                    $message.empty().append(response.message);
                    $message.addClass(response.status);
                    $message.css('font-weight', 'bold');
                    $message.fadeIn( 'fast' );
                });

                $('.selected', $api_images).fadeOut();

                var $user_image = $('.selected', $user_images);
                $user_image.removeClass('selected');
                $user_image.css('opacity', '0.25');
                if ( image_hider.is_hidden() ) {
                    $user_image.fadeOut();
                }

            });
        });
    });

    $(window).on( 'resize', resize_mapper );

    /**
     * Appends an image to the User Images container.
     * @param {object} image
     */
    function append_user_image( image ) {

        var $container = $('#user-uploaded-images');
        var $content   = $('.content', $container);
        var $ul        = $('.content ul', $container);
        var $items     = $('.content ul li', $ul);

        if ( $ul.length == 0 ) {
            $content.append('<ul></ul>');
            $('.content ul', $container);
        }

        var $li = $('<li/>');
        var $img = $('<img/>');
        $img.attr( 'src', image.url );
        $img.attr( 'data-properties', JSON.stringify({"attachment_id": image.id}) );
        $li.append($img);
        $ul.append($li);

    };

    function init_user_images() {

        var $user_images = $('#user-uploaded-images');
        var $user_items  = $('.content ul li', $user_images);

        $user_items.on('click', function(e) {

            var $this = $(this);

            if ($this.hasClass('selected')) {
                $this.removeClass('selected');
            }
            else {
                $user_items.removeClass('selected');
                $this.addClass('selected');
            }
        });
    };

    function resembler( $f, $files ) {

        var iter = 0;
        var stop  = $files.length;

        var distance = 100;
        var closest  = $files[iter];

        next( $f.attr('src'), $($files[iter]).attr('src') );

        function next( f1, f2 ) {
            if ( iter >= stop ) return;
            resemble(f1).compareTo(f2).scaleToSameSize().onComplete(function(data) {

                if ( data.misMatchPercentage < distance ) {

                    var $closest = $($files[iter]);

                    distance = data.misMatchPercentage;
                    closest  = $closest;

                    // console.log( 'Mismatch %: ' + data.misMatchPercentage );
                    // console.log( 'Image: ' + $closest.attr('src') );

                    $('#user-uploaded-images .content ul li').removeClass('selected');
                    $closest.closest('li').addClass('selected');

                    $('.suggestions .content').empty().append(
                        $('<img/>').attr( 'src',  $closest.attr('src') ).css('width', '96px')
                    );
                }
                iter++;
                next( f1, $($files[iter]).attr('src') );
            });
        }
    };

    function init_api_images() {
        var $api_images = $('#iconfinder-api-images');
        var $api_items  = $('.content ul li', $api_images);

        $api_items.on('click', function(e) {

            var $this = $(this);

            resembler(
                $('img', $this),
                $('#user-uploaded-images .content ul li.not-in-use img')
            );

            if ($this.hasClass('selected')) {
                $this.removeClass('selected');
            }
            else {
                $api_items.removeClass('selected');
                $this.addClass('selected');
            }
        });
    };

    function resize_mapper() {

        var $win    = $(window);
        var margin  = 200;
        var width   = $win.width();
        var height  = $win.height();

        var $api_content = $("#iconfinder-api-images .content");

        var image_mapper_height = height - margin;
        var image_mapper_width  = width - margin;

        $('.image-mapper-dialog').css({
            'width': image_mapper_width + 'px',
            'height': image_mapper_height + 'px',
            'position': 'fixed',
            'left': ((width - image_mapper_width) / 2) + 'px',
            'top': ((height - image_mapper_height) / 2) + 'px'
        });

        $('#image-mapper .inner').css({

               'height': (image_mapper_height - 200) + 'px'
        });

        $(".image-mapper-column .content").css({

            'height': (image_mapper_height - 380) + 'px'
        });
    };

})( jQuery );

/*
 Image comparison APIs:

 https://huddle.github.io/Resemble.js/
 https://cloud.google.com/vision/
 https://www.ltutech.com/technology/similarity/
 https://algorithmia.com/algorithms/zskurultay/ImageSimilarity
 http://cloudsight.ai/api

 */