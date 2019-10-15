jQuery(document).ready(function($){
    $(document).on('click', '.btn-remove-contribution', function(e){
        e.preventDefault();
        var $this = $(this);
        KTApp.blockPage({overlayColor: '#000000',type: 'v2',state: 'success',size: 'xl'});
        $.post("/contribution/remove", { id: $this.attr('data-id') })
        .done(function( data ) {
            KTApp.unblockPage();
            if(data.success){
                $this.closest('.contribution-item').remove();
            }
        })
        .fail(function() {
            KTApp.unblockPage();
        });
    });
    
    var searchRequest = null;
    $("#search-collaborator").on('keyup',function(){
        var $this = $(this);
        var minlength = 3;
        var value = $(this).val();
        if (value.length >= minlength ) {
            $('#search-collaborator-results').html('');
            if (searchRequest != null) {
                searchRequest.abort();
            }
            $this.parent().addClass('kt-spinner kt-spinner--v2 kt-spinner--sm kt-spinner--success kt-spinner--right kt-spinner--input');
            searchRequest = $.ajax({
                type: "GET",
                url: "/contribution/search",
                dataType: "json",
                data: {search: value, project: $this.attr('data-project')},
                cache: false,
                success: function (data) {
                    $this.parent().removeClass('kt-spinner kt-spinner--v2 kt-spinner--sm kt-spinner--success kt-spinner--right kt-spinner--input');
                    $('#search-collaborator-results').html(data.html);
                },
                error: function (response) {
                    $this.parent().removeClass('kt-spinner kt-spinner--v2 kt-spinner--sm kt-spinner--success kt-spinner--right kt-spinner--input');
                }
            });
        }
    });
    
    $(document).on('click', '.add-contribution', function(e){
        e.preventDefault();
        var $this = $(this);
        $this.hide();
        $('#search-collaborator-results').html('');
        KTApp.blockPage({overlayColor: '#000000',type: 'v2',state: 'success',size: 'xl'});
        $.post("/contribution/add", { user_id: $this.attr('data-id'), project_id: $this.attr('data-project'), })
        .done(function( data ) {
            KTApp.unblockPage();
            if(data.success){
                $this.remove();
                $("#project-contributions").append(data.html);
            }else{
                $this.show();
            }
        })
        .fail(function() {
            KTApp.unblockPage();
        });
    });
});