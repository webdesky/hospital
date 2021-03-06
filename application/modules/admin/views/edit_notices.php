<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">NoticeBoard</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    <!-- /.row -->
    <div class="row">

        <div class="col-lg-12">

            <?php if ($info_message = $this->session->flashdata('info_message')): ?>
            <div id="form-messages" class="alert alert-success" role="alert">
                <?php echo $info_message; ?>
            </div>
            <?php endif ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <button class="btn btn-primary"><i class="fa fa-th-list">&nbsp;Add Notice</i></button>
                </div>


                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <form role="form" method="post" action="<?php echo base_url('admin/notices/'.$notices[0]->id) ?>" class="registration_form1" enctype="multipart/form-data">

                                <div class="form-group"> <label class="col-md-2">Title * </label>
                                    <div class="col-lg-6"> <input class="form-control" type="text" name="title" placeholder="title" autocomplete="off" required="required" value="<?php echo $notices[0]->title; ?>">
                                        <span class="red"><?php echo form_error('title'); ?></span> </div>
                                </div>

                                <div class="form-group"> <label class="col-md-2">Description * </label>
                                    <div class="col-lg-6"> <textarea class="form-control" rows="5" id="Description" name="description" placeholder="description"><?php echo $notices[0]->description; ?>  </textarea>
                                        <span class="red"><?php echo form_error('description'); ?></span>
                                        <script type="text/javascript">
                                            CKEDITOR.replace('description');
                                        </script>
                                    </div>
                                </div>

                                <div class="form-group"> <label class="col-md-2">Start Date * </label>
                                    <div class="col-lg-6"> <input type="text" id="start_date" name="start_date" class="form-control date" autocomplete="off" readonly="readonly" required="required" value="<?php echo $notices[0]->start_date; ?>"><span class="red"><?php echo form_error('start_date'); ?></span> </div>
                                </div>

                                <div class="form-group"> <label class="col-md-2">End Date * </label>
                                    <div class="col-lg-6"> <input type="text" id="end_date" name="end_date" class="form-control date" autocomplete="off" readonly="readonly" required="required" value="<?php echo   $notices[0]->end_date;?>">
                                        <span class="red"><?php echo form_error('end_date'); ?></span> </div>
                                </div>

                                <div class="col-md-12" align="center"> <button type="submit" value="Save" class="btn btn-success">Save</button><input type="reset" class="btn btn-default" value="Reset"> </div>

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