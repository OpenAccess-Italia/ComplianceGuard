"use strict";

var KTLayoutStickyCard = function() {
    // Private properties
	var _element;
    var _object;

	// Private functions
	var _init = function() {

        _object = new KTCard(_element, {
			sticky: {
				offset: KTLayoutHeader.getHeight(),
				zIndex: 90,
				position: {
					top: function() {
						var pos = 0;
                        var body = KTUtil.getBody();

						if (KTUtil.isBreakpointUp('lg')) {
							if (KTLayoutHeader.isFixed()) {
								pos = pos + KTLayoutHeader.getHeight();
							}

							if (KTLayoutSubheader.isFixed()) {
								pos = pos + KTLayoutSubheader.getHeight();
							}
						} else {
							if (KTLayoutHeader.isFixedForMobile()) {
								pos = pos + KTLayoutHeader.getHeightForMobile();
							}
						}

						return pos;
					},
					left: function(card) {
						return KTUtil.offset(_element).left;
					},
					right: function(card) {
						var body = KTUtil.getBody();

						var cardWidth = parseInt(KTUtil.css(_element, 'width'));
						var bodyWidth = parseInt(KTUtil.css(body, 'width'));
						var cardOffsetLeft = KTUtil.offset(_element).left;

						return bodyWidth - cardWidth - cardOffsetLeft;
					}
				}
			}
		});

		_object.initSticky();

		KTUtil.addResizeHandler(function() {
			_object.updateSticky();
		});
	}

    // Public methods
	return {
		init: function(id) {
            _element = KTUtil.getById(id);

            if (!_element) {
                return;
            }

            // Initialize
			_init();
		},

		update: function() {
			if (_object) {
				_object.updateSticky();
			}
		}
	};
}();

// Webpack support
if (typeof module !== 'undefined') {
	module.exports = KTLayoutStickyCard;
}
