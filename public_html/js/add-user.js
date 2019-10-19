"use strict";


var KTUserEdit = function () {
	var picture;
	var initUserForm = function() {
		picture = new KTAvatar('kt_user_avatar');
	}

	return {
		init: function() {
			initUserForm();
		}
	};
}();

KTUtil.ready(function() {	
	KTUserEdit.init();
});