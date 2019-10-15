jQuery(document).ready(function($){
    $("a[data-toggle='status-change'").click(function(e){
        e.preventDefault();
        KTApp.blockPage({overlayColor: '#000000',type: 'v2',state: 'success',size: 'xl'});
        var badge = $('.report-status');
        $.post("/report/status", { status: $(this).attr('data-status'), id: $(this).attr('data-id') })
        .done(function( data ) {
            KTApp.unblockPage();
            badge.removeClass('kt-badge--unified-success');
            badge.removeClass('kt-badge--unified-danger');
            badge.removeClass('kt-badge--unified-warning');
            if(data.success){
                badge.addClass('kt-badge--unified-'+data.class);
                badge.html(data.status);
            }
        })
        .fail(function() {
            KTApp.unblockPage();
            badge.addClass('kt-badge--unified-danger');
            alert( "error" );
        });
    });
});