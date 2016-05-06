/*
 * Requires jquery ui
 */
 
 
(function($){
	var $calroot;

    function selectCurrentWeek() {
        window.setTimeout(function () {
            var t = $calroot.find('.ui-datepicker-current-day a');//.addClass('ui-state-active');
            for (var i = 0; i < 6; i++) {
                if (t.parent().hasClass('ui-datepicker-week-end')) {
                    if (!t.parent().hasClass('ui-datepicker-current-day')) {
                        t = t.parent().next('td').find('a');
                        t.addClass('ui-state-active');
                        i++;
                    }
                    if (i < 6) {
                        t = t.parent().parent().closest('tr').next('tr').find('td').first().find('a');
                        t.addClass('ui-state-active');
                    }
                } else {
                    t = t.parent().next('td').find('a');
                    t.addClass('ui-state-active');
                }
            }

        }, 1);

    }
	function onSelect(date) {
        var startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        var endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate()+ 6);
		$calroot.trigger('weekselected',{
			start:startDate,
			end:endDate,
			weekOf:startDate
		});
        selectCurrentWeek();
    }

    $.fn.weekpicker = function(options){
		var $this = this;
		$calroot = $this;
        var customOnSelect = options.onSelect;
        options.onSelect = function(dt) {
            var date = new Date(dt);
            onSelect(date);
            if (customOnSelect) {
                customOnSelect(date);
            }
        };
        options.showOtherMonths = true;
        options.selectOtherMonths = true;
        $this.datepicker(options);

		//events
		$dprow = $this.find('.ui-datepicker');
		
		$dprow.on('mousemove','td', function() {
            $('.ui-state-hover').removeClass('ui-state-hover');
            var t = $(this).find('a');
            t.addClass('ui-state-hover');
            for (var i = 0; i < 6; i++) {
                if (t.parent().hasClass('ui-datepicker-week-end')) {
                    if (t.parent() != this) {
                        t = t.parent().next('td').find('a');
                        t.addClass('ui-state-hover');
                        i++;
                        if (i < 6) {
                            t = t.parent().parent().closest('tr').next('tr').find('td').first().find('a');
                            t.addClass('ui-state-hover');
                        }
                    } else {
                        t = $(this).parent().closest('tr').next('tr').find('td').first().find('a');
                        t.addClass('ui-state-hover');
                    }
                } else {
                    t = t.parent().next('td').find('a');
                    t.addClass('ui-state-hover');
                }
            }
		});
		/*$dprow.on('mouseleave','td', function() {
			$(this).find('td a').removeClass('ui-state-hover'); 
		});*/
	};
})(jQuery);