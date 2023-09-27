(function($) {
'use strict';
    var $window = jQuery( window ),
        $document = jQuery( document ),
        $adminMenuWrap = jQuery( '#m_ver_menu' ),
        $wpwrap = jQuery( '#m_aside_left' ),
        $adminbar = jQuery( '#m_header' ),

        menuTop = 0,
        menuIsPinned = false,
        pinnedMenuTop = false,
        pinnedMenuBottom = false,
        lastScrollPosition = 0,
        height = {
            window: $window.height(),
            wpwrap: $wpwrap.height(),
            adminbar: $adminbar.height(),
            menu: $adminMenuWrap.height()
        };

    function pinMenu( event ) {
        var windowPos = $window.scrollTop(),
            menuIsPinned = true;

        if ( height.menu + height.adminbar < height.window ||
            height.menu + height.adminbar + 20 > height.wpwrap ) {
            unpinMenu();
            return;
        }

        if ( height.menu + height.adminbar > height.window ) {

            // Check for overscrolling
            if ( !(windowPos > 0) ) {

                if ( ! pinnedMenuTop ) {
                    pinnedMenuTop = true;
                    pinnedMenuBottom = false;

                    $adminMenuWrap.css({
                        position: 'fixed',
                        top: '',
                        bottom: '',
                    });
                }
                return;

            } else if ( windowPos + height.window > $document.height() - 1 ) {

                if ( ! pinnedMenuBottom ) {
                    pinnedMenuBottom = true;
                    pinnedMenuTop = false;

                    $adminMenuWrap.css({
                        position: 'fixed',
                        top: '',
                        bottom: 0,
                    });
                }
                return;

            }

            if ( windowPos > lastScrollPosition ) {

                // Scrolling down
                if ( pinnedMenuTop ) {

                    // let it scroll
                    pinnedMenuTop = false;
                    menuTop = $adminMenuWrap.offset().top - ( windowPos - lastScrollPosition );

                    if ( menuTop + height.menu < windowPos + height.window ) {
                        menuTop = windowPos + height.window - height.menu;
                    }

                    $adminMenuWrap.css({
                        position: 'absolute',
                        top: menuTop,
                        bottom: '',
                    });
                } else if ( ! pinnedMenuBottom && $adminMenuWrap.offset().top + height.menu < windowPos + height.window ) {

                    // pin the bottom
                    pinnedMenuBottom = true;

                    $adminMenuWrap.css({
                        position: 'fixed',
                        top: '',
                        bottom: 0,
                    });
                }
            } else if ( windowPos < lastScrollPosition ) {

                // Scrolling up
                if ( pinnedMenuBottom ) {
                    // let it scroll
                    pinnedMenuBottom = false;
                    menuTop = $adminMenuWrap.offset().top + ( lastScrollPosition - windowPos );

                    if ( menuTop + height.menu - height.adminbar > windowPos + height.window ) {
                        menuTop = windowPos;
                    }

                    $adminMenuWrap.css({
                        position: 'absolute',
                        top: menuTop,
                        bottom: '',
                    });
                } else if ( ! pinnedMenuTop && $adminMenuWrap.offset().top >= windowPos + height.adminbar ) {
                    // pin the top
                    pinnedMenuTop = true;

                    $adminMenuWrap.css({
                        position: 'fixed',
                        top: '',
                        bottom: '',
                    });
                }
            }

        }

        lastScrollPosition = windowPos;
    }

    function resetHeights() {
        height = {
            window: $window.height(),
            wpwrap: $wpwrap.height(),
            adminbar: $adminbar.height(),
            menu: $adminMenuWrap.height()
        };
    }

    function unpinMenu() {
        if ( ! menuIsPinned ) {
            return;
        }

        pinnedMenuTop = pinnedMenuBottom = menuIsPinned = false;
        $adminMenuWrap.css({
            position: 'fixed',
            top: '',
            bottom: '',
        });
    }

    function setPinMenu() {
        resetHeights();

        if ( $window.width() < 1024 ){
            unpinMenu();
        } else if ( height.menu + height.adminbar > height.window ) {
            pinMenu();
        } else {
            unpinMenu();
        }
    }

    setPinMenu();
    jQuery( window ).scroll(function() {
        setPinMenu();
    });

    jQuery( window ).resize(function() {
        setPinMenu();
    });

    if ( $window.width() > 1024 ){
        jQuery(document).on('click', '.m-menu__item--submenu > a', function(e) {
            e.preventDefault();

            var $wrapper = $(this).closest('.m-menu__item--submenu');

            if( $wrapper.hasClass('m-menu__item--open') ) {
                $wrapper.removeClass('m-menu__item--open').removeClass('m-menu__item--active');
            }else {
                $wrapper.addClass('m-menu__item--open').addClass('m-menu__item--active');
            }
            
        });
    }
})(jQuery);