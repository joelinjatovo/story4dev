"use strict";
var KTProjectGeneral=function(){
    var t=$("#kt_modal_add_activity"),
        q=$("#kt_modal_add_activity .modal-content"),
        i=function(t,i,e){
            var n=$('<div class="kt-alert kt-alert--outline alert alert-'+i+' alert-dismissible" role="alert">\t\t\t<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>\t\t\t<span></span>\t\t</div>');
            t.find(".alert").remove(),
            n.prependTo(t),
            KTUtil.animateClass(n[0],"fadeIn animated"),
            n.find("span").html(e)
        };
    return{
        init:function(){
            $(".btn-add-activity").click(function(n){
                t.find(".alert").remove()
            }),
            $("#activity_submit").click(function(n){
                t.find(".alert").remove()
            }),
            $("#activity_submit").click(function(n){
                n.preventDefault();
                var s=$(this),r=$(this).closest("form");
                r.validate({rules:{title:{required:!0,maxlength:100}}}),
                r.valid()&&(
                    KTApp.block("#kt_modal_add_activity .modal-content",{overlayColor:"#000000",type:"v2",state:"primary",message:"Please wait..."}),
                    r.ajaxSubmit({url:"/admin/activity", 
                      error:function(d){
                          console.error(d);
                          KTApp.unblock(q);
                          i(r,"danger","An error is occurred.");
                      },
                      success:function(m,a,l,o){
                        console.log(m);
                        KTApp.unblock(q);
                        if(m.success===true){
                            $("#kt_modal_add_activity").modal("hide");
                            swal.fire(m.title, m.message,m.status);
                            r.clearForm(),
                            r.validate().resetForm(),
                            $('#kt-activity-list').prepend(m.html);
                        }else{
                            i(r,"danger",m.message);
                            m.errors.violations.forEach((item, index) => {
                                var path = item.propertyPath;
                                $('#activity-'+path).parent().append("<div id=\"activity-"+path+"-error\" class=\"error invalid-feedback\">" + item.title + "</div>");
                                $('#activity-'+path).parent().addClass('is-invalid');
                                $('#activity-'+path).addClass('is-invalid');
                            });
                        }
                      }
                    })
                )
            })
        }
    }
}();
jQuery(document).ready(function(){
    KTProjectGeneral.init()
});