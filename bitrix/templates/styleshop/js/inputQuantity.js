'use strict';

$(function() {

    $(document).on('keydown', '.min-price,.max-price', function(event){
        if((event.which < 48 || event.which > 57) && event.which != 32 && event.which != 8 && event.which != 190 && event.which != 39 && event.which != 37)
        {
            return false;
        }
    });

});