 


function check_loginName(id_form){
    var inputs = $(id_form).find('input');
    $.each(inputs, function(index, value){
        if ($(this).val().length == 0){
            $(this).parent().addClass('has-error');
        }else{
            $(this).parent().removeClass('has-error');
        }
    });

    if ($('#login').val().length !== 0){
        $.ajax({
            url: '../handler.php',
            type: "POST",
            data: ({
                action: 'check_loginName',
                login: $('#login').val(),
            }),
            dataType: "html",
            success: function(data){
                data = JSON.parse(data);
                console.log(data)
                if (data['result'] == false){
                    $('#btn_new').attr('type', 'button');
                    $('#login').parent().addClass('has-error');
                    $('.alert-danger').show();
                }else{
                    $('#btn_new').attr('type', 'submit');
                    $('#login').parent().removeClass('has-error');
                    $('.alert-danger').hide();
                }
            }
        });
    }

}




 
