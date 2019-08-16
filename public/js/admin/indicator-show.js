"use strict";
var KTGoalGeneral=function(){
    var t=$("#kt_modal_add_goal"),
        q=$("#kt_modal_add_goal .modal-content"),
        i=function(t,i,e){
            var n=$('<div class="kt-alert kt-alert--outline alert alert-'+i+' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
            t.find(".alert").remove(),
            n.prependTo(t),
            KTUtil.animateClass(n[0],"fadeIn animated"),
            n.find("span").html(e)
        },
        e=function(t,i){
            $('#goal_'+t).parent().append("<div id=\"goal-"+t+"-error\" class=\"error invalid-feedback\">" + i + "</div>");
            $('#goal_'+t).parent().addClass('is-invalid');
            $('#goal_'+t).addClass('is-invalid');
        };
    return{
        init:function(){
            $(".btn-add-goal").click(function(n){
                t.find(".alert").remove()
            }),
            $("#goal_form").submit(function(n){
                n.preventDefault();
                var r=$(this);
                t.find(".alert").remove(),
                r.validate({rules:{title:{required:!0,maxlength:100}}}),
                r.valid()&&(
                    KTApp.block("#kt_modal_add_ .modal-content",{overlayColor:"#000000",type:"v2",state:"primary",message:"Please wait..."}),
                    r.ajaxSubmit({url:"/goal", 
                      error:function(d){
                          console.error(d);
                          KTApp.unblock(q);
                          i(r,"danger","An error is occurred.");
                      },
                      success:function(m,a,l,o){
                        console.log(m);
                        KTApp.unblock(q);
                        if(m.success===true){
                            $("#kt_modal_add_goal").modal("hide");
                            swal.fire(m.title, m.message,m.status);
                            r.clearForm(),
                            r.validate().resetForm(),
                            $('#kt-goal-list').prepend(m.html);
                        }else{
                            i(r,"danger",m.message);
                            if(m.errors != undefined ){
                                if(m.errors.title != undefined ) e('title', m.errors.title);
                            }
                        }
                      }
                    })
                )
            })
        }
    }
}();
jQuery(document).ready(function(){
    KTGoalGeneral.init()
});