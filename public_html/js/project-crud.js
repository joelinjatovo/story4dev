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
			},
			hide:function(e){confirm("Etes-vous sûre de vouloir supprimer ?")&&$(this).slideUp(e)}
		});
	}

	return {
		init: function() {
			initProjectForm();
		}
	};
}();

KTUtil.ready(function() {	
	KTProjectEdit.init();
});