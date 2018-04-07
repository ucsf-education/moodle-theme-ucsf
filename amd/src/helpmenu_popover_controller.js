/**
 * Controls the message popover in the nav bar.
 *
 * See template: theme_ucsfx/helpmenu_popover
 *
 * @module     theme_ucsfx/helpmenu_popover_controller
 * @class      helpmenu_popover_controller
 * @package    theme_ucsfx
 */
define(['jquery', 'core/templates', 'core/str', 'core/custom_interaction_events', 'core/popover_region_controller' ],
    function($, Templates, Str, CustomEvents, PopoverController) {

        /**
         * Constructor for the HelpmenuPopoverController.
         * Extends PopoverRegionController.
         *
         * @param {object} element jQuery object root element of the popover
         */
        var HelpmenuPopoverController = function(element) {
            // Initialise base class.
            PopoverController.call(this, element);
        };

        /**
         * Clone the parent prototype.
         */
        HelpmenuPopoverController.prototype = Object.create(PopoverController.prototype);

        /**
         * Make sure the constructor is set correctly.
         */
        HelpmenuPopoverController.prototype.constructor = HelpmenuPopoverController;

        /**
         * Set the correct aria label on the menu toggle button to be read out by screen
         * readers.
         *
         * @method updateButtonAriaLabel
         */
        HelpmenuPopoverController.prototype.updateButtonAriaLabel = function() {
            if (this.isMenuOpen()) {
                Str.get_string('hidehelpmenuwindow', 'theme_ucsfx').done(function(string) {
                    this.menuToggle.attr('aria-label', string);
                }.bind(this));
            } else {
                 Str.get_string('showhelpmenuwindow', 'theme_ucsfx').done(function(string) {
                    this.menuToggle.attr('aria-label', string);
                }.bind(this));
            }
        };

        /**
         * Add all of the required event listeners for this helpmenu popover.
         *
         * @method registerEventListeners
         */
        HelpmenuPopoverController.prototype.registerEventListeners = function() {
            CustomEvents.define(this.root, [
                CustomEvents.events.keyboardActivate,
            ]);

            // Update the message information when the menu is opened.
            this.root.on(this.events().menuOpened, function() {
                this.updateButtonAriaLabel();
            }.bind(this));

            // Update the message information when the menu is opened.
            this.root.on(this.events().menuClosed, function() {
                this.updateButtonAriaLabel();
            }.bind(this));
        };

        return HelpmenuPopoverController;
    });
