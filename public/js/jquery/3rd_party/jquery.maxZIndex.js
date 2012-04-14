(function($) {
    
    
    
    /**
     * @see http://west-wind.com/weblog/posts/876332.aspx
     * 
     * Returns the max zOrder in the document (no parameter)
     * Sets max zOrder by passing a non-zero number
     * which gets added to the highest zOrder.
     * 
     * @param inc: increment value, 
     * @param group: selector for zIndex elements to find max for
     * @return jQuery
     */
    
    $.fn.maxZIndex = function(opt) {
        
        var def = { inc: 1, group: "*" };
        
        $.extend(def, opt);
        
        var zmax = 0;
        $(def.group).each(function() {
            var cur = parseInt($(this).css('z-index'));
            zmax = cur > zmax ? cur : zmax;
        });
        
        if (!this.jquery)
            return zmax;
    
        return this.each(function() {
            zmax += def.inc;
            $(this).css("z-index", zmax);
        });
        
    };

    

})(jQuery);