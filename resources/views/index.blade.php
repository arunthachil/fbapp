@extends('layouts.app')
@section('css_content')
<link href="https://datatables.yajrabox.com/css/datatables.bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<style type="text/css">
    .view_friend{
       margin-left: 10px;
    }
</style>
@endsection
@section('content')
<table class="table table-bordered" id="users-table">
   <thead>
      <tr>
         <th>Name</th>
         <th>Email</th>
         <th>Created At</th>
         <th></th>
      </tr>
   </thead>
</table>
<div class="modal fade" id="contact" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content">
         <form class="form-horizontal" action="send.php" method="post">
            <div class="modal-header">
               <h4>Add Friend</h4>
            </div>
            <div class="modal-body">
               <div class="form-group">
                  <label for="contact-email" class="col-lg-2 control-label">E-mail:</label>
                  <div class="col-lg-10">
                     <input type="email" class="form-control" id="contact-email" placeholder="you@example.com">
                  </div>
               </div>
            </div>
            <div class="modal-footer">
               <button class="btn btn-success" type="submit">Send Request</button>
               <a class="btn btn-danger" data-dismiss="modal">Cancel</a>
            </div>
         </form>
      </div>
   </div>
</div>
@endsection
@section('js_content')
<script src="https://datatables.yajrabox.com/js/jquery.dataTables.min.js"></script>
<script src="https://datatables.yajrabox.com/js/datatables.bootstrap.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
<script type="text/javascript">
   $(function() {
       $('#users-table').DataTable({
           processing: true,
           serverSide: true,
           ajax: 'user',
           columns: [
               {data: 'name'},
               {data: 'email'},
               {data: 'created_at'},
               {data: 'action', name: 'action', orderable: false, searchable: false}
           ],
            "bLengthChange": false,
       });
   });
   $(document).on('click', ".add_friend", function (){
       var friend_id = $(this).data('id');
      $.ajax({
          type:'POST',
          url:'/add_friend',
          data: {
           "_token": "{{ csrf_token() }}",
           "friend_id": friend_id
           },
          success:function(data){
           if(data.status=='Success'){
               $.notify(data.msg, "success");  
               $('#friendadd_'+friend_id).html('Request sent');
               $('#friendadd_'+friend_id).removeClass('add_friend btn-primary').addClass('btn-default');
   
           }else{
               $.notify(data.msg, "error");
           }
          }
       });
   });
   
   
</script>
@endsection