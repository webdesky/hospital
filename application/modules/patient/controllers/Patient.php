<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Patient extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function index($msg = NULL)
    {
        
        if (!empty($this->session->userdata['user_role'])) {
            $log = $this->session->userdata['user_role'];
            if (isset($log) && $log == 1) {
                $this->dashboard();
            } else {
                $data['user_role'] = $this->Common_model->getAllwhere('user_role');
                $data['msg']       = $msg;
                $this->load->view('admin/login', $data);
            }
        } else {
            if (isset($msg) && !empty($msg)) {
                $data['msg'] = $msg;
            } else {
                $data['msg'] = '';
            }
            $data['user_role'] = $this->Common_model->getAllwhere('user_role');
            $data['msg']       = $msg;
            $this->load->view('admin/login', $data);
        }
    }
    public function dashboard()
    {
        if ($this->controller->checkSession()) {
            $this->appointment_list();
        } else {
            $this->index();
        }
    }
    public function appointment_list()
    {
        if ($this->controller->checkSession()) {
            $notification = $this->model->getcount('appointment', array(
                'patient_id' => $this->session->userdata('id'),
                'is_active' => 1
            ), 'updated_by,appointment_date');
            
            if (!empty($notification)) {
                $this->session->set_userData('notification', $notification);
            }
            $where                   = array(
                'user_role' => 2,
                'patient_id' => $this->session->userdata('id')
            );
            $field_val               = 'appointment.id,appointment.problem,appointment.is_active,appointment.appointment_date,appointment.appointment_time,appointment.appointment_id,users.first_name,users.last_name';
            $data['appointmentList'] = $this->model->GetJoinRecord('appointment', 'doctor_id', 'users', 'id', $field_val, $where);
            
            $data['body']            = 'appointment_list';
            $this->controller->load_view($data);
        } else {
            redirect('admin/index');
        }
    }
    
    public function get_notification()
    {
        $notification = $this->model->getAllwhere('appointment', array(
            'patient_id' => $this->session->userdata('id'),
            'notification_status' => 1
        ), 'updated_by,appointment_date,id');
        
        print_r(json_encode($notification));
    }
    
    public function read_notification()
    {
        $appointment_id = $this->uri->segment(3);
        $data           = array(
            'notification_status' => 0
        );
        $this->model->updateFields('appointment', $data, array(
            'id' => $appointment_id
        ));
        $this->session->unset_userdata('notification');
        $this->appointment_list();
        
        
        
    }
    public function view_appointment($id)
    {
        if ($this->controller->checkSession()) {
            $where               = array(
                'user_role' => 2
            );
            $wheres              = array(
                'user_role ' => 3
            );
            $data['doctor']      = $this->model->getAllwhere('users', $where);
            $data['patient']     = $this->model->getAllwhere('users', $wheres);
            $where1              = array(
                'appointment.id ' => $id
            );
            $data['appointment'] = $this->model->GetJoinRecord('appointment', 'doctor_id', 'users', 'id', '', $where1);
            $data['body']        = 'view_appointment';
            $this->load->view('common/templates/default', $data);
            
        } else {
            $this->index();
        }
    }
    public function document_list()
    {
        if ($this->controller->checkSession()) {
            $where = array(
                'patient_id' => $this->session->userdata('id')
            );
            
            $field_val              = "documents.id as did,users.first_name,documents.description,documents.file,users.id,users.last_name";
            $data['documents_list'] = $this->model->GetJoinRecord('documents', 'doctor_id', 'users', 'id', $field_val, $where);
            
            $data['body'] = 'document_list';
            
            $this->controller->load_view($data);
        } else {
            redirect('admin/index');
        }
    }
    
    
    public function edit_document($id = '')
    {
        if ($this->controller->checkSession()) {
            $where                      = array(
                'user_role ' => 2
            );
            $data['doctor']             = $this->model->getAllwhere('users', $where);
            $where1                     = array(
                'documents.id' => $id
            );
            $data['edit_document_data'] = $this->model->GetJoinRecord('documents', 'id', 'document_files', 'document_id', 'documents.id as id,documents.description,document_files.description as file_description,document_files.image,documents.doctor_id', $where1);
            $data['edit_doc_id']        = $id;
            $data['body']               = 'add_document';
            if ($this->input->post('doctor_id')) {
                $this->form_validation->set_rules('doctor_id', 'Doctor Name', 'trim|required');
                $this->form_validation->set_rules('description', 'Description', 'trim|required');
                if ($this->form_validation->run() == false) {
                    $this->session->set_flashdata('errors', validation_errors());
                } else {
                    
                    if (!empty($_FILES['file']['name'][0])) {
                        
                        $doctor_id   = $this->input->post('doctor_id');
                        $patient_id  = $this->session->userdata('id');
                        $description = $this->input->post('description');
                        $data        = array(
                                            'patient_id' => $patient_id,
                                            'description' => $description,
                                            'doctor_id' => $doctor_id,
                                            'is_active' => 1,
                                            'created_at' => date('Y-m-d H:i:s')
                                            );
                        
                        $delete_document = $this->model->delete('documents', array(
                            'id' => $id
                        ));
                        $delete_files    = $this->model->delete('document_files', array(
                            'document_id' => $id
                        ));
                        $result          = $this->model->insertData('documents', $data);
                        $new_array       = array();
                        for ($i = 0; $i < 10; $i++) {
                            if (isset($_FILES['file']['name'][$i]) && !empty($_FILES['file']['name'][$i])) {
                                if ($_FILES['file']['error'][$i] == 0) {
                                    $f_extension = explode('.', $_FILES['file']['name'][$i]); //To breaks the string into array
                                    $f_extension = strtolower(end($f_extension)); //end() is used to retrun a last element to the array
                                    $f_newfile   = "";
                                    if ($_FILES['file']['name'][$i]) {
                                        $f_newfile            = uniqid() . '.' . $f_extension;
                                        $store                = "asset/uploads/" . $f_newfile;
                                        $image                = move_uploaded_file($_FILES['file']['tmp_name'][$i], $store);
                                        $new_array[]['image'] = $f_newfile;
                                        
                                    }
                                }
                            }
                        }
                        
                        $desc = $this->input->post('file_description');
                        if (!empty($desc)) {
                            foreach ($desc as $key => $value) {
                                $new_array[$key]['document_id'] = $result;
                                $new_array[$key]['description'] = $value;
                            }
                        }
                        
                        $this->model->insertBatch('document_files', $new_array);
                    }
                    redirect("patient/document_list", "refresh");
                }
            }
            $this->controller->load_view($data);
        } else {
            redirect('admin/index');
        }
    }
    public function add_document()
    {
        if ($this->controller->checkSession()) {
            if ($this->input->post('doctor_id')) {
                $this->form_validation->set_rules('doctor_id', 'Doctor Name', 'trim|required');
                if (empty($_FILES['file']['name'][0])) {
                    $this->form_validation->set_rules('file', 'Attach File', 'required');
                }
                $this->form_validation->set_rules('description', 'Description', 'trim|required');
                
                if ($this->form_validation->run() == false) {
                    $this->session->set_flashdata('errors', validation_errors());
                    $data['body'] = 'add_document';
                    $this->controller->load_view($data);
                } else {
                    if ($this->controller->checkSession()) {
                        $doctor_id   = $this->input->post('doctor_id');
                        $patient_id  = $this->session->userdata('id');
                        $description = $this->input->post('description');
                        $data        = array(
                            'patient_id' => $patient_id,
                            'description' => $description,
                            'doctor_id' => $doctor_id,
                            'is_active' => 1,
                            'created_at' => date('Y-m-d H:i:s')
                        );
                        
                        $result = $this->model->insertData('documents', $data);
                        
                        
                        $new_array = array();
                        
                        for ($i = 0; $i < 10; $i++) {
                            if (isset($_FILES['file']['name'][$i]) && !empty($_FILES['file']['name'][$i])) {
                                if ($_FILES['file']['error'][$i] == 0) {
                                    $f_extension = explode('.', $_FILES['file']['name'][$i]); //To breaks the string into array
                                    $f_extension = strtolower(end($f_extension)); //end() is used to retrun a last element to the array
                                    $f_newfile   = "";
                                    if ($_FILES['file']['name'][$i]) {
                                        $f_newfile            = uniqid() . '.' . $f_extension;
                                        $store                = "asset/uploads/" . $f_newfile;
                                        $image                = move_uploaded_file($_FILES['file']['tmp_name'][$i], $store);
                                        $new_array[]['image'] = $f_newfile;
                                        
                                    }
                                }
                            }
                        }
                        
                        $desc = $this->input->post('file_description');
                        if (!empty($desc)) {
                            foreach ($desc as $key => $value) {
                                $new_array[$key]['document_id'] = $result;
                                $new_array[$key]['description'] = $value;
                            }
                        }
                        
                        $this->model->insertBatch('document_files', $new_array);
                        $this->document_list();
                    }
                }
            } else {
                $where          = array(
                    'user_role ' => 2
                );
                $data['doctor'] = $this->model->getAllwhere('users', $where);
                $data['body']   = 'add_document';
                $this->controller->load_view($data);
            }
        } else {
            redirect('admin/index');
        }
    }
    
    public function prescription_list()
    {
        if ($this->controller->checkSession()) {
            $where                     = array(
                'prescription.patient_id' => $this->session->userdata('id')
            );
            $field_val                 = 'prescription.*, users.first_name,users.last_name';
            $data['prescription_list'] = $this->model->GetJoinRecord('prescription', 'patient_id', 'users', 'id', $field_val, $where, '', 'prescription.id', 'DESC');
            
            if (!empty($data['prescription_list'][0]->id)) {
                $prescription_id = $data['prescription_list'][0]->id;
                $where1          = array(
                    'prescription_id' => $prescription_id
                );
                $data['review']  = $this->model->getAllwhere('review', $where1);
            }
            $data['body'] = 'prescription_list';
            $this->controller->load_view($data);
        } else {
            redirect('admin/index');
        }
    }
    public function view_prescription($id = null)
    {
        if ($this->controller->checkSession()) {
            $where                = array(
                'prescription.id' => $id
            );
            $field_val            = 'prescription.*,users.first_name,users.last_name,users.date_of_birth,users.gender,hospitals.hospital_name,hospitals.address';
            $data['prescription'] = $this->model->GetJoinRecordNew('prescription', 'patient_id', 'hospital_id', 'users', 'id', 'hospitals', 'id', 'id', $id, $field_val);
            $where1               = array(
                'prescription_id' => $id
            );
            $data['medicine']     = $this->model->getAllwhere('medicine', $where1, '*', 'DESC');
            $data['diagnosis']    = $this->model->getAllwhere('diagnosis', $where1, '*', 'DESC');
            $data['body']         = 'view_prescription';
            $this->controller->load_view($data);
        } else {
            redirect('admin/index');
        }
    }
    public function profile()
    {
        if ($this->controller->checkSession()) {
            $where         = array(
                'id' => $this->session->userdata('id')
            );
            $data['users'] = $this->model->getAllwhere('users', $where);
            
            $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|alpha|min_length[2]');
            $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|alpha|min_length[2]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('date_of_birth', 'Date Of Birth', 'trim|required');
            
            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('errors', validation_errors());
                $data['body'] = 'profile';
                $this->controller->load_view($data);
            } else {
                if ($this->controller->checkSession()) {
                    
                    $first_name  = $this->input->post('first_name');
                    $last_name   = $this->input->post('last_name');
                    $email       = $this->input->post('email');
                    $address     = $this->input->post('address');
                    $phone_no    = $this->input->post('phone');
                    $mobile_no   = $this->input->post('mobile');
                    $dob         = $this->input->post('date_of_birth');
                    $gender      = $this->input->post('gender');
                    $blood_group = $this->input->post('blood_group');
                    
                    $data = array(
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'email' => $email,
                        'address' => $address,
                        'phone_no' => $phone_no,
                        'mobile' => $mobile_no,
                        'date_of_birth' => $dob,
                        'gender' => $gender,
                        'blood_group' => $blood_group
                    );
                    
                    
                    if (isset($_FILES['image']['name']) && !empty($_FILES['image']['name'])) {
                        if (move_uploaded_file($_FILES['image']['tmp_name'], 'asset/uploads/' . $_FILES['image']['name'])) {
                            
                            $data['profile_pic'] = $_FILES['image']['name'];
                        }
                    }
                    
                    $result = $this->model->updateFields('users', $data, $where);
                    redirect('/patient/profile', 'refresh');
                }
            }
        } else {
            redirect('admin/index');
        }
    }
    public function change_password()
    {
        if ($this->controller->checkSession()) {
            $this->form_validation->set_rules('old_password', 'Old Password', 'trim|required');
            $this->form_validation->set_rules('new_password', 'New Password', 'trim|required');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required');
            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('errors', validation_errors());
                $data['body'] = 'change_password';
                $this->controller->load_view($data);
            } else {
                if ($this->controller->checkSession()) {
                    $data   = array(
                        'password' => md5($this->input->post('new_password', TRUE))
                    );
                    $where  = array(
                        'id' => $this->session->userdata('id')
                    );
                    $table  = 'users';
                    $result = $this->model->updateFields($table, $data, $where);
                    redirect('patient/change_password', 'refresh');
                }
            }
        } else {
            redirect('admin/index');
        }
    }
    public function check_password()
    {
        if ($this->controller->checkSession()) {
            $old_password = $this->input->post('data');
            $where        = array(
                'id' => $this->session->userdata('id'),
                'password' => md5($old_password)
            );
            
            $result = $this->model->getsingle('users', $where);
            if (!empty($result)) {
                echo '0';
            } else {
                echo '1';
            }
        } else {
            redirect('admin/index');
        }
    }
    public function addAppointment($id = null)
    {
        if ($this->controller->checkSession()) {
            $this->form_validation->set_rules('hospital_id', 'Hospital', 'trim|required');
            $this->form_validation->set_rules('doctor_id', 'doctor_id', 'trim|required');
            $this->form_validation->set_rules('appointment_date', 'appointment_date', 'trim|required');
            $this->form_validation->set_rules('appointment_time', 'appointment_time', 'trim|required');
            $this->form_validation->set_rules('problem', 'problem', 'trim|required');
            
            if (empty($id)) {
                $this->form_validation->set_rules('doctor_id', 'doctor_id', 'trim|required');
                $this->form_validation->set_rules('appointment_date', 'appointment_date', 'trim|required');
                $this->form_validation->set_rules('appointment_time', 'appointment_time', 'trim|required');
                $this->form_validation->set_rules('problem', 'problem', 'trim|required');
            }
            
            if ($this->form_validation->run() == false) {
                $this->session->set_flashdata('errors', validation_errors());
                $data['body']       = 'add_appointment';
                $where              = array(
                    'is_active' => 1
                );
                $data['speciality'] = $this->model->getAllwhere('speciality', $where);
                $data['hospitals']  = $this->model->getAllwhere('hospitals', $where);
                $this->controller->load_view($data);
            } else {
                
                if ($this->controller->checkSession()) {
                    
                    $data = $this->input->post();
                    
                    $data = array(
                        'appointment_type' => 'Online',
                        'appointment_id' => 'AP' . mt_rand(100000, 999999),
                        'patient_id' => $this->session->userdata('id'),
                        'doctor_id' => $data['doctor_id'],
                        'appointment_date' => $data['appointment_date'],
                        'appointment_time' => $data['appointment_time'],
                        'is_active' => '0',
                        'hospital_id' => $data['hospital_id'],
                        'problem' => $data['problem'],
                        'created_at' => date('Y-m-d H:i:s')
                    );
                    
                    if (!empty($id)) {
                        
                        $where = array(
                            'id' => $id
                        );
                        unset($data['created_at']);
                        unset($data['appointment_id']);
                        $result = $this->model->updateFields('appointment', $data, $where);
                    } else {
                        $result = $this->model->insertData('appointment', $data);
                    }
                    $this->session->set_flashdata("info_message", "Appointment updated Successfully..");
                    redirect("patient/appointment_list/");
                }
            }
        } else {
            redirect('admin/index');
        }
    }
    public function review_doctor($doctor_id = null, $prescription_id = null)
    {
        if ($this->controller->checkSession()) {
            $data['prescription_id'] = $prescription_id;
            $where                   = array(
                'id' => $doctor_id
            );
            $data['doctor']          = $this->model->getAllwhere('users', $where);
            $data['body']            = 'review_doctor';
            
            $this->controller->load_view($data);
        } else {
            redirect('admin/index');
        }
    }
    public function doctor_review()
    {
        if ($this->controller->checkSession()) {
            $data        = $this->input->post();
            $improvement = implode(",", $data['improvement']);
            $data        = array(
                'doctor_id' => $data['doctor_id'],
                'patient_id' => $this->session->userdata('id'),
                'prescription_id' => $data['prescription_id'],
                'recommendation' => $data['recommendation'],
                'problem' => $data['problem'],
                'experience' => $data['experience'],
                'improvement' => $improvement,
                'rating' => $data['rating'],
                'created_at' => date('Y-m-d H:i:s')
            );
            $result      = $this->model->insertData('review', $data);
            redirect('patient/prescription_list');
        } else {
            redirect('admin/index');
        }
    }
    public function get_schedule()
    {
        if ($this->controller->checkSession()) {
            $doctor_id        = $this->input->post('doctor_id');
            $appointment_time = $this->input->post('appointment_time');
            $appointment_date = $this->input->post('appointment_date');
            $day              = date('l', strtotime($appointment_date));
            $where            = array(
                'doctor_id' => $doctor_id
            );
            $data             = $this->model->getAllwhere('schedule', $where);
            echo json_encode($data);
        } else {
            redirect('admin/index');
        }
    }
    public function get_time()
    {
        if ($this->controller->checkSession()) {
            $doctor_id        = $this->input->post('doctor_id');
            $appointment_date = $this->input->post('appointment_date');
            $day              = date('l', strtotime($appointment_date));
            $where            = array(
                'doctor_id' => $doctor_id,
                'appointment_date' => $appointment_date
            );
            $field_val        = 'appointment_time';
            $data             = $this->model->getAllwhere('appointment', $where, '', '', $field_val);
            print_r(json_encode($data));
        } else {
            redirect('admin/index');
        }
    }
    public function get_hospitals()
    {
        if ($this->controller->checkSession()) {
            $id   = $this->input->get('id');
            $data = $this->Common_model->find_hospital_records($id);
            print_r(json_encode($data));
        } else {
            redirect('admin/index');
        }
    }
}