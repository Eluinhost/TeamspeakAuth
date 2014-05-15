$(document).ready(function(){

    $('#request_codes').click(function(e){
        $('#request_code_alert').hide();
        e.preventDefault();
        $.get(
            "{{ path('teamspeakcode') }}",
            {
                'ts_name': $('#ts_name').val()
            }
        )
            .done(function(data) {
                $('#ts_uuid').val(data['UUID']);
            })
            .fail(function(xhr) {
                if(typeof xhr.responseJSON === 'undefined'){
                    $('#request_code_error').html('Unexpected response from server');
                }else{
                    var error = xhr.responseJSON.error;
                    $('#request_code_error').html(error);
                }
                $('#request_code_alert').show();
            });
    });

    $('#check_auth').click(function(e){
        $('#check_auth_alert').hide();
        e.preventDefault();
        $.get(
            "{{ path('authcheck') }}",
            {
                'ts_uuid':$('#ts_uuid').val(),
                'ts_code':$('#ts_code').val(),
                'mc_name':$('#mc_name').val(),
                'code':$('#code').val()
            }
        )
            .done(function(data) {
                $('#auth_success').show();
            })
            .fail(function(xhr) {
                if(typeof xhr.responseJSON === 'undefined'){
                    $('#check_auth_error').html('Unexpected response from server');
                }else{
                    var error = xhr.responseJSON.error;
                    $('#check_auth_error').html(error);
                }
                $('#check_auth_alert').show();
            });
    });
    $('.alert').hide();
    $('.alert-close').click(function(e){
        e.preventDefault();
        $(this).parent().hide();
    });
});