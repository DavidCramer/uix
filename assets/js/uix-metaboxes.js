(function() {

    var uix_metaboxes = {};

    document.addEventListener('click', function (e) {
        if( 
            e.which === 1 && 
            ( e.target.classList.contains( 'uix-tab-trigger' ) ||
                ( e.target.parentElement && e.target.parentElement.classList.contains( 'uix-tab-trigger' ) )
            )
        ){
            e.preventDefault();
            var clicked         = e.target.classList.contains( 'uix-tab-trigger' ) ? e.target : e.target.parentElement,
                target          = document.getElementById( clicked.hash.substr(1) ),
                metabox         = document.getElementById( clicked.dataset.metabox );
            
            // add metabox to register
            if( !uix_metaboxes[ clicked.dataset.metabox ] ){
                uix_metaboxes[ clicked.dataset.metabox ] = {};
            }
            // if not active tab, set it
            if( !uix_metaboxes[ clicked.dataset.metabox ].active_tab ){
                uix_metaboxes[ clicked.dataset.metabox ].active_tab = metabox.querySelector('[aria-selected="true"]');
            }
            // if not active section, set it
            if( !uix_metaboxes[ clicked.dataset.metabox ].active_section ){
                uix_metaboxes[ clicked.dataset.metabox ].active_section = metabox.querySelector('[aria-hidden="false"]');
            }

            // remove setting
            uix_metaboxes[ clicked.dataset.metabox ].active_tab.setAttribute('aria-selected', false);
            uix_metaboxes[ clicked.dataset.metabox ].active_section.setAttribute('aria-hidden', true);

            // set current
            uix_metaboxes[ clicked.dataset.metabox ].active_tab = clicked.parentElement;
            uix_metaboxes[ clicked.dataset.metabox ].active_section = target;

            // toggle
            uix_metaboxes[ clicked.dataset.metabox ].active_section.setAttribute('aria-hidden', false);
            uix_metaboxes[ clicked.dataset.metabox ].active_tab.setAttribute('aria-selected', true);

        }
    });

})( window );