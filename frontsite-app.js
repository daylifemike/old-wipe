(function($, window){
    $(document).ready(function() {
        var data = PhotoSwiper.saved_data;
        var $image_links;
        var selectors = [
            'a[href$="jpg"]',
            'a[href$="jpeg"]',
            'a[href$="jpe"]',
            'a[href$="jif"]',
            'a[href$="jfif"]',
            'a[href$="jfi"]',
            'a[href$="gif"]',
            'a[href$="gif"]',
            'a[href$="png"]',
            'a[href$="tif"]',
            'a[href$="tiff"]',
            'a[href$="svg"]',
            'a[href$="bmp"]',
            'a[href$="xbm"]'
        ];
        var selector = selectors.join(',');

        $image_links = (data.selector) ? $(data.selector) : $(selector).has('img');

        if ( data.selectorFilter ) {
            $image_links = $image_links.filter(function(){
                return !( $(this).is( data.selectorFilter ) );
            });
        }

        if ( data.logSelectorMatch ) {
            var prettySelector = function() {
                var output = [];
                if ( data.selector ) {
                    return data.selector;
                } else {
                    for (var i = 0; i < selectors.length; i++) {
                        output.push(selectors[i] + ':has(img)');
                    };
                    return output.join(', ');
                }
            };
            var prettySelectorFilter = function() {
                if ( data.selectorFilter ) {
                    return "minus elements that match \"" + data.selectorFilter + "\"";
                } else {
                    return "with no additional filtering";
                }
            };
            var clean_list = [];
            $image_links.each(function(){
                clean_list.push(this);
            });
            console.log( "PhotoSwiper: " + clean_list.length + " elements match \"" + prettySelector() + "\" " + prettySelectorFilter() );
            console.log( clean_list );
        }

        if ( $image_links.length > 0 ) {
            $image_links.photoSwipe(data);
        }
    });
}(jQuery, window));