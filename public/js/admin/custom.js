$(document).ready(function () {
    $('body').on('click', '#popup-modal-button', function(event) {
        $('#popup-modal-body').html('');
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
        setTimeout(function() {   //calls click event after a certain time 
            $('#popup-modal').modal('show');
        }, 1000);
    });
});

function alert_message(message) {
    if(typeof(message.success) != "undefined" && message.success !== null) {
        var messageHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success: </strong> '+ message.success +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        //$('#error_message').html(messageHtml);
        //$('#message').html(messageHtml);
        Swal.fire({icon: 'Success', title: 'Success!', text: message.success })

    }else if(typeof(message.delete) != "undefined" && message.delete !== null) {
        var messageHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Delete: </strong> '+ message.delete +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        //$('#error_message').html(messageHtml);
        //$('#message').html(messageHtml);
        Swal.fire({ icon: 'delete', title: 'Delete!', text: message.delete })

    } else if(typeof(message.error) != "undefined" && message.error !== null){
        var messageHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> '+message.error+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        //$('#message').html(messageHtml);
        Swal.fire({ icon: 'error',  title: 'Oops...', text: message.error})
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
                $("#popup-modal").modal('hide');
                if(typeof(message.success) != "undefined" && message.success !== null) {
                    var messageHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success: </strong> '+ message.success +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    //$('#message').html(messageHtml);

                    $("#navbar_current_Status").load(location.href+" #navbar_current_Status>*","");// after new attendance create. reload navbar_current_Status div
                    setTimeout(function() {   //calls click event after a certain time
                        datatables();
                        getLocationNavbar(); // after new attendance create. reload navbar_current_Status div
                        $("#pageloader").hide();
                        Swal.fire({ icon: 'Success', title: 'Success!', text: message.success})
                    }, 1000);
                } else if(typeof(message.delete) != "undefined" && message.delete !== null) {
                    var messageHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Delete: </strong> '+ message.delete +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    //$('#message').html(messageHtml);
                    $("#navbar_current_Status").load(location.href+" #navbar_current_Status>*","");// after new attendance create. reload navbar_current_Status div
                    setTimeout(function() {   //calls click event after a certain time
                        datatables();
                        getLocationNavbar(); // after new attendance create. reload navbar_current_Status div
                        $("#pageloader").hide();
                        Swal.fire({ icon: 'delete', title: 'Delete!', text: message.delete })
                    }, 1000);
                } else if(typeof(message.error) != "undefined" && message.error !== null){
                    var messageHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> '+message.error+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    //$('#message').html(messageHtml);
                    setTimeout(function() {   //calls click event after a certain time
                        datatables();
                        $("#pageloader").hide();
                        Swal.fire({ icon: 'error', title: 'Oops...', text: message.error})
                    }, 1000);
                }
            },
            error: function(message){
                if(typeof(message.responseJSON.errors) != "undefined" && message.responseJSON.errors !== null){
                    var errors = message.responseJSON.errors;
                    $("#popup-modal").modal('hide');
                    $.each(errors, function (key, val) {
                        var messageHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> '+val[0]+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        //$('#message').append(messageHtml);
                        Swal.fire({ icon: 'error', title: 'Oops...', text: val[0]})
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
        var url = $(this).attr('action');
        var data = $(this).serialize();
        swal({
            title: "Delete?",
            text: "Are you sure want to delete it?",
            type: "warning",
            showCancelButton: !0,
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: !0
        }).then(function (r) {
            if (r.value === true) {
                $("#pageloader").fadeIn();
                $.ajax({
                    method: "POST",
                    url: url,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: data,
                    success: function(message){
                        setTimeout(function() {   //calls click event after a certain time
                            datatables();
                            $("#pageloader").hide();
                            alert_message(message);
                        }, 1000);
                    },
                });
            } else {
                r.dismiss;
            }
        }, function (dismiss) {
            return false;
        })
    }); 

    $(document).on('submit','.replicate-form',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $("#pageloader").fadeIn();
        $.ajax({
            method: "POST",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: data,
            success: function(message){
                setTimeout(function() {   //calls click event after a certain time
                    datatables();
                    $("#pageloader").hide();
                    alert_message(message);
                }, 1000);
            },
        });
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


$(function () {
    $('.genealogy-tree ul').hide();
    $('.genealogy-tree>ul').show();
    $('.genealogy-tree ul.active').show();
    $('.genealogy-tree li').on('click', function (e) {
        var children = $(this).find('> ul');
        if (children.is(":visible")) children.hide('fast').removeClass('active');
        else children.show('fast').addClass('active');
        e.stopPropagation();
    });
});


