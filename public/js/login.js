jQuery(document).ready(function($){
    var i=function(c,t,m){
        $(".message").removeClass('message-success').removeClass('message-danger').removeClass('message-info');
        $(".message").addClass('message-'+c);
        $("#container").addClass('has-message');
        $("#message-title").html(t);
        $("#message-content").html(m);
    };
    $('#signUp').click(function(e){
        e.preventDefault();
	    $('#container').addClass("right-panel-active");
	    $('#container').removeClass("forgot-panel-active");
    });
    $('#signIn').click(function(e){
        e.preventDefault();
	    $('#container').removeClass("right-panel-active");
    });
    $('#forgot').click(function(e){
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
        form.valid()&&(
            KTApp.block(form,{overlayColor:"#000000",type:"v2",state:"primary",message: KTAppMessages.waiting}),
            form.ajaxSubmit({url:"/login",
                error:function(d){
                    console.error(d);
                    KTApp.unblock(form);
                    i("danger", "Error!", "Something was wrong.");
                },
                success:function(t,s,r,a){
                    console.log(t);
                    KTApp.unblock(form);
    ;               if(t.success===true){
                        i("success", "Success", "You will be redirect in few secondes."),
                        setTimeout(function(){
                            window.location.replace(t.redirect);
                        },2e3)
                    }else{
                        i("danger", "Error!", "Incorrect username or password. Please try again.")
                    }
                }
            })
        )
    });
    $('#sign-up-form').submit(function(e){
        e.preventDefault();
        var form = $(this);
        form.validate({rules:{email:{required:!0,email:!0,maxlength:100},password:{required:!0,minlength:5},password_confirm:{required:!0,minlength:5,equalTo:'#password'},agree:{required:!0}}});
        form.valid()&&(
            KTApp.block(form,{overlayColor:"#000000",type:"v2",state:"primary",message: KTAppMessages.waiting}),
            form.ajaxSubmit({url:"/register",
                error:function(d){
                    console.error(d);
                    KTApp.unblock(form);
                    i("danger", "Error!", "Something was wrong.");
                },
                success:function(t,s,r,a){
                    console.log(t);
                    KTApp.unblock(form);
    ;               if(t.success===true){
                        i("success", "Success", t.message);
                        form.clearForm();
                        form.validate().resetForm();
                    }else{
                        i("danger", "Error!", t.error);
                    }
                }
            })
        )
    });
    $('#forgot-form').submit(function(e){
        e.preventDefault();
        var form = $(this);
        form.validate({rules:{email:{required:!0,email:!0,maxlength:100}}});
        form.valid()&&(
            KTApp.block(form,{overlayColor:"#000000",type:"v2",state:"primary",message: KTAppMessages.waiting}),
            form.ajaxSubmit({url:"/forgot",
                error:function(d){
                    console.error(d);
                    KTApp.unblock(form);
                    i("danger", "Error!", "Something was wrong.");
                },
                success:function(t,s,r,a){
                    console.log(t);
                    KTApp.unblock(form);
    ;               if(t.success===true){
                        i("success", "Success", t.message);
                        form.clearForm();
                        form.validate().resetForm();
                    }else{
                        i("danger", "Error!", t.error);
                    }
                }
            })
        )
    });
});