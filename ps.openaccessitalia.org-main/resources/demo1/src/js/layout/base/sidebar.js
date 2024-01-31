"use strict";

var KTLayoutSidebar = function() {
    // Private properties
    var _body;
    var _element;
    var _offcanvasObject;
    var _lastOpenedTab;

    // Private functions
	// Initialize
	var _init = function() {
        // Initialize mobile sidebar offcanvas
		_offcanvasObject = new KTOffcanvas(_element, {
			baseClass: 'sidebar',
			overlay: true,
			closeBy: 'kt_sidebar_close',
			toggleBy: {
				target: 'kt_sidebar_mobile_toggle',
				state: 'active'
			}
		});
	}

    var _initNav = function() {
        var sidebarNav = KTUtil.find(_element, '.sidebar-nav');

        if (!sidebarNav) {
            return;
        }

        KTUtil.scrollInit(sidebarNav, {
            disableForMobile: true,
            resetHeightOnDestroy: true,
            handleWindowResize: true,
            height: function() {
                var height = parseInt(KTUtil.getViewPort().height);
                var sidebarNav = KTUtil.find(_element, '.sidebar-nav');
                var sidebarFooter = KTUtil.find(_element, '.sidebar-footer');

                height = height - (parseInt(KTUtil.css(sidebarNav, 'height')));
                height = height - (parseInt(KTUtil.css(sidebarNav, 'marginBottom')) + parseInt(KTUtil.css(sidebarNav, 'marginTop')));

                height = height - (parseInt(KTUtil.css(sidebarFooter, 'height')));
                height = height - (parseInt(KTUtil.css(sidebarFooter, 'marginBottom')) + parseInt(KTUtil.css(sidebarFooter, 'marginTop')));

                return height;
            }
        });

        $(sidebarNav).on('click', 'a[data-toggle="tab"]', function (e) {
            if ((_lastOpenedTab && _lastOpenedTab.is($(this))) && $('body').hasClass('sidebar-expanded')) {
                KTLayoutSidebar.minimize();
            } else {
                _lastOpenedTab =  $(this);
                KTLayoutSidebar.expand();
            }
        });
    }

    var _initContent = function(parent) {
        var parent = KTUtil.getById(parent);
        var wrapper = KTUtil.find(_element, '.sidebar-wrapper');
        var header = KTUtil.find(parent, '.sidebar-header');
        var content = KTUtil.find(parent, '.sidebar-content');

        // Close Content
        $(header).on('click', '.sidebar-toggle', function (e) {
            KTLayoutSidebar.minimize();
        });

        if (!content) {
            return;
        }

        // Init Content Scroll
        KTUtil.scrollInit(content, {
            disableForMobile: true,
            resetHeightOnDestroy: true,
            handleWindowResize: true,
            height: function() {
                var height = parseInt(KTUtil.getViewPort().height);

                if (KTUtil.isBreakpointUp('lg')) {
                    height = height - KTLayoutHeader.getHeight();
                } 

                if (header) {
                    height = height - parseInt(KTUtil.css(header, 'height'));
                    height = height - parseInt(KTUtil.css(header, 'marginTop'));
                    height = height - parseInt(KTUtil.css(header, 'marginBottom'));
                }

                if (content) {
                    height = height - parseInt(KTUtil.css(content, 'marginTop'));
                    height = height - parseInt(KTUtil.css(content, 'marginBottom'));
                }

                height = height - parseInt(KTUtil.css(wrapper, 'paddingTop'));
                height = height - parseInt(KTUtil.css(wrapper, 'paddingBottom'));

                height = height - parseInt(KTUtil.css(_element, 'paddingTop'));
                height = height - parseInt(KTUtil.css(_element, 'paddingBottom'));

                height = height - 2;

                return height;
            }
        });
    }

    // Public methods
	return {
		init: function(id) {
            _element = KTUtil.getById(id);
            _body = KTUtil.getBody();

            if (!_element) {
                return;
            }

            // Initialize
            _init();
            _initNav();
            _initContent('kt_sidebar_tab_1');
            _initContent('kt_sidebar_tab_2');
            _initContent('kt_sidebar_tab_3');
        },

        getElement: function() {
            return _element;
        },

        getOffcanvas: function() {
            return _offcanvasObject;
        },

        expand: function() {
            KTUtil.addClass(_body, 'sidebar-expanded');
        },

        minimize: function() {
            KTUtil.removeClass(_body, 'sidebar-expanded');
        }
	};
}();

// Webpack support
if (typeof module !== 'undefined') {
	module.exports = KTLayoutSidebar;
}
