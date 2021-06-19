$(document).ready(function () {
    $('body').on('click', '#popup-modal-buttonUserRole', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            dataType: 'html',
            success: function(response) {
                $('#popup-modal-bodyUserRole').html(response);
            },
            error: function (data){
                    console.log(data);
            }
        });
        $('#popup-modalUserRole').modal('show');
    });
});

function datatablesUserRole() {
    var table = $('#datatableUserRole').DataTable({
        dom: 'Bfrtip',
        "ordering": false,
        buttons: []
    });
}
datatablesUserRole();

$(document).ready(function () {
    $(document).on('submit','#popup-formUserRole',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $("#pageloader").fadeIn();
        $.ajax({
            method: "POST",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: $(this).serialize(),
            success: function(message){
                $("#popup-modalUserRole").modal('hide');
                alert_message(message);
                $(".table-responsive").load(location.href + " #datatableUserRole");
                setTimeout(function() {   //calls click event after a certain time
                    datatablesUserRole();
                    $("#pageloader").hide();
                }, 1000);
            },
        });
    }); 
});

$(document).ready(function () {
    $(document).on('submit','.delete-formUserRole',function(e){
        e.preventDefault();
        var confirm_delete = confirm("Are you sure want to delete it?");
        if (confirm_delete == true) {
            $("#pageloader").fadeIn();
            var url = $(this).attr('action');
            $.ajax({
                method: "POST",
                url: url,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: $(this).serialize(),
                success: function(message){
                    alert_message(message);
                    $(".table-responsive").load(location.href + " #datatableUserRole");
                    setTimeout(function() {   //calls click event after a certain time
                        datatablesUserRole();
                        $("#pageloader").hide();
                    }, 1000);
                },
            });
        }
        return confirm_delete;
    }); 
});






