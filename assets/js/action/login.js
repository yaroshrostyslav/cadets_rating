$(document).ready(function() {

    var handler = 'handler.php';

    $('#login_form').submit(function(){
        $.ajax({
            url: handler,
            type: "POST",
            data: ({
                action: 'login',
                login: $('#login').val(),
                password: $('#password').val(),
            }),
            dataType: "html",
            success: function(data){
                data = JSON.parse(data);
                if (data['result'] == 1){
                    $('.alert-danger').hide();
                    $('#login, #password').parent().removeClass('has-error');
                    window.location.replace('index.php');
                }else{
                    $('.alert-danger').show();
                    $('#login, #password').parent().addClass('has-error');
                }
            }
        });
        return false;
    });


});
