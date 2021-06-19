$(document).ready(function () {
    $('body').on('click', '#popup-modal-button', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            dataType: 'html',
            success: function(response) {
                $('#popup-modal-body').html(response);
            },
            error: function (data){
                    console.log(data);
            }
        });

        $('#popup-modal').modal('show');
    });
});

function alert_message(message) {
    if(typeof(message.success) != "undefined" && message.success !== null) {
        var messageHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success: </strong> '+ message.success +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        $('#error_message').html(messageHtml);
        $('#message').html(messageHtml);
    } else if(typeof(message.error) != "undefined" && message.error !== null){
        var messageHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> '+message.error+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        $('#message').html(messageHtml);
    }
    
}

$(document).ready(function () {
    $(document).on('submit','#popup-form',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $("#pageloader").fadeIn();
        $.ajax({
            method: "POST",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: $(this).serialize(),
            success: function(message){
                if(typeof(message.success) != "undefined" && message.success !== null) {
                    $("#popup-modal").modal('hide');
                    var messageHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success: </strong> '+ message.success +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    $('#message').html(messageHtml);
                    $("#navbar_current_Status").load(location.href+" #navbar_current_Status>*","");// after new attendance create. reload navbar_current_Status div
                    setTimeout(function() {   //calls click event after a certain time
                        datatables();
                        getLocationNavbar(); // after new attendance create. reload navbar_current_Status div
                        $("#pageloader").hide();
                    }, 1000);
                } else if(typeof(message.error) != "undefined" && message.error !== null){
                    var messageHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> '+message.error+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    $('#error_message').html(messageHtml);
                    setTimeout(function() {   //calls click event after a certain time
                        datatables();
                        $("#pageloader").hide();
                    }, 1000);
                }
            },
            error: function(message){
                if(typeof(message.responseJSON.errors) != "undefined" && message.responseJSON.errors !== null){
                    var errors = message.responseJSON.errors;
                    $.each(errors, function (key, val) {
                        var messageHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> '+val[0]+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        $('#error_message').append(messageHtml);
                    });
                    
                    setTimeout(function() {   //calls click event after a certain time
                        datatables();
                        $("#pageloader").hide();
                    }, 1000);
                }
            },
        });
    }); 
});

$(document).ready(function () {
    $(document).on('submit','.delete-form',function(e){
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
                    setTimeout(function() {   //calls click event after a certain time
                        datatables();
                        $("#pageloader").hide();
                    }, 1000);
                },
            });
        }
        return confirm_delete;
    }); 
});


// Get Latitude & Longitude For Navbar Current Status
function getLocationNavbar() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            // Success function
            showPositionNavbar, 
            // Error function
            null, 
            // Options. See MDN for details.
            {
               enableHighAccuracy: true,
               timeout: 5000,
               maximumAge: 0
            });
        
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPositionNavbar(position) {
    document.getElementById("latitudeNavbar").value= position.coords.latitude;
    document.getElementById("longitudeNavbar").value= position.coords.longitude;
}
getLocationNavbar();