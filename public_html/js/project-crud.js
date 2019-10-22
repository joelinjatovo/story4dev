"use strict";

var KTProjectEdit = function () {
	var picture;
	var initProjectForm = function() {
		picture = new KTAvatar('kt_project_piture');
		jQuery('.js-datepicker').datepicker({format: 'yyyy-mm-dd'});
		jQuery("#kt_repeater_2").repeater({
			initEmpty:!1,
			show:function(){
				jQuery(this).slideDown(); 
				jQuery('.js-datepicker').datepicker({format: 'yyyy-mm-dd'});
				var values = jQuery("#kt_repeater_2").repeaterVal();
				var label  = jQuery("#project_periodicity option:selected" ).text();
				var length = values.project.iterations.length;
				jQuery('input[name="project[iterations]['+(length-1)+'][title]"').val(label+" "+length);
				console.log(values);
			},
			hide:function(e){confirm("Etes-vous sûre de vouloir supprimer ?")&&$(this).slideUp(e)}
		});
		jQuery("select#project_periodicity").change(function(){
			initIteration();
		});
	};
	
	var initIteration = function() {
		var label  = jQuery("select#project_periodicity").children("option:selected").text();
		var values = jQuery("#kt_repeater_2").repeaterVal();
		var length = values.project.iterations.length;
		for(var index=1 ; index<=length; index++){
			var input = jQuery('input[name="project[iterations]['+(index-1)+'][title]"');
			input.val(label+" "+index);
		}
	}

	return {
		init: function() {
			initProjectForm();
			initIteration();
		}
	};
}();

KTUtil.ready(function() {	
	KTProjectEdit.init();
});