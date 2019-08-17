const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const forgotButton = document.getElementById('forgot');
const cancelForgotButton = document.getElementById('cancelForgot');
const okButton = document.getElementById('btnOk');
const container = document.getElementById('container');

signUpButton.addEventListener('click', () => {
	container.classList.add("right-panel-active");
	container.classList.remove("forgot-panel-active");
});

signInButton.addEventListener('click', () => {
	container.classList.remove("right-panel-active");
});

forgotButton.addEventListener('click', () => {
	container.classList.add("forgot-panel-active");
});

cancelForgotButton.addEventListener('click', () => {
	container.classList.remove("forgot-panel-active");
});

okButton.addEventListener('click', () => {
	container.classList.remove("has-message");
});

jQuery(document).ready(function($){
    var i=function(c,t,m){
        $(".message").removeClass('message-success').removeClass('message-danger').removeClass('message-info');
        $(".message").addClass('message-'+c);
        $("#container").addClass('has-message');
        $("#message-title").html(t);
        $("#message-content").html(m);
    };
    $('#sign-in-form').submit(function(e){
        e.preventDefault();
        var form = $(this);
        form.validate({rules:{email:{required:!0,email:!0},password:{required:!0}}});
        form.valid()&&(
            KTApp.block(form,{overlayColor:"#000000",type:"v2",state:"primary",message: KTAppOptions}),
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
    })
    $('#sign-up-form').submit(function(e){
        e.preventDefault();
        var form = $(this);
        form.validate({rules:{email:{required:!0,email:!0,maxlength:100},password:{required:!0,minlength:5},password_confirm:{required:!0,minlength:5,equalTo:'#password'},agree:{required:!0}}});
        form.valid()&&(
            KTApp.block(form,{overlayColor:"#000000",type:"v2",state:"primary",message: KTAppOptions}),
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
    })
});