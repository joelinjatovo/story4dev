jQuery(document).ready(function($){
    $(".btn-remove-contributor").on('click',function(e){
        e.preventDefault();
        var $this = $(this);
        KTApp.blockPage({overlayColor: '#000000',type: 'v2',state: 'success',size: 'xl'});
        $.post("/contributor/remove", { id: $this.attr('data-id') })
        .done(function( data ) {
            KTApp.unblockPage();
            alert(data.status);
            if(data.success){
                $this.closest('.contributor-item').remove();
            }
        })
        .fail(function() {
            KTApp.unblockPage();
            alert("my error");
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
            KTApp.blockPage({overlayColor: '#000000',type: 'v2',state: 'success',size: 'xl'});
            searchRequest = $.ajax({
                type: "GET",
                url: "/contributor/search",
                dataType: "json",
                data: {search: value, project: $this.attr('data-project')},
                cache: false,
                success: function (data) {
                    KTApp.unblockPage();
                    $('#search-collaborator-results').html(data.html);
                },
                error: function (response) {
                    KTApp.unblockPage();
                }
            });
        }
    });
    
    $(document).on('click', '.add-contributor', function(e){
        e.preventDefault();
        var $this = $(this);
        $this.hide();
        $('#search-collaborator-results').html('');
        KTApp.blockPage({overlayColor: '#000000',type: 'v2',state: 'success',size: 'xl'});
        $.post("/contributor/add", { user_id: $this.attr('data-id'), project_id: $this.attr('data-project'), })
        .done(function( data ) {
            KTApp.unblockPage();
            if(data.success){
                $this.remove();
            }else{
                $this.show();
            }
        })
        .fail(function() {
            KTApp.unblockPage();
        });
    });
});