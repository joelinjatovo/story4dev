"use strict";
var KTLoginGeneral=function(){
    var t=$("#kt_login"),
        i=function(t,i,e){
            var n=$('<div class="kt-alert kt-alert--outline alert alert-'+i+' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
            t.find(".alert").remove(),
            n.prependTo(t),
            KTUtil.animateClass(n[0],"fadeIn animated"),
            n.find("span").html(e)
        },
        e=function(){
            t.removeClass("kt-login--forgot"),
            t.removeClass("kt-login--signup"),
            t.addClass("kt-login--signin"),
            KTUtil.animateClass(t.find(".kt-login__signin")[0],"flipInX animated")
        },
        n=function(){
            $("#kt_login_forgot").click(function(i){
                i.preventDefault(),
                t.removeClass("kt-login--signin"),
                t.removeClass("kt-login--signup"),
                t.addClass("kt-login--forgot"),
                KTUtil.animateClass(t.find(".kt-login__forgot")[0],"flipInX animated")
            }),
            $("#kt_login_forgot_cancel").click(function(t){
                t.preventDefault(),e()
            }),
            $("#kt_login_signup").click(function(i){
                i.preventDefault(),
                t.removeClass("kt-login--forgot"),
                t.removeClass("kt-login--signin"),
                t.addClass("kt-login--signup"),
                KTUtil.animateClass(t.find(".kt-login__signup")[0],"flipInX animated")
            }),
            $("#kt_login_signup_cancel").click(function(t){t.preventDefault(),e()})};
    return{
        init:function(){
            n(),
            $("#kt_login_signin_submit").click(function(t){
                t.preventDefault();
                var e=$(this),
                    n=$(this).closest("form");
                    n.validate({rules:{email:{required:!0,email:!0},password:{required:!0}}}),
                    n.valid()&&(
                        e.addClass("kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light").attr("disabled",!0),
                        n.ajaxSubmit({url:"/login",
                            error:function(d){
                              console.error(d);
                              i(n,"danger", "Something was wrong.")
                            },
                            success:function(t,s,r,a){
                                console.log(t);
    ;                            if(t.success===true){
                                    e.removeClass("kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light").attr("disabled",!1),
                                    i(n,"success","You will be redirect in few secondes."),
                                    setTimeout(function(){
                                        window.location.replace(t.redirect);
                                    },2e3)
                                }else{
                                    e.removeClass("kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light").attr("disabled",!1),
                                    i(n,"danger", "Incorrect username or password. Please try again.")
                                }
                            }
                        })
                    )
            }),
            $("#kt_login_signup_submit").click(function(n){
                n.preventDefault();
                var s=$(this),r=$(this).closest("form");
                r.validate({rules:{fullname:{required:!1,maxlength:100},email:{required:!0,email:!0,maxlength:100},password:{required:!0,minlength:5},password_confirm:{required:!0,minlength:5,equalTo:'#password'},agree:{required:!0}}}),
                r.valid()&&(
                    s.addClass("kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light").attr("disabled",!0),
                    r.ajaxSubmit({url:"/register", 
                      error:function(d){
                          console.error(d);
                          s.removeClass("kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light").attr("disabled",!1);
                          var n=t.find(".kt-login__signin form");
                          n.clearForm(),
                          n.validate().resetForm(),
                          i(r,"danger", "Something was wrong.")
                      },
                      success:function(m,a,l,o){
                        console.log(m);
                        s.removeClass("kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light").attr("disabled",!1);
                        if(m.success===true){
                            r.clearForm(),
                            r.validate().resetForm(),
                            e();
                            var n=t.find(".kt-login__signin form");
                            n.clearForm(),
                            n.validate().resetForm(),
                            i(n,"success",m.message)
                        }else{
                            i(r,"danger",m.error)
                        }
                     }})
                )
            }),
            $("#kt_login_forgot_submit").click(function(n){
                n.preventDefault();
                var s=$(this),r=$(this).closest("form");
                r.validate({rules:{email:{required:!0,email:!0}}}),
                    r.valid()&&(
                    s.addClass("kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light").attr("disabled",!0),
                    r.ajaxSubmit({url:"/forgot",success:function(data,a,l,o){
                          s.removeClass("kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light").attr("disabled",!1),
                          r.clearForm(),
                          r.validate().resetForm(),
                          e();
                          var n=t.find(".kt-login__signin form");
                          n.clearForm(),
                          n.validate().resetForm();
                          if(data.success===true){i(n,"success", data.message);}else{i(n,"danger", data.message);}
                    }})
                )
            })
        }
    }
}();
jQuery(document).ready(function(){
    KTLoginGeneral.init()
});