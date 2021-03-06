<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Schedule</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <?php
            $session_user_role  = $this->session->userdata('user_role');
            if($session_user_role==4){
            $hospital_id    = $this->session->userdata('hospital_id');
            }
            
            if(!empty($msg)){?>
            <div class="alert alert-success">
            <?php echo $msg;?> </div>
            <?php }?>
            <?php if ($info_message = $this->session->flashdata('info_message')): ?>
            <div id="form-messages" class="alert alert-info" role="alert">
            <?php echo $info_message; ?> </div>
            <?php endif ?>
            <div class="panel panel-default">
                <div class="panel-heading"><a class="btn btn-primary" href="<?php echo base_url('admin/list_schedule')?>"><i class="fa fa-th-list">&nbsp; Schedule List</i></a></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <form role="form" method="post" action="<?php echo base_url('admin/addSchedule') ?>" class="registration_form1" enctype="multipart/form-data">
                                <?php if($session_user_role!=4){?>
                                <div> <label class="col-md-2">Hospital * </label>
                                <div class="col-lg-9"> <select class="wide" name="hospital_id" id="hospital_id" onchange="get_doctor(this.value)">
                                    <option value="">--Select Hospital--</option>
                                    <?php foreach ($hospitals as $value) { ?>
                                    <option value="<?php echo $value->id; ?>" <?php echo set_select('hospital_id', $value->id); ?>><?php echo ucwords($value->hospital_name); ?></option>
                                    <?php } ?>
                                    </select> <span class="red"><?php echo form_error('hospital_id'); ?></span> </div>
                                </div>
                                <?php }elseif($session_user_role==4){?>
                                <input type="hidden" name="hospital_id" value="<?php echo $hospital_id; ?>">
                                <script type="text/javascript">
                                $(document).ready(function() {
                                get_doctor('<?php echo $hospital_id; ?>');
                                });
                                </script>
                                <?php }?>
                                <div> <label class="col-md-2">Doctor Name * </label>
                                <div class="col-lg-9"> <select class="wide" name="doctor_id" id="doctor_id" onchange="getSchedule(this.value)"></select> <span class="red"><?php echo form_error('doctor_id'); ?></span> </div>
                            </div>
                            <div id="data" style="display: none" class="col-lg-6 col-lg-offset-2">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">Doctor Schedule</div>
                                    <div class="panel-body">
                                        <table id="table" class="table table-condensed table-bordered table-striped">
                                            <tr>
                                                <th>Day</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                            </tr>
                                            <tr>
                                                <td colspan="3">Hello</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div id="app"> <label class="col-md-2">Available Days * </label>
                            <div class="col-lg-4">
                                <select class="wide" name="schedule[]" id="schedule" required="required">
                                    <option value="">--Select Days--</option>
                                    <option value="sunday">Sunday</option>
                                    <option value="monday">Monday</option>
                                    <option value="tuesday">Tuesday</option>
                                    <option value="wednesday">Wednesday</option>
                                    <option value="thursday">Thursday</option>
                                    <option value="friday">Friday</option>
                                    <option value="saturday">Saturday</option>
                                </select>
                                <span class="red"><?php echo form_error('schedule[]'); ?></span>
                            </div>
                            <div class="col-lg-2"><input type="text" id="starttime" name="starttime[]" class="form-control times" autocomplete="off" readonly="readonly" placeholder="Start Time" required="required"></div>
                            <div class="col-lg-2"><input type="text" id="endtime" name="endtime[]" class="form-control time" autocomplete="off" readonly="readonly" placeholder="End Time" required="required"></div>
                            <div class="col-lg-2" style="margin-top: 5px;"><i class="fa fa-plus-circle" aria-hidden="true" id="add" style="font-size: 25px;"></i></div>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="col-md-12" align="center">
                            <button type="submit" value="Save" class="btn btn-success">Save</button>&nbsp;
                        <button type="reset" value="Reset" class="btn btn-default">Reset</button> </div>
                    </form>
                </div>
            </div>
            <!-- /.row (nested) -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
</div>
<!-- row -->
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
$('select').niceSelect();
var counter = 2;
$("#add").click(function() {
if (counter > 14) {
alert("Only 14 textboxes allow");
return false;
}
$("#app").append('<div class="form-group" id="box' + counter + '"><label class="col-md-2"></label><div class="col-lg-4"><select class="wide" name="schedule[]" required="required"><option>--Select Days--</option><option value="sunday">Sunday</option><option value="monday">Monday</option><option value="tuesday">Tuesday</option><option value="wednesday">Wednesday</option><option value="thursday">Thursday</option><option value="friday">Friday</option><option value="saturday">Saturday</option></select></div> <div class="col-lg-2"><input type="text" id="starttime" name="starttime[]" class="form-control time" autocomplete="off" readonly="readonly" placeholder="Start Time" required="required"></div><div class="col-lg-2"><input type="text" id="endtime" name="endtime[]" class="form-control time" autocomplete="off" readonly="readonly"  placeholder="End Time" required="required"></div><i class="fa fa-minus-circle remove" aria-hidden="true" id="removeButton" style="font-size:25px;margin-left: 15px;"></i></div>');
$('.time').each(function() {
$(this).timepicker({
timeFormat: 'HH:mm'
});
});
$('select').each(function() {
$(this).niceSelect();
});
counter++;
});
$("body").on("click", ".remove", function() {
$(this).closest("div").remove();
});
$('#starttime').timepicker({
timeFormat: 'HH:mm'
});
$('#endtime').timepicker({
timeFormat: 'HH:mm'
});
});
function getSchedule(doctor_id) {
var appointment_date = $('#appointment_date').val();
var appointment_time = $('#timepicker').val();
$.ajax({
type: "POST",
url: "<?php echo base_url('admin/get_schedule')?>",
data: {
'doctor_id': doctor_id,
'appointment_date': appointment_date,
'appointment_time': appointment_time
},
success: function(data) {
var obj = JSON.parse(data);
$('#table tr').html('');
$('#table').append('<tr><th>Hospital</th><th>Day</th><th>StartTime</th><th>EndTime</th></tr>');
for (var i = 0; i < obj.length; i++) {
$('#table').append('<tr><td>' + obj[i].hospital_name + '</td><td>' + obj[i].day + '</td><td>' + obj[i].starttime + '</td><td>' + obj[i].endtime + '</td></tr>');
$('#data').show();
}
}
});
}
</script>