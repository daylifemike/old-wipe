(function($, window){
    // https://github.com/codecomputerlove/PhotoSwipe
    // http://www.photoswipe.com/

    var plugin_schema = [
        { name : 'allowUserZoom',                       default : true,                 type : 'bool',      label : 'Allow User Zoom',                          help : "Allow the user to zoom / pan around images." },
        { name : 'autoStartSlideshow',                  default : false,                type : 'bool',      label : 'Auto-start Slideshow',                     help : "Automatically starts the slideshow mode when PhotoSwipe is activated." },
        { name : 'allowRotationOnUserZoom',             default : false,                type : 'bool',      label : 'Allow Zoom',                               help : "iOS only - Allow the user to rotate images whilst zooming / panning." },
        { name : 'backButtonHideEnabled',               default : true,                 type : 'bool',      label : 'Enable Back Button Hide',                  help : "This will hide the gallery when the user hits the back button. Useful for Android and Blackberry. Works in BB6, Android v2.1 and above and iOS 4 and above." },
        { name : 'captionAndToolbarAutoHideDelay',      default : 5000,                 type : 'int',       label : 'Caption & Toolbar: Auto-hide Delay',       help : "How long to wait before the caption and toolbar automatically disappear.  Set to 0 to prevent auto disappearing." },
        { name : 'captionAndToolbarFlipPosition',       default : false,                type : 'bool',      label : 'Caption & Toolbar: Flip Position',         help : "Place the caption at the bottom and the toolbar at the top." },
        { name : 'captionAndToolbarHide',               default : false,                type : 'bool',      label : 'Caption & Toolbar: Hide',                  help : "Hide the caption and toolbar." },
        { name : 'captionAndToolbarOpacity',            default : 0.8,                  type : 'float',     label : 'Caption & Toolbar: Opacity',               help : "The opacity of the caption and toolbar." },
        { name : 'captionAndToolbarShowEmptyCaptions',  default : true,                 type : 'bool',      label : 'Caption & Toolbar: Show Empty Captions',   help : "Shows a blank caption area even if a caption cannot be found for the current image." },
        { name : 'doubleTapSpeed',                      default : 300,                  type : 'int',       label : 'Double Tap Speed',                         help : "Double tap speed in milliseconds." },
        { name : 'doubleTapZoomLevel',                  default : 2.5,                  type : 'float',     label : 'Double Tap Zoom Level',                    help : "When the user double taps an image, the default \"zoom-in\" level." },
        { name : 'enableDrag',                          default : true,                 type : 'bool',      label : 'Enable Drag Prev/Next',                    help : "Enables dragging the next / previous image into view." },
        { name : 'enableKeyboard',                      default : true,                 type : 'bool',      label : 'Enable Keyboard Interactions',             help : "Enables keyboard support." },
        { name : 'enableMouseWheel',                    default : true,                 type : 'bool',      label : 'Enable Mousewheel Interactions',           help : "Enables mouse wheel support." },
        { name : 'fadeInSpeed',                         default : 250,                  type : 'int',       label : 'Fade-In Speed',                            help : "The speed of any fading-in elements in milliseconds." },
        { name : 'fadeOutSpeed',                        default : 250,                  type : 'int',       label : 'Fade-Out Speed',                           help : "The speed of any fading-out elements in milliseconds." },
        { name : 'imageScaleMethod',                    default : 'fit',                type : 'select',    label : 'Image Scaling Method',                     options : ['fit', 'fitNoUpscale', 'zoom'], help : "How images will fit onto the screen. \"fit\" ensures the image always fits the screen. \"fitNoUpscale\" works like \"fit\" but will never upscale the image. \"zoom\" the image will always fill the full screen, this may cause the image to be \"zoomed\" in and cropped." },
        { name : 'invertMouseWheel',                    default : false,                type : 'bool',      label : 'Invert Mousewheel',                        help : "By default, moving the mouse wheel down will move to the next image, up to the previous." },
        { name : 'jQueryMobileDialogHash',              default : '&ui-state=dialog',   type : 'string',    label : 'Dialog Hash',                              help : "The window hash tag used by jQuery Mobile and dialog pages." },
        { name : 'loop',                                default : true,                 type : 'bool',      label : 'Loop Gallery',                             help : "Whether the gallery auto-loops back to the beginning when you reach the end." },
        { name : 'margin',                              default : 20,                   type : 'int',       label : 'Margin',                                   help : "The margin between each image in pixels." },
        { name : 'maxUserZoom',                         default : 5.0,                  type : 'float',     label : 'Maximum User Zoom',                        help : "The maximum a user can zoom into an image." },
        { name : 'minUserZoom',                         default : 0.5,                  type : 'float',     label : 'Minimum User Zoom',                        help : "The minimum a user can zoom out of an image." },
        { name : 'mouseWheelSpeed',                     default : 500,                  type : 'int',       label : 'Mousewheel Speed',                         help : "How responsive the mouse wheel is." },
        { name : 'nextPreviousSlideSpeed',              default : 0,                    type : 'int',       label : 'Prev/Next Slide Speed',                    help : "How fast images are displayed when the next/previous buttons are clicked in milliseconds." },
        { name : 'preventHide',                         default : false,                type : 'bool',      label : 'Disable Closing',                          help : "Prevents the user closing PhotoSwipe. Also hides the \"close\" button from the toolbar." },
        { name : 'preventSlideshow',                    default : false,                type : 'bool',      label : 'Disable Slideshow',                        help : "Prevents the slideshow being activated. Also hides the \"play\" button from the toolbar." },
        { name : 'slideshowDelay',                      default : 3000,                 type : 'int',       label : 'Slideshow Delay',                          help : "The delay between showing the next image when in slideshow mode in milliseconds." },
        { name : 'slideSpeed',                          default : 250,                  type : 'int',       label : 'Slide Speed',                              help : "How fast images slide into view in milliseconds." },
        { name : 'swipeThreshold',                      default : 50,                   type : 'int',       label : 'Swipe Threshold',                          help : "How many pixels your finger has to move across the screen to register a swipe gesture." },
        { name : 'swipeTimeThreshold',                  default : 250,                  type : 'int',       label : 'Swipe Time Threshold',                     help : "A swipe must take no longer than this value in milliseconds to be registered as a swipe gesture." },
        { name : 'slideTimingFunction',                 default : 'ease-out',           type : 'string',    label : 'Slide Easing',                             help : "Easing function used when sliding." },
        { name : 'zIndex',                              default : 1000,                 type : 'int',       label : 'zIndex',                                   help : "The intial zIndex for PhotoSwipe." },
    ];

    // var photoswiper_saved_data = {};
    var form_data = {};


    angular.element(document).ready(function() {
        angular.forEach(plugin_schema, function(field, i){
            field.value = field.default;

            if (photoswiper_saved_data[field.name]) {
                // sanitize the data?
                field.value = photoswiper_saved_data[field.name];
            }
        });

        // pull the WP generated form data
        var $form = $('#photoswiper-form');
        form_data.option_page = $form.find('[name="option_page"]').val();
        // form_data.action = $form.find('[name="action"]').val();
        form_data._wpnonce = $form.find('[name="_wpnonce"]').val();
        form_data._wp_http_referer = $form.find('[name="_wp_http_referer"]').val();

        // gogogo
        angular.bootstrap('#photoswiper-form', ['photoswiper']);
    });

    angular.module('photoswiper', []);

    angular.module('photoswiper').controller('photoswiperCtrl', ['$scope',
        function($scope) {
            $scope.schema = plugin_schema;
            $scope.wp_data = form_data;
        }
    ]);

    angular.module('photoswiper').directive('formWatch', 
        function(){
            return {
                restrict : 'A',
                link : function ($scope, $element, $attrs) {
                    function updateValue() {
                        var form = {};
                        var $input = angular.element('[name="'+ $attrs.formWatch +'"]');

                        angular.forEach($scope.schema, function(field, i){
                            form[field.name] = field.value;
                        });

                        $input.val( angular.toJson(form) );                        
                    }

                    $scope.$watch(function() {
                        updateValue();
                    });

                    updateValue();
                }
            };
        }
    );

}(jQuery, window));