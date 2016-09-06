(function() {

    var uix_panels = {};
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
                parent         = document.getElementById( clicked.dataset.parent );
            
            // add parent to register
            if( !uix_panels[ clicked.dataset.parent ] ){
                uix_panels[ clicked.dataset.parent ] = {};
            }
            // if not active tab, set it
            if( !uix_panels[ clicked.dataset.parent ].active_tab ){
                uix_panels[ clicked.dataset.parent ].active_tab = parent.querySelector('[aria-selected="true"]');
            }
            // if not active section, set it
            if( !uix_panels[ clicked.dataset.parent ].active_section ){
                uix_panels[ clicked.dataset.parent ].active_section = parent.querySelector(':scope > .uix-sections > [aria-hidden="false"]');
            }

            // remove setting
            uix_panels[ clicked.dataset.parent ].active_tab.setAttribute('aria-selected', false);
            uix_panels[ clicked.dataset.parent ].active_section.setAttribute('aria-hidden', true);

            // set current
            uix_panels[ clicked.dataset.parent ].active_tab = clicked.parentElement;
            uix_panels[ clicked.dataset.parent ].active_section = target;

            // toggle
            uix_panels[ clicked.dataset.parent ].active_section.setAttribute('aria-hidden', false);
            uix_panels[ clicked.dataset.parent ].active_tab.setAttribute('aria-selected', true);

        }
    });

})( window );