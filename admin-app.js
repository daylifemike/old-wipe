(function($, angular, window){

    var form_data = {};

    // admin app init
    angular.module('photoswiper', []);

    // admin controller
    angular.module('photoswiper').controller('formCtrl', ['$scope',
        function($scope) {
            $scope.schema = PhotoSwiper.schema;
            $scope.wp_data = form_data;
            $scope.photoswiper = angular.toJson({});
        }
    ]);

    // admin app directives
    angular.module('photoswiper').directive('formWatch',
        function(){
            return {
                restrict : 'A',
                link : function ($scope, $element, $attrs, $controller) {
                    function updateValue() {
                        var form = {};
                        var $input = angular.element('[name="'+ $attrs.formWatch +'"]');

                        angular.forEach($scope.schema, function(field, i){
                            // don't save a string unless it's a string
                            switch (field.type) {
                                case 'int':
                                    form[field.name] = parseInt(field.value);
                                    break;
                                case 'float':
                                    form[field.name] = parseFloat(field.value);
                                    break;
                                default:
                                    form[field.name] = field.value;
                                    break;
                            }
                        });

                        $scope.photoswiper = angular.toJson(form);
                    }

                    $scope.$watch(function() {
                        updateValue();
                    });

                    updateValue();
                }
            };
        }
    );

    // doc.ready
    angular.element(document).ready(function() {
        // pull the WP generated form data
        var $form = $('#photoswiper-form');
        form_data.option_page = $form.find('[name="option_page"]').val();
        // form_data.action = $form.find('[name="action"]').val();
        form_data._wpnonce = $form.find('[name="_wpnonce"]').val();
        form_data._wp_http_referer = $form.find('[name="_wp_http_referer"]').val();

        // gogogo
        angular.bootstrap('#photoswiper-form', ['photoswiper']);
    });

}(jQuery, angular, window));