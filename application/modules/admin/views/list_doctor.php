<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Doctor </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- row -->
    <div class="row">
        <div class="col-lg-12">
            <?php if ($info_message = $this->session->flashdata('info_message')): ?>
            <div id="form-messages" class="alert alert-success" role="alert">
                <?php echo $info_message; ?>
            </div>
            <?php endif ?>
            <div class="panel panel-default">
                <div class="panel-heading"> Doctor List </div>
                <!-- /.panel-heading -->
                <div class="panel-body table-responsive">
                    <table class="table table-bordered display nowrap" cellspacing="0" width="100%" id="dataTables-example">
                        <thead>
                            <tr>
                                <th>SL.No</th>
                                <th>Picture</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email Address</th>
                                <th>Mobile No</th>
                                <th>Phone No</th>
                                <th>Address</th>
                                <th>Sex</th>
                                <th>Blood Group</th>
                                <th>Date Of Birth</th>
                                <th>Status</th>
                                <th>User Role</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $count=1;

                                if($doctorList){

                                foreach ($doctorList as  $value) { ?>
                            <tr class="odd gradeX">
                                <td>
                                    <?php echo $count; ?>
                                </td>
                                <td><img src="<?php echo base_url($value->doctor_image) ?>" style="max-width: 80px; max-height: 80px;"></td>
                                <td>
                                    <?php echo $value->doctor_fname; ?>
                                </td>
                                <td class="center">
                                    <?php echo $value->doctor_lname; ?>
                                </td>
                                <td class="center">
                                    <?php echo $value->doctor_email; ?>
                                </td>
                                <td class="center">
                                    <?php echo $value->doctor_mobile_no; ?>
                                </td>
                                <td class="center">
                                    <?php echo $value->doctor_phone_no; ?>
                                </td>
                                <td class="center">
                                    <?php echo $value->doctor_address; ?>
                                </td>
                                <td class="center">
                                    <?php echo $value->doctor_gender; ?>
                                </td>
                                <td class="center">
                                    <?php echo $value->doctor_bg; ?>
                                </td>
                                <td class="center">
                                    <?php echo $value->doctor_dob; ?>
                                </td>
                                <td class="center" id="status">
                                    <?php if($value->is_active=='1'){ ?>
                                    <?php if($value->doctor_status=='1'){ ?> <button class="btn btn-primary">Active</button>
                                    <?php } else{ ?> <button class="btn btn-danger">Inactive</button>
                                    <?php } ?> </td>
                                <td class="center">
                                    <?php echo $value->role_name; ?>
                                </td>
                                <td class="center"><a href="<?php echo base_url('admin/edit_doctor/').$value->doctor_id; ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> <a href="<?php echo base_url('admin/edit_doctor/').$value->doctor_id; ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></a> <a href="javascript:void(0)" onclick="delete_user('<?php echo $value->doctor_id?>')"><i class="fa fa-trash-o" aria-hidden="true"></i></a> <i class="fa fa fa-plus" aria-hidden="true" onclick="updateStatus(<?php echo $value->doctor_id; ?>,<?php echo $value->is_active; ?>)"></i> </td>
                            </tr>
                            <?php $count++; } ?> </tbody>
                    </table>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<script type="text/javascript">
function updateStatus(id, status) {
    swal({
        title: "Are you sure?",
        text: "you want to change status?",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        confirmButtonText: "Yes, Change it!",
        confirmButtonColor: "#ec6c62"
    }, function() {
        $.ajax({
            url: "<?php echo base_url('admin/updateStatus')?>",
            data: {
                id: id,
                status: status
            },
            type: "POST"
        }).done(function(data) {
            swal("Deleted!", "status was successfully changed!", "success");
            $('#tr_' + tr_id).remove();
        });
    });
}


   

function delete_user(id) {
    swal({
        title: "Are you sure?",
        text: "want to delete?",
        type: "warning",
        showCancelButton: true,
        closeOnConfirm: false,
        confirmButtonText: "Yes, Delete it!",
        confirmButtonColor: "#ec6c62"
    }, function() {
        $.ajax({
            url: "<?php echo base_url('admin/delete')?>",
            data: {
                id: id,
                table: 'users'
            },
            type: "POST"
        }).done(function(data) {
            swal("Deleted!", "Record was successfully deleted!", "success");
            $('#tr_' + tr_id).remove();
        });
    });
}
</script>