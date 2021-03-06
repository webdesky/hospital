<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Send Message</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <?php if(!empty($msg)){?>
            <div class="alert alert-success">
                <?php echo $msg;?> </div>
            <?php }?>
            <?php if ($info_message = $this->session->flashdata('info_message')): ?>
            <div id="form-messages" class="alert alert-success" role="alert">
                <?php echo $info_message; ?> </div>
            <?php endif ?>
            <div class="panel panel-default">
                <div class="panel-heading"><a class="btn btn-primary" href="<?php echo base_url('doctor/message_list')?>"><i class="fa fa-list"></i>  Message List </a>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <form role="form" method="post" action="<?php echo base_url('doctor/send_message') ?>" class="registration_form1">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-md-2">Send To * </label>
                                    <div class="col-md-6">
                                        <select class="form-control" multiple="multiple" name="reciever_id[]">
                                            <option value="">--Select Users--</option>
                                            <?php foreach($users_data as $user){?>
                                            <option value="<?php echo $user['id'];?>">
                                                <?php echo ucwords($user['first_name'].' '.$user['last_name']);?>
                                            </option>
                                            <?php }?>
                                        </select>
                                    </div> <span class="red"><?php echo form_error('reciever_id'); ?></span> </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-md-2">Subject * </label>
                                    <div class="col-md-6">
                                        <input type="text" id="subject" name="subject" class="form-control"> </div> <span class="red"><?php echo form_error('subject'); ?></span> </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="col-md-2">Message * </label>
                                    <div class="col-md-6">
                                        <textarea class="form-control" rows="5" id="message" name="message" placeholder="Message">
                                        </textarea>
                                    </div> <span class="red"><?php echo form_error('message'); ?></span>
                                    <script type="text/javascript">
                                    CKEDITOR.replace('message');
                                    </script>
                                </div>
                            </div>
                            <div class="col-md-12" align="center">
                                <button type="submit" value="Save" class="btn btn-success">Save</button>
                                <button type="reset" class="btn btn-default">Reset</button>
                            </div>
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
    $(".registration_form1").validate({
        rules: {
            "reciever_id": "required",
            "subject": "required",
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});
</script>