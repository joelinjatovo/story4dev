jQuery(document).ready(function($){
    var i=function(c,t,m){
        $(".message").removeClass('message-success').removeClass('message-danger').removeClass('message-info');
        $(".message").addClass('message-'+c);
        $("#container").addClass('has-message');
        $("#message-title").html(t);
        $("#message-content").html(m);
    };
    
    $('.input-field').on('blur', function () {
        if (!this.value) {
            $(this).parent('.f_row').removeClass('focus');
        } else {
            $(this).parent('.f_row').addClass('focus');
        }
    }).on('focus', function () {
        $(this).parent('.f_row').addClass('focus');
        $('.btn').removeClass('active');
        $('.f_row').removeClass('shake');
    });
    
    $('#signUp').click(function(e){
        e.preventDefault();
        history.replaceState({}, null, '/register');
	    $('#container').addClass("right-panel-active");
	    $('#container').removeClass("forgot-panel-active");
    });
    $('#signIn').click(function(e){
        history.replaceState({}, null, '/login');
        e.preventDefault();
	    $('#container').removeClass("right-panel-active");
    });
    $('#forgot').click(function(e){
        history.replaceState({}, null, '/forgot');
        e.preventDefault();
	    $('#container').addClass("forgot-panel-active");
    });
    $('#cancelForgot').click(function(e){
        e.preventDefault();
	    $('#container').removeClass("forgot-panel-active");
    });
    $('#btnOk').click(function(e){
        e.preventDefault();
	    $('#container').removeClass("has-message");
    });
    $('#sign-in-form').submit(function(e){
        e.preventDefault();
        var form = $(this);
        form.validate({rules:{email:{required:!0,email:!0},password:{required:!0}}});
        if(!form.valid()){
            var finp =  $(this).parent('form').find('input');
            if (!finp.val() == 0) {
                $(this).addClass('active');
            }
            setTimeout(function () {
                $('.f_row').removeClass('shake');
            }, 2000);

            if($('.input-field').val() == 0) {
                $('.input-field').parent('.f_row').addClass('shake');
            }
        }
        form.valid()&&(
            KTApp.block(form,{overlayColor:"#000000",type:"v2",state:"primary",message: KTAppMessages.waiting}),
            form.ajaxSubmit({url:"/login",
                error:function(d){
                    KTApp.unblock(form);
                    i("danger", "Erreur!", "Une erreur s'est produite.");
                },
                success:function(t,s,r,a){
                    KTApp.unblock(form);
    ;               if(t.success===true){
                        i("success", "Succès", "Vous allez être redirigé dans quelques secondes.");
                        KTApp.blockPage({overlayColor: '#000000',type: 'v2',state: 'success',size: 'xl'});
                        $('.overlay-container').css('z-index', 0);
                        setTimeout(function(){
                            window.location.replace(t.redirect);
                        },2e3)
                    }else{
                        i("danger", "Erreur!", t.message)
                    }
                }
            })
        )
    });
    $('#sign-up-form').submit(function(e){
        e.preventDefault();
        var form = $(this);
        form.validate({rules:{email:{required:!0,email:!0,maxlength:100},password:{required:!0,minlength:5},password_confirm:{required:!0,minlength:5,equalTo:'#password'},agree:{required:!0}}});
        if(!form.valid()){
            var finp =  $(this).parent('form').find('input');
            if (!finp.val() == 0) {
                $(this).addClass('active');
            }
            setTimeout(function () {
                $('.f_row').removeClass('shake');
            }, 2000);

            if($('.input-field').val() == 0) {
                $('.input-field').parent('.f_row').addClass('shake');
            }
        }
        form.valid()&&(
            KTApp.block(form,{overlayColor:"#000000",type:"v2",state:"primary",message: KTAppMessages.waiting}),
            form.ajaxSubmit({url:"/register",
                error:function(d){
                    KTApp.unblock(form);
                    i("danger", "Erreur!", "Une erreur s'est produite.");
                },
                success:function(t,s,r,a){
                    KTApp.unblock(form);
    ;               if(t.success===true){
                        i("success", "Succès", t.message);
                        form.clearForm();
                        form.validate().resetForm();
                    }else{
                        i("danger", "Erreur!", t.error);
                    }
                }
            })
        )
    });
    $('#forgot-form').submit(function(e){
        e.preventDefault();
        var form = $(this);
        form.validate({rules:{email:{required:!0,email:!0,maxlength:100}}});
        if(!form.valid()){
            var finp =  $(this).parent('form').find('input');
            if (!finp.val() == 0) {
                $(this).addClass('active');
            }
            setTimeout(function () {
                $('.f_row').removeClass('shake');
            }, 2000);

            if($('.input-field').val() == 0) {
                $('.input-field').parent('.f_row').addClass('shake');
            }
        }
        form.valid()&&(
            KTApp.block(form,{overlayColor:"#000000",type:"v2",state:"primary",message: KTAppMessages.waiting}),
            form.ajaxSubmit({url:"/forgot",
                error:function(d){
                    KTApp.unblock(form);
                    i("danger", "Erreur!", "Une erreur s'est produite.");
                },
                success:function(t,s,r,a){
                    KTApp.unblock(form);
    ;               if(t.success===true){
                        i("success", "Succès", t.message);
                        form.clearForm();
                        form.validate().resetForm();
                    }else{
                        i("danger", "Erreur!", t.error);
                    }
                }
            })
        )
    });
});