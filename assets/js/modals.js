(function($){
    
    var uixBackdrop = null,
        uixModals   = {},
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
            modalHeight  = uixModals[ modalId ].config.height,
            modalOuterHeight  = modalHeight,
            modalWidth  = uixModals[ modalId ].config.width,
            top          = 0,
            flickerBD    = false,
            modalReduced = false;

        if( uixBackdrop ){ pageHTML.addClass('has-uix-modal'); }

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

        if( modalHeight + ( uixModals[ modalId ].config.padding * 2 ) > windowHeight && uixBackdrop ){
            modalHeight = windowHeight - ( uixModals[ modalId ].config.padding * 2 );
            modalOuterHeight = '100%';
            if( uixBackdrop ){
                uixBackdrop.css( {
                    paddingTop: uixModals[ modalId ].config.padding,
                    paddingBottom: uixModals[ modalId ].config.padding,
                });
            }
            modalReduced = true;
        }
        if( modalWidth + ( uixModals[ modalId ].config.padding * 2 ) >= windowWidth ){
            modalWidth = '100%';
            if( uixBackdrop ){
                uixBackdrop.css( {
                    paddingLeft: uixModals[ modalId ].config.padding,
                    paddingRight: uixModals[ modalId ].config.padding,
                });
            }
            modalReduced = true;
        }

        if( true === modalReduced ){
            if( windowWidth <= 700 && windowWidth > 600 ){
                if( uixBackdrop ){ modalHeight = windowHeight - ( uixModals[ modalId ].config.padding * 2 ); }
                modalWidth = windowWidth;
                modalOuterHeight = modalHeight - ( uixModals[ modalId ].config.padding * 2 );
                modalWidth = '100%';
                top = 0;
                if( uixBackdrop ){ uixBackdrop.css( { padding : uixModals[ modalId ].config.padding } ); }
            }else if( windowWidth <= 600 ){
                if( uixBackdrop ){ modalHeight = windowHeight; }
                modalWidth = windowWidth;
                modalOuterHeight = '100%';
                top = 0;
                if( uixBackdrop ){ uixBackdrop.css( { padding : 0 } ); }
            }
        }


        // set backdrop
        if( uixBackdrop && uixBackdrop.is(':hidden') ){
            flickerBD = true;
            uixBackdrop.show();
        }
        // title?
        if( uixModals[ modalId ].header ){
            if( uixBackdrop ){ uixBackdrop.show(); }
            modalHeight -= uixModals[ modalId ].header.outerHeight();
            uixModals[ modalId ].closer.css( {
                padding     : ( uixModals[ modalId ].header.outerHeight() / 2 ) - 3.8
            } );
            uixModals[ modalId ].title.css({ paddingRight: uixModals[ modalId ].closer.outerWidth() } );
        }
        // footer?
        if( uixModals[ modalId ].footer ){
            if( uixBackdrop ){ uixBackdrop.show(); }
            modalHeight -= uixModals[ modalId ].footer.outerHeight();
        }

        if( uixBackdrop && flickerBD === true ){
            uixBackdrop.hide();
            flickerBD = false;
        }

        // set final height
        if( modalHeight != modalOuterHeight ){
            uixModals[ modalId ].body.css( {
                height      : modalHeight           
            } );
        }
        uixModals[ modalId ].modal.css( {
            width       : modalWidth    
        } );
        if( uixModals[ modalId ].config.sticky && uixModals[ modalId ].config.minimized ){
            var toggle = {},
                minimizedPosition = uixModals[ modalId ].title.outerHeight() - uixModals[ modalId ].modal.outerHeight();
            if( uixModals[ modalId ].config.sticky.indexOf( 'bottom' ) > -1 ){
                toggle['margin-bottom'] = minimizedPosition;
            }else if( uixModals[ modalId ].config.sticky.indexOf( 'top' ) > -1 ){
                toggle['margin-top'] = minimizedPosition;
            }
            uixModals[ modalId ].modal.css( toggle );
            if( uixModals[ modalId ].config.sticky.length >= 3 ){
                pageBody.css( "margin-" + uixModals[ modalId ].config.sticky[0] , uixModals[ modalId ].title.outerHeight() );
                if( modalReduced ){
                    uixModals[ modalId ].modal.css( uixModals[ modalId ].config.sticky[1] , 0 );
                }else{
                    uixModals[ modalId ].modal.css( uixModals[ modalId ].config.sticky[1] , parseFloat( uixModals[ modalId ].config.sticky[2] ) );
                }
            }
        }
        if( uixBackdrop ){
            uixModals[ modalId ].modal.css( {
                marginTop   : top,
                height      : modalOuterHeight
            } );
            setTimeout( function(){
                uixModals[ modalId ].modal.addClass( 'uix-animate' );
            }, 10);

            uixBackdrop.fadeIn( uixModals[ modalId ].config.speed );
        }

        return uixModals;
    }

    var closeModal = function( obj ){   
        var modalId = $(obj).data('modal'),
            position = 0,
            toggle = {};

        if( obj && uixModals[ modalId ].config.sticky ){

            if( uixModals[ modalId ].config.minimized ){
                uixModals[ modalId ].config.minimized = false
                position = 0;
            }else{
                uixModals[ modalId ].config.minimized = true;
                position = uixModals[ modalId ].title.outerHeight() - uixModals[ modalId ].modal.outerHeight();
            }
            if( uixModals[ modalId ].config.sticky.indexOf( 'bottom' ) > -1 ){
                toggle['margin-bottom'] = position;
            }else if( uixModals[ modalId ].config.sticky.indexOf( 'top' ) > -1 ){
                toggle['margin-top'] = position;
            }
            uixModals[ modalId ].modal.stop().animate( toggle , uixModals[ modalId ].config.speed );
            return;
        }
        var lastModal;
        if( activeModals.length ){
            
            lastModal = activeModals.pop();
            if( uixModals[ lastModal ].modal.hasClass( 'uix-animate' ) && !activeModals.length ){
                uixModals[ lastModal ].modal.removeClass( 'uix-animate' );
                setTimeout( function(){
                    uixModals[ lastModal ].modal.remove();
                    //delete uixModals[ lastModal ];
                }, 500 );
            }else{
                if( uixBackdrop ){
                    uixModals[ lastModal ].modal.hide( 0 , function(){
                        $( this ).remove();
                        //delete uixModals[ lastModal ];
                    });
                }
            }

        }

        if( !activeModals.length ){
            if( uixBackdrop ){
                uixBackdrop.fadeOut( 250 , function(){
                    $( this ).remove();
                    uixBackdrop = null;
                });
            }
            pageHTML.removeClass('has-uix-modal');
        }else{          
            uixModals[ activeModals[ ( activeModals.length - 1 ) ] ].modal.show();
        }

    }
    $.uixModal = function(opts,trigger){
        var defaults    = $.extend(true, {
            element             :   'form',
            height              :   550,
            width               :   620,
            padding             :   12,
            speed               :   250,
            content             :   ''
        }, opts );
        defaults.trigger = trigger;
        if( !uixBackdrop && ! defaults.sticky ){
            uixBackdrop = $('<div>', {"class" : "uix-backdrop"});
            if( ! defaults.focus ){
                uixBackdrop.on('click', function( e ){
                    if( e.target == this ){
                        closeModal();
                    }
                });
            }
            pageBody.append( uixBackdrop );
            uixBackdrop.hide();
        }



        // create modal element
        var modalElement = defaults.element,
            modalId = defaults.modal;

        if( activeModals.length ){

            if( activeModals[ ( activeModals.length - 1 ) ] !== modalId ){
                uixModals[ activeModals[ ( activeModals.length - 1 ) ] ].modal.hide();
            }
        }

        if( typeof uixModals[ modalId ] === 'undefined' ){
            if( defaults.sticky ){
                defaults.sticky = defaults.sticky.split(' ');
                if( defaults.sticky.length < 2 ){
                    defaults.sticky = null;
                }
                activeSticky.push( modalId );
            }
            uixModals[ modalId ] = {
                config  :   defaults
            };

            uixModals[ modalId ].body = $('<div>', {"class" : "uix-modal-body",id: modalId + '_uixModalBody'});
            uixModals[modalId].content = $('<div>', {"class": "uix-modal-content", id: modalId + '_uixModalContent'});


        }else{
            uixModals[ modalId ].config = defaults;
        }
        //add in wrapper
        uixModals[ modalId ].modal = $('<' + modalElement + '>', {
            id                  : modalId + '_uixModal',
            tabIndex            : -1,
            "ariaLabelled-by"   : modalId + '_uixModalLable',
            "method"            : 'post',
            "enctype"           : 'multipart/form-data',
            "class"             : "uix-modal-wrap" + ( defaults.sticky ? ' uix-sticky-modal ' + defaults.sticky[0] + '-' + defaults.sticky[1] : '' )
        });

        // push active
        if( !defaults.sticky ){ activeModals.push( modalId ); }

        // add animate      
        if( defaults.animate && uixBackdrop ){
            var animate         = defaults.animate.split( ' ' ),
                animateSpeed    = defaults.speed + 'ms',
                animateEase     = ( defaults.animateEase ? defaults.animateEase : 'ease' );

            if( animate.length === 1){
                animate[1] = 0;
            }

            uixModals[ modalId ].modal.css( {
                transform               : 'translate(' + animate[0] + ', ' + animate[1] + ')',
                '-web-kit-transition'   : 'transform ' + animateSpeed + ' ' + animateEase,
                '-moz-transition'       : 'transform ' + animateSpeed + ' ' + animateEase,
                transition              : 'transform ' + animateSpeed + ' ' + animateEase
            } );

        }




        // padd content
        uixModals[ modalId ].content.css( {
            //padding : defaults.padding
        } );
        uixModals[ modalId ].body.append( uixModals[ modalId ].content ).appendTo( uixModals[ modalId ].modal );
        if( uixBackdrop ){ uixBackdrop.append( uixModals[ modalId ].modal ); }else{
            uixModals[ modalId ].modal . appendTo( $( 'body' ) );
        }


        if( defaults.footer ){
            if( !uixModals[ modalId ].footer ) {
                uixModals[modalId].footer = $('<div>', {"class": "uix-modal-footer", id: modalId + '_uixModalFooter'});
                uixModals[ modalId ].footer.css({ padding: defaults.padding });

                // function?
                if( typeof window[defaults.footer] === 'function' ){
                    uixModals[ modalId ].footer.append( window[defaults.footer]( defaults, uixModals[ modalId ] ) );
                }else if( typeof defaults.footer === 'string' ){
                    // is jquery selector?
                    try {
                        var footerElement = $( defaults.footer );
                        uixModals[ modalId ].footer.html( footerElement.html() );
                    } catch (err) {
                        uixModals[ modalId ].footer.html( defaults.footer );
                    }
                }
            }

            uixModals[ modalId ].footer.appendTo( uixModals[ modalId ].modal );
        }

        if( defaults.title ){
            var headerAppend = 'prependTo';
            uixModals[ modalId ].header = $('<div>', {"class" : "uix-modal-title", id : modalId + '_uixModalTitle'});
            uixModals[ modalId ].closer = $('<a>', { "href" : "#close", "class":"uix-modal-closer", "data-dismiss":"modal", "aria-hidden":"true",id: modalId + '_uixModalCloser'}).html('&times;');
            uixModals[ modalId ].title = $('<h3>', {"class" : "modal-label", id : modalId + '_uixModalLable'});

            uixModals[ modalId ].title.html( defaults.title ).appendTo( uixModals[ modalId ].header );
            uixModals[ modalId ].title.css({ padding: defaults.padding });
            uixModals[ modalId ].title.append( uixModals[ modalId ].closer );
            if( uixModals[ modalId ].config.sticky ){
                if( uixModals[ modalId ].config.minimized && true !== uixModals[ modalId ].config.minimized ){
                    setTimeout( function(){
                        uixModals[ modalId ].title.trigger('click');
                    }, parseInt( uixModals[ modalId ].config.minimized ) );
                    uixModals[ modalId ].config.minimized = false;
                }
                uixModals[ modalId ].closer.hide();
                uixModals[ modalId ].title.addClass( 'uix-modal-closer' ).data('modal', modalId).appendTo( uixModals[ modalId ].header );
                if( uixModals[ modalId ].config.sticky.indexOf( 'top' ) > -1 ){
                    headerAppend = 'appendTo';
                }
            }else{
                uixModals[ modalId ].closer.data('modal', modalId).appendTo( uixModals[ modalId ].header );
            }
            uixModals[ modalId ].header[headerAppend]( uixModals[ modalId ].modal );
        }
        // hide modal
        uixModals[ modalId ].modal.outerHeight( defaults.height );
        uixModals[ modalId ].modal.outerWidth( defaults.width );

        if( defaults.content && !uixModals[ modalId ].content.children().length ){
            // function?
            if( typeof defaults.content === 'function' ){
                uixModals[ modalId ].content.append( defaults.content( defaults, uixModals[ modalId ] ) );
            }else if( typeof defaults.content === 'string' ){
                // is jquery selector?
                  try {
                    var contentElement = $( defaults.content );
                    if( contentElement.length ){
                        uixModals[ modalId ].content.append( contentElement.html() );
                        contentElement.show();
                    }else{
                        uixModals[ modalId ].content.html( defaults.content );
                    }
                  } catch (err) {
                    uixModals[ modalId ].content.html( defaults.content );
                  }
            }
        }

        // set position;
        positionModals();
        // return main object
        $( window ).trigger('modal.open');
        return uixModals[ modalId ];
    }

    $.fn.uixModal = function( opts ){
        
        pageHTML        = $('html');
        pageBody        = $('body');
        mainWindow      = $(window);

        if( !opts ){ opts = {}; }
        opts = $.extend( {}, this.data(), opts );
        return $.uixModal( opts, this );
    }

    // setup resize positioning and keypresses
    if ( window.addEventListener ) {
        window.addEventListener( "resize", positionModals, false );
        window.addEventListener( "keypress", function(e){
            if( e.keyCode === 27 && uixBackdrop !== null ){
                uixBackdrop.trigger('click');
            }
        }, false );

    } else if ( window.attachEvent ) {
        window.attachEvent( "onresize", positionModals );
    } else {
        window["onresize"] = positionModals;
    }

    $(document).on('click', '[data-modal]:not(.uix-modal-closer)', function( e ){
        e.preventDefault();
        var modal = $(this).uixModal();

        modal.modal.attr('data-load-element', '_parent' ).baldrick({
            request : window.location.href,
            before : function( el, e ){
                $( el ).find( '[type="submit"],button' ).prop( 'disabled', true );
            },
            callback : function( obj ){
                obj.params.trigger.find( '[type="submit"],button' ).prop( 'disabled', false );
                if( obj.data.success ){
                    closeModal();
                }else{

                }

            }
        });
    });

    $(document).on( 'click', '.uix-modal-closer', function( e ) {
        e.preventDefault();
        $(window).trigger('close.modal');
    })

    $(window).on( 'close.modal', function( e ) {
        closeModal();
    })
    $(window).on( 'modal.init', function( e ) {
        $('[data-modal][data-autoload]').each( function(){
            $( this ).uixModal();
        });
    })  

    $(window).load( function(){
        $(window).trigger('modal.init');
    });



})(jQuery);
