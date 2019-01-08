$(function(){
    'use strict';

    $('.btn-danger.btn-delete').on('click', function(e){

        e.preventDefault();

        var id = $(this).data('id');
        var place = $(this).data('place');
        let btn = $(this);

        swal({
            title: "Oops",
            text : "Are you sure you want to delete this user ?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#F0F0F0",
            confirmButtonText: "Delete",
            cancelButtonText: "Cancel",
            closeOnConfirm: false,
            closeOnCancel: false
            
          }, function(isConfirm){

                if(isConfirm)
                {
                    $.ajax({
                    url: base_uri +'admin/users/delete/' + id,
                    method: 'DELETE',
                    success: function(result) {
                            
                            if(result == 1) 
                            {
                                swal ( "Succees" ,  "User deleted Successfully !" ,  "success" );

                                if(place == 'show') btn.parents('.tile').remove(); // if show page remove all taile
                                btn.parents('tr').remove(); // if row  remove it

                            }else{
                                swal ( "Opps" ,  "Something went Wrong !" ,  "error" );
                            }    
                        
                        },
                        error: function(request,msg,error) {
                           swal ( "Opps" ,  "Something went Wrong !" ,  "error" );
                        }
                    });

                }else{

                    swal ( "Cancel" ,  "User Will Not be deleted !" ,  "error" );
                    
                }

          });
        })

});
