

$(document).on('click','#btn-login', function(e){ 
    e.preventDefault()
    const arr = {
        "name_user":$('#name_user').val(),
        "email_user":$("#email_user").val(),
        "password_user": $('#password_user').val()
    }
    
   $.post('http://localhost:8080/api/dev/create/user',
    arr,
    function(data)
    {
      data.fail ?
        alert('falha ao cadastrar') : alert('cadastrado com sucesso');
      
    });
});

