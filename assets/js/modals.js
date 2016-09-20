(function($){

    var dbptBackdrop = null,
        dbptModals   = {},
        activeModals    = [],
        activeSticky    = [],
        pageHTML,
        pageBody,
        mainWindow;

    var positionModals = function(){

        if( !activeModals.length && !activeSticky.length ){
            return;
        }
        var modalId  = ( activeModals.length ? activeModals[ ( activeModals.length - 1 ) ] : activeSticky[ ( activeSticky.length - 1 ) ] ),
            windowWidth  = mainWindow.width(),
            windowHeight = mainWindow.height(),
            modalHeight  = dbptModals[ modalId ].config.height,
            modalOuterHeight  = modalHeight,
            modalWidth  = dbptModals[ modalId ].config.width,
            top          = 0,
            flickerBD    = false,
            modalReduced = false;

        if( dbptBackdrop ){ pageHTML.addClass('has-dbpt-modal'); }

        // check modals for %
        if( typeof modalWidth === 'string' ){
            modalWidth = parseInt( modalWidth );
            modalWidth = windowWidth / 100 * parseInt( modalWidth );
        }
        if( typeof modalHeight === 'string' ){
            modalHeight = parseInt( modalHeight );
            modalHeight = windowHeight / 100 * parseInt( modalHeight );
        }
        // top
        top = (windowHeight - modalHeight ) / 2.2;

        if( top < 0 ){
            top = 0;
        }
        if( modalHeight + ( dbptModals[ modalId ].config.padding * 2 ) > windowHeight && dbptBackdrop ){
            modalHeight = windowHeight - ( dbptModals[ modalId ].config.padding * 2 );
            modalOuterHeight = '100%';
            if( dbptBackdrop ){
                dbptBackdrop.css( {
                    paddingTop: dbptModals[ modalId ].config.padding,
                    paddingBottom: dbptModals[ modalId ].config.padding,
                });
            }
            modalReduced = true;
        }
        if( modalWidth + ( dbptModals[ modalId ].config.padding * 2 ) >= windowWidth ){
            modalWidth = '100%';
            if( dbptBackdrop ){
                dbptBackdrop.css( {
                    paddingLeft: dbptModals[ modalId ].config.padding,
                    paddingRight: dbptModals[ modalId ].config.padding,
                });
            }
            modalReduced = true;
        }

        if( true === modalReduced ){
            if( windowWidth <= 700 && windowWidth > 600 ){
                if( dbptBackdrop ){ modalHeight = windowHeight - ( dbptModals[ modalId ].config.padding * 2 ); }
                modalWidth = windowWidth;
                modalOuterHeight = modalHeight - ( dbptModals[ modalId ].config.padding * 2 );
                modalWidth = '100%';
                top = 0;
                if( dbptBackdrop ){ dbptBackdrop.css( { padding : dbptModals[ modalId ].config.padding } ); }
            }else if( windowWidth <= 600 ){
                if( dbptBackdrop ){ modalHeight = windowHeight; }
                modalWidth = windowWidth;
                modalOuterHeight = '100%';
                top = 0;
                if( dbptBackdrop ){ dbptBackdrop.css( { padding : 0 } ); }
            }
        }


        // set backdrop
        if( dbptBackdrop && dbptBackdrop.is(':hidden') ){
            flickerBD = true;
            dbptBackdrop.show();
        }
        // title?
        if( dbptModals[ modalId ].header ){
            if( dbptBackdrop ){ dbptBackdrop.show(); }
            modalHeight -= dbptModals[ modalId ].header.outerHeight();
            dbptModals[ modalId ].closer.css( {
                padding     : ( dbptModals[ modalId ].header.outerHeight() / 2 ) - 3.8
            } );
            dbptModals[ modalId ].title.css({ paddingRight: dbptModals[ modalId ].closer.outerWidth() } );
        }
        // footer?
        if( dbptModals[ modalId ].footer ){
            if( dbptBackdrop ){ dbptBackdrop.show(); }
            modalHeight -= dbptModals[ modalId ].footer.outerHeight();
        }

        if( dbptBackdrop && flickerBD === true ){
            dbptBackdrop.hide();
            flickerBD = false;
        }

        // set final height
        if( modalHeight != modalOuterHeight ){
            dbptModals[ modalId ].body.css( {
                height      : modalHeight
            } );
        }
        dbptModals[ modalId ].modal.css( {
            width       : modalWidth
        } );
        if( dbptModals[ modalId ].config.sticky && dbptModals[ modalId ].config.minimized ){
            var toggle = {},
                minimizedPosition = dbptModals[ modalId ].title.outerHeight() - dbptModals[ modalId ].modal.outerHeight();
            if( dbptModals[ modalId ].config.sticky.indexOf( 'bottom' ) > -1 ){
                toggle['margin-bottom'] = minimizedPosition;
            }else if( dbptModals[ modalId ].config.sticky.indexOf( 'top' ) > -1 ){
                toggle['margin-top'] = minimizedPosition;
            }
            dbptModals[ modalId ].modal.css( toggle );
            if( dbptModals[ modalId ].config.sticky.length >= 3 ){
                pageBody.css( "margin-" + dbptModals[ modalId ].config.sticky[0] , dbptModals[ modalId ].title.outerHeight() );
                if( modalReduced ){
                    dbptModals[ modalId ].modal.css( dbptModals[ modalId ].config.sticky[1] , 0 );
                }else{
                    dbptModals[ modalId ].modal.css( dbptModals[ modalId ].config.sticky[1] , parseFloat( dbptModals[ modalId ].config.sticky[2] ) );
                }
            }
        }
        if( dbptBackdrop ){
            dbptModals[ modalId ].modal.css( {
                marginTop   : top,
                height      : modalOuterHeight
            } );
            setTimeout( function(){
                dbptModals[ modalId ].modal.addClass( 'dbpt-animate' );
            }, 10);

            dbptBackdrop.fadeIn( dbptModals[ modalId ].config.speed );
        }

        return dbptModals;
    }

    var closeModal = function( obj ){
        var modalId = $(obj).data('modal'),
            position = 0,
            toggle = {};

        if( obj && dbptModals[ modalId ].config.sticky ){

            if( dbptModals[ modalId ].config.minimized ){
                dbptModals[ modalId ].config.minimized = false
                position = 0;
            }else{
                dbptModals[ modalId ].config.minimized = true;
                position = dbptModals[ modalId ].title.outerHeight() - dbptModals[ modalId ].modal.outerHeight();
            }
            if( dbptModals[ modalId ].config.sticky.indexOf( 'bottom' ) > -1 ){
                toggle['margin-bottom'] = position;
            }else if( dbptModals[ modalId ].config.sticky.indexOf( 'top' ) > -1 ){
                toggle['margin-top'] = position;
            }
            dbptModals[ modalId ].modal.stop().animate( toggle , dbptModals[ modalId ].config.speed );
            return;
        }
        var lastModal;
        if( activeModals.length ){

            lastModal = activeModals.pop();
            if( dbptModals[ lastModal ].modal.hasClass( 'dbpt-animate' ) && !activeModals.length ){
                dbptModals[ lastModal ].modal.removeClass( 'dbpt-animate' );
                setTimeout( function(){
                    dbptModals[ lastModal ].modal.remove();
                    delete dbptModals[ lastModal ];
                }, 500 );
            }else{
                if( dbptBackdrop ){
                    dbptModals[ lastModal ].modal.hide( 0 , function(){
                        $( this ).remove();
                        delete dbptModals[ lastModal ];
                    });
                }
            }

        }

        if( !activeModals.length ){
            if( dbptBackdrop ){
                dbptBackdrop.fadeOut( 250 , function(){
                    $( this ).remove();
                    dbptBackdrop = null;
                });
            }
            pageHTML.removeClass('has-dbpt-modal');
        }else{
            dbptModals[ activeModals[ ( activeModals.length - 1 ) ] ].modal.show();
        }

    }
    $.dbptModal = function(opts,trigger){
        var defaults    = $.extend(true, {
            element             :   'div',
            height              :   550,
            width               :   620,
            padding             :   12,
            speed               :   250,
            content             :   ''
        }, opts );
        defaults.trigger = trigger;
        if( !dbptBackdrop && ! defaults.sticky ){
            dbptBackdrop = $('<div>', {"class" : "dbpt-backdrop"});
            if( ! defaults.focus ){
                dbptBackdrop.on('click', function( e ){
                    if( e.target == this ){
                        closeModal();
                    }
                });
            }
            pageBody.append( dbptBackdrop );
            dbptBackdrop.hide();
        }



        // create modal element
        var modalElement = defaults.element,
            modalId = defaults.modal;

        if( activeModals.length ){

            if( activeModals[ ( activeModals.length - 1 ) ] !== modalId ){
                dbptModals[ activeModals[ ( activeModals.length - 1 ) ] ].modal.hide();
            }
        }

        if( typeof dbptModals[ modalId ] === 'undefined' ){
            if( defaults.sticky ){
                defaults.sticky = defaults.sticky.split(' ');
                if( defaults.sticky.length < 2 ){
                    defaults.sticky = null;
                }
                activeSticky.push( modalId );
            }
            dbptModals[ modalId ] = {
                config  :   defaults,
                modal   :   $('<' + modalElement + '>', {
                    id                  : modalId + '_dbptModal',
                    tabIndex            : -1,
                    "ariaLabelled-by"   : modalId + '_dbptModalLable',
                    "class"             : "dbpt-modal-wrap" + ( defaults.sticky ? ' dbpt-sticky-modal ' + defaults.sticky[0] + '-' + defaults.sticky[1] : '' )
                })
            };
            if( !defaults.sticky ){ activeModals.push( modalId ); }
        }else{
            dbptModals[ modalId ].config = defaults;
            dbptModals[ modalId ].modal.empty();
        }
        // add animate
        if( defaults.animate && dbptBackdrop ){
            var animate         = defaults.animate.split( ' ' ),
                animateSpeed    = defaults.speed + 'ms',
                animateEase     = ( defaults.animateEase ? defaults.animateEase : 'ease' );

            if( animate.length === 1){
                animate[1] = 0;
            }

            dbptModals[ modalId ].modal.css( {
                transform               : 'translate(' + animate[0] + ', ' + animate[1] + ')',
                '-web-kit-transition'   : 'transform ' + animateSpeed + ' ' + animateEase,
                '-moz-transition'       : 'transform ' + animateSpeed + ' ' + animateEase,
                transition              : 'transform ' + animateSpeed + ' ' + animateEase
            } );

        }
        dbptModals[ modalId ].body = $('<div>', {"class" : "dbpt-modal-body",id: modalId + '_dbptModalBody'});
        dbptModals[ modalId ].content = $('<div>', {"class" : "dbpt-modal-content",id: modalId + '_dbptModalContent'});


        // padd content
        dbptModals[ modalId ].content.css( {
            margin : defaults.padding
        } );
        dbptModals[ modalId ].body.append( dbptModals[ modalId ].content ).appendTo( dbptModals[ modalId ].modal );
        if( dbptBackdrop ){ dbptBackdrop.append( dbptModals[ modalId ].modal ); }else{
            dbptModals[ modalId ].modal . appendTo( $( 'body' ) );
        }


        if( defaults.footer ){
            dbptModals[ modalId ].footer = $('<div>', {"class" : "dbpt-modal-footer",id: modalId + '_dbptModalFooter'});
            dbptModals[ modalId ].footer.css({ padding: defaults.padding });
            dbptModals[ modalId ].footer.appendTo( dbptModals[ modalId ].modal );
            // function?
            if( typeof window[defaults.footer] === 'function' ){
                dbptModals[ modalId ].footer.append( window[defaults.footer]( defaults, dbptModals[ modalId ] ) );
            }else if( typeof defaults.footer === 'string' ){
                // is jquery selector?
                try {
                    var footerElement = $( defaults.footer );
                    dbptModals[ modalId ].footer.html( footerElement.html() );
                } catch (err) {
                    dbptModals[ modalId ].footer.html( defaults.footer );
                }
            }
        }

        if( defaults.title ){
            var headerAppend = 'prependTo';
            dbptModals[ modalId ].header = $('<div>', {"class" : "dbpt-modal-title", id : modalId + '_dbptModalTitle'});
            dbptModals[ modalId ].closer = $('<a>', { "href" : "#close", "class":"dbpt-modal-closer", "data-dismiss":"modal", "aria-hidden":"true",id: modalId + '_dbptModalCloser'}).html('&times;');
            dbptModals[ modalId ].title = $('<h3>', {"class" : "modal-label", id : modalId + '_dbptModalLable'});

            dbptModals[ modalId ].title.html( defaults.title ).appendTo( dbptModals[ modalId ].header );
            dbptModals[ modalId ].title.css({ padding: defaults.padding });
            dbptModals[ modalId ].title.append( dbptModals[ modalId ].closer );
            if( dbptModals[ modalId ].config.sticky ){
                if( dbptModals[ modalId ].config.minimized && true !== dbptModals[ modalId ].config.minimized ){
                    setTimeout( function(){
                        dbptModals[ modalId ].title.trigger('click');
                    }, parseInt( dbptModals[ modalId ].config.minimized ) );
                    dbptModals[ modalId ].config.minimized = false;
                }
                dbptModals[ modalId ].closer.hide();
                dbptModals[ modalId ].title.addClass( 'dbpt-modal-closer' ).data('modal', modalId).appendTo( dbptModals[ modalId ].header );
                if( dbptModals[ modalId ].config.sticky.indexOf( 'top' ) > -1 ){
                    headerAppend = 'appendTo';
                }
            }else{
                dbptModals[ modalId ].closer.data('modal', modalId).appendTo( dbptModals[ modalId ].header );
            }
            dbptModals[ modalId ].header[headerAppend]( dbptModals[ modalId ].modal );
        }
        // hide modal
        dbptModals[ modalId ].modal.outerHeight( defaults.height );
        dbptModals[ modalId ].modal.outerWidth( defaults.width );

        if( defaults.content ){
            // function?
            if( typeof defaults.content === 'function' ){
                dbptModals[ modalId ].content.append( defaults.content( defaults, dbptModals[ modalId ] ) );
            }else if( typeof defaults.content === 'string' ){
                // is jquery selector?
                try {
                    var contentElement = $( defaults.content );
                    if( contentElement.length ){
                        dbptModals[ modalId ].content.append( contentElement.detach() );
                        contentElement.show();
                    }else{
                        dbptModals[ modalId ].content.html( defaults.content );
                    }
                } catch (err) {
                    dbptModals[ modalId ].content.html( defaults.content );
                }
            }
        }

        // set position;
        positionModals();
        // return main object
        $( window ).trigger('modal.open');
        return dbptModals[ modalId ];
    }

    $.fn.dbptModal = function( opts ){

        pageHTML        = $('html');
        pageBody        = $('body');
        mainWindow      = $(window);

        if( !opts ){ opts = {}; }
        opts = $.extend( {}, this.data(), opts );
        return $.dbptModal( opts, this );
    }

    // setup resize positioning and keypresses
    if ( window.addEventListener ) {
        window.addEventListener( "resize", positionModals, false );
        window.addEventListener( "keypress", function(e){
            if( e.keyCode === 27 && dbptBackdrop !== null ){
                dbptBackdrop.trigger('click');
            }
        }, false );

    } else if ( window.attachEvent ) {
        window.attachEvent( "onresize", positionModals );
    } else {
        window["onresize"] = positionModals;
    }



    $(document).on('click', '[data-modal]:not(.dbpt-modal-closer)', function( e ){
        e.preventDefault();
        $(this).dbptModal();
    });

    $(document).on( 'click', '.dbpt-modal-closer', function( e ) {
        e.preventDefault();
        $(window).trigger('close.modal');
    })

    $(window).on( 'close.modal', function( e ) {
        closeModal();
    })
    $(window).on( 'modal.init', function( e ) {
        $('[data-modal][data-autoload]').each( function(){
            $( this ).dbptModal();
        });
    })

    $(window).load( function(){
        $(window).trigger('modal.init');
    });

})(jQuery);
