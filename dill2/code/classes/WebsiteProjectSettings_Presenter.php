<?php

require_once("PresenterBase.php");
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"WebsiteProjectSettings_constants.php"
);


class WebsiteProjectSettings_Presenter extends PresenterBase
{
	public function __construct(
		$_view,
		$_model)
	{
		parent::__construct(
			$_view,
			$_model);
		
		// Set possible values for wxchoice "Authentication method".
		$this->view_wxchoice_sftp_authentication_method_set_choices();
			
		// Fetch data from db.
		$this->model_fetch_data_from_db();
		
		// Update the view.
		$this->update_view();
			
		$this->view->wxbutton_ok->Connect(
			wxEVT_COMMAND_BUTTON_CLICKED,
			array(
				$this,
				"view_wxbutton_ok_clicked"));
		
		$this->view->wxbutton_cancel->Connect(
			wxEVT_COMMAND_BUTTON_CLICKED,
			array(
				$this,
				"view_wxbutton_cancel_clicked"));
	}		

	
	public function view_wxbutton_ok_clicked()
	{
		$this->save_settings();
		$this->view->Close();
	}
	
	
	public function view_wxbutton_cancel_clicked()
	{
		$this->view->Close();
	}
	
	
	public function model_fetch_data_from_db()
	{
		$this->model->load_settings();
	}
	
	
	// Sets possible sftp authentication methods for the wxchoice control.
	protected function view_wxchoice_sftp_authentication_method_set_choices()
	{
		$this->view->wxchoice_sftp_authentication_method->Append(DILL2_SFTP_AUTHENTICATION_METHOD_SSH);
		$this->view->wxchoice_sftp_authentication_method->Append(DILL2_SFTP_AUTHENTICATION_METHOD_SFTP);
	}
	
	
	protected function update_view()
	{
		$this->view->wxtextctrl_websitetitle->SetValue(
			$this->model->get_website_project_title());
			
		$this->view->wxtextctrl_websitetestserver_address->SetValue(
			$this->model->get_testserver_address());
			
		$this->view->wxtextctrl_websitetestserver_port->SetValue(
			$this->model->get_testserver_port());
			
			
		$this->view->wxtextctrl_sftp_path_on_webserver->SetValue(
			$this->model->get_sftp_webserver_path());
		
		$this->view->wxtextctrl_sftp_webserver_ip_address->SetValue(
			$this->model->get_sftp_webserver_ip_address());
			
		$this->view->wxtextctrl_sftp_username->SetValue(
			$this->model->get_sftp_username());
			
		$this->view->wxfilepickerctrl_sftp_password->SetPath(
			$this->model->get_sftp_password());
			
		$this->view->wxfilepickerctrl_sftp_privatekey->SetPath(
			$this->model->get_sftp_privatekey());
			
		$this->view->wxfilepickerctrl_sftp_privatekey_passphrase->SetPath(
			$this->model->get_sftp_privatekey_passphrase());			
			
		$this->view->wxchoice_sftp_authentication_method->SetStringSelection(
			$this->model->get_sftp_authentication_method());
						
		$this->view->wxtextctrl_ftps_path_on_webserver->SetValue(
			$this->model->get_ftps_webserver_path());

		$this->view->wxtextctrl_ftps_webserver_ip_address->SetValue(
			$this->model->get_ftps_webserver_ip_address());
			
		$this->view->wxtextctrl_ftps_username->SetValue(
			$this->model->get_ftps_username());

		$this->view->wxfilepickerctrl_ftps_password->SetPath(
			$this->model->get_ftps_password());

		$this->view->wxcheckbox_ftps_use_ftp->SetValue(
			$this->model->get_ftps_use_ftp());
		
		$this->view->wxcheckbox_ftps_mode_passive->SetValue(
			$this->model->get_ftps_mode_passive());
	}
	
	
	protected function save_settings()
	{
		$this->model->set_website_project_title(
			$this->view->wxtextctrl_websitetitle->GetValue());
			
		$this->model->set_testserver_address(
			$this->view->wxtextctrl_websitetestserver_address->GetValue());
		
		$this->model->set_testserver_port(
			intval(
				$this->view->wxtextctrl_websitetestserver_port->GetValue(),
				10));
				
				
		$this->model->set_sftp_webserver_path(
			$this->view->wxtextctrl_sftp_path_on_webserver->GetValue());
		
		$this->model->set_sftp_webserver_ip_address(
			$this->view->wxtextctrl_sftp_webserver_ip_address->GetValue());
			
		$this->model->set_sftp_username(
			$this->view->wxtextctrl_sftp_username->GetValue());
			
		$this->model->set_sftp_password(
			$this->view->wxfilepickerctrl_sftp_password->GetPath());

		$this->model->set_sftp_privatekey(
			$this->view->wxfilepickerctrl_sftp_privatekey->GetPath());
			
		$this->model->set_sftp_privatekey_passphrase(
			$this->view->wxfilepickerctrl_sftp_privatekey_passphrase->GetPath());					

		$this->model->set_sftp_authentication_method(			
			$this->view->wxchoice_sftp_authentication_method->GetStringSelection());
			
			
		$this->model->set_ftps_webserver_path(
			$this->view->wxtextctrl_ftps_path_on_webserver->GetValue());
			
		$this->model->set_ftps_webserver_ip_address(
			$this->view->wxtextctrl_ftps_webserver_ip_address->GetValue());

		$this->model->set_ftps_username(
			$this->view->wxtextctrl_ftps_username->GetValue());

		$this->model->set_ftps_password(
			$this->view->wxfilepickerctrl_ftps_password->GetPath());

		$this->model->set_ftps_use_ftp(
			$this->view->wxcheckbox_ftps_use_ftp->GetValue());
			
		$this->model->set_ftps_mode_passive(
			$this->view->wxcheckbox_ftps_mode_passive->GetValue());
			
		
		$this->model->save_settings();
	}

}

?>