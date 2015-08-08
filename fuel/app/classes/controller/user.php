<?php


class Controller_User extends Controller_Base
{
    public function action_get_login_form()
    {
        $block = View::forge('user/login');
        $block->validation = Validation::forge();
        $block->destination = Input::get('destination', '');
        
        return $block;
    }
    
	public function action_login()
	{
        $this->template->title = 'User &raquo; Login';
        $this->template->content = $this->action_get_login_form();
        
        $post = Input::post();        

        if (!empty($post))
        {
            //get the destination path to redirect to upon login
            $destination = empty($_REQUEST['destination']) ? '/welcome' : $_REQUEST['destination'];
            $this->template->content->destination = $destination;
            
			//add server-side validation
			$validation = $this->template->content->validation;
			$validation->add_field('username_or_email', 'Username or Email', 'required');
			$validation->add_field('password', 'Password', 'required');                 

			if ($validation->run())
			{
				try {
					$authenticated_flag = false;

					if ($this->user && \Access::can('impersonate_any_user', $this->user))
					{
						Warden::logout();
						$authenticated_flag = Warden::force_login($validation->validated('username_or_email'), true /*check confirmation*/);
					}
					else
					{
						$authenticated_flag = Warden::authenticate($validation->validated('username_or_email'), $validation->validated('password'), Input::post('remember_me'));
					}

					if ($authenticated_flag)
					{
						Response::redirect(Uri::create($destination, array(), array(), false /* logged in, so force http as we don't need ssl */));
					}
					else
					{
						Session::set_flash('error', 'Invalid username/email or password entered.');
					}
				} catch (Warden\Failure $failure) {
					switch ($failure->reason())
					{
						case 'unconfirmed': //user is unconfirmed - let them know they need to confirm and activate their account                          
							Session::set_flash('error', $failure->getMessage());                      
							$this->template->content = View::forge('user/unconfirmed');
							$this->template->content->user = $failure->get_user();
							$this->template->content->user->send_confirmation_instructions();
							break;
						
						case 'locked':
							Session::set_flash('error', array(
								'Your account has been locked due to too many consecutive failed login attempts.',
								'Check your email for instructions on unlocking your account. Or you can wait a few days and try again.'
							));
							break;
						
						default:
							Session::set_flash('error', $failure->getMessage());
					}
				} catch (Exception $ex) {
					Session::set_flash('error', $ex->getMessage());
				}
			}
			else
			{
				Session::set_flash('error', 'Invalid username/email or password entered.');
			}
        }
	}

	public function action_logout()
	{
		Warden::logout();
        Response::redirect('/welcome');
	}
        
	public function action_register($user_id = null)
	{
        $this->template->title = 'User &raquo; Register';
        
        try
        {
			$this->include_client_scripts('jquery_forms');
            $this->template->content = View::forge('user/register');
			$this->template->content->title = 'Create Your Account';

            if (!$this->user)
            {
                $post = Input::post();
                
                if (!empty($post))
                { 
                    $this->template->content->validation = \Validation::forge(); 
                    $validation = $this->template->content->validation;
                    
                    //add server-side validation to the form fields (because you can't always rely on the client)
                    if (!$this->user)
                    {
                        //validation for new users
                        $validation->add('username', 'Username')->add_rule('required')->add_rule('match_pattern', '#^[A-Za-z0-9]{6,}$#');
                        $validation->add_field('email', 'Email', 'required|valid_email');
                        $validation->add('password', 'Password')->add_rule('required')->add_rule('match_pattern', '#^\S{8,}$#');
                        $validation->add('password_confirm', 'Confirm Password')->add_rule('required')->add_rule('match_field', 'password');
                    }

                    if ($validation->run()) //if server-side validation passed
                    {                        
                        if (!$this->user)
                        {
                            //new user creation
                            $user = new \Warden\Model_User($validation->validated());
                            $user->save();
                            $user->send_confirmation_instructions();
                            $this->template->title = 'User &raquo; New Registration';
                            $this->template->content = View::forge('user/unconfirmed');  
                            $this->template->content->user = $user;
                            $user_id = $user->id;
                        }
                    }
                    else
                    {
                        //in case client-side validation didn't run, server-side validation will fail as well, so display the errors if that happens
                        $errors = $validation->error();
                        $error_messages = array();

                        foreach ($errors as $field=>$error)
                        {
                            switch ($field)
                            {
                                case 'username':
                                    $error_messages[] = 'Your username must contain at least 6 characters using only letters and numbers (case-insensitive)';
                                    break;

                                case 'password':
                                    $error_messages[] = 'Your password must contain at least 8 characters (case-sensitive, no spaces)';
                                    break;

                                case 'email':
                                    $error_messages[] = $claim_agency_domain ? "Must be a valid email address within the $claim_agency_domain domain" : 'Must be a valid email address';
                                    break;

                                default:
                                    $error_messages[] = $error->get_message();
                            }        
                        }

                        Session::set_flash('error', $error_messages);
                    }
                }
            }
            else
            {
                Session::set_flash('error', 'You are currently logged in, so you cannot register');
            }
        } catch (\Orm\ValidationFailed $ex) {
            Session::set_flash('error', $ex->getMessage());
        } catch (Exception $ex) {
            $msg = $ex->getMessage(); 
            Session::set_flash('error', $msg ? $msg : 'Oops, something went wrong.');
            \Response::redirect('/welcome/404');
        }
	}

    public function action_unconfirmed()
    {        
        if ($this->user)
        {
            if ($this->user->is_confirmation_required())
            {
                $this->template->title = 'User &raquo; Send Activation Email';
                $this->template->content = View::forge('user/unconfirmed');  
                $this->template->content->user = $this->user;
                $this->user->send_confirmation_instructions();
                Session::set_flash('success', 'Activation email sent.');
            }
            else
            {
                Session::set_flash('error', 'Your account is already active.');
                Response::redirect('/welcome');
            }
        }
        else
        {
            Response::redirect('/welcome/404'); //page not found
        }
    }
    
    public function action_confirm()
    {
        $success = false;
        $msg = array();
        $user = null;
        
        try {
            $token = Request::active()->param('token');
            $user = \Warden\Model_User::confirm_by_token($token);
            if ($user) {
                $msg[] = 'Welcome ' . $user->username . ', your account is now activated.';
                $success = true;
            } else {
                $msg[] = 'Invalid token.';
            }
        } catch (\Warden\Failure $ex) {
              // token has expired (if enabled)
            $msg[] = $ex->getMessage();
        } catch (Exception $ex) {
              // Server/DB error
            $msg[] = 'Oops, something went wrong.';
        }
      
        if ($success)
        {
            $claim = \Session::get('claim', null);
 
            if ($claim && $user && ($claim['user_id'] === $user->id)) //if this is a new user who registered as an agency user to claim or post an agency
            {
                if ($claim['user_id'] === $user->id)
                {
                    $msg[] = !empty($claim['agency_id']) ? 'Please login again to complete your claim.' : 'Please login again to add your organization.';
                }
            }
            else
            {
                $msg[] = 'Please login again.';
            }
            
            \Session::set_flash('success', $msg);            
        }
        else
        {
            $msg[] = 'Please login again to receive a new activation email.';
            \Session::set_flash('error', $msg);
        }
        
        Response::redirect('/user/login');
    }
  
    public function action_unlock()
    {
        $success = false;
        $msg = array();
        
        try {
            $token = Request::active()->param('token');
            $user = \Warden\Model_User::unlock_access_by_token($token);
            if ($user) {
                $msg[] = 'Welcome ' . $user->username . ', your account is now unlocked.';
                $msg[] = 'You can now login.';
                $success = true;
            } else {
                $msg[] = 'Invalid token.';
            }
        } catch (\Warden\Failure $ex) {
              // token has expired (if enabled)
            $msg[] = $ex->getMessage();
        } catch (Exception $ex) {
              // Server/DB error
            $msg[] = 'Oops, something went wrong.';
        }
      
        if ($success)
        {
            \Session::set_flash('success', $msg);            
        }
        else
        {
            \Session::set_flash('error', $msg);
        }
        
        Response::redirect('/user/login');
    }

	public function action_forgot_password()
	{
        if ($this->user)
        {
            Response::redirect('/welcome/404'); //page not found
        }
        else
        {
            $this->template->title = 'User &raquo; Forgot Password';
            $this->template->content = View::forge('user/forgot_password');
            $this->template->content->validation = Validation::forge();
            
            Package::load('captcha');
            $this->template->content->captcha = Captcha::forge('simplecaptcha');
            
            $this->include_client_scripts('jquery_forms');

            $post = Input::post();        

            if (!empty($post))
            {
                //add server-side validation
                $captcha = $this->template->content->captcha;
                $validation = $this->template->content->validation;
                $validation->add_field('username_or_email', 'Username or Email', 'required');
                $validation->add('captcha', 'Captcha')->add_rule('required')->add_rule(array('captcha' => function($val, $captcha) { return $captcha->check(); }), $captcha);
                
                if ($validation->run())
                {
                    try {
                        $user = \Warden\Model_User::authenticate($validation->validated('username_or_email'), true);

                        if ($user)
                        {
                            $user->send_reset_password_instructions();
                            Session::set_flash('success', 'An email was sent to you with instructions to reset your password.');
                        }
                        else
                        {
                            Session::set_flash('error', 'Invalid username/email entered.  Account does not exist.');
                        }
                    } catch (Warden\Failure $failure) {
                        Session::set_flash('error', $failure->getMessage());
                    } catch (Exception $ex) {
                        Session::set_flash('error', $ex->getMessage());
                    }
                }
                else
                {
                    Session::set_flash('error', 'Invalid username/email entered.');
                }
            }
        }
	}
    
    public function action_reset_password($token = null)
    {                
        $post = Input::post();
        
        if (empty($post))
        {
            if ($token)
            {
                $this->template->title = 'User &raquo; Reset Password';
                $this->template->content = View::forge('user/reset_password');
                $this->template->content->token = $token;
                $this->include_client_scripts('jquery_forms');
            }
            else
            {
                if ($this->user)
                {
                    $this->user->send_reset_password_instructions();
                    Session::set_flash('success', 'An email was sent to you with instructions to reset your password.');
                    Response::redirect('/member/view');
                }
                else
                {
                    Response::redirect('/welcome/404'); //page not found
                }
            }
        }
        else
        {
            $validation = Validation::forge();
            $validation->add('password', 'Password')->add_rule('required')->add_rule('match_pattern', '#^\S{8,}$#');
            $validation->add('password_confirm', 'Confirm Password')->add_rule('required')->add_rule('match_field', 'password');
            $validation->add('token', 'Token')->add_rule('required');

            if ($validation->run())
            {
                $success = false;
                $msg = array();

                try {
                    $user = \Warden\Model_User::reset_password_by_token($validation->validated('token'), $validation->validated('password'));
                    if ($user) {
                        $msg[] = $user->username . ', your password has been changed.';
                        $success = true;
                    } else {
                        $msg[] = 'Invalid token.';
                    }
                } catch (\Warden\Failure $ex) {
                      // token has expired (if enabled)
                    $msg[] = $ex->getMessage();
                } catch (Exception $ex) {
                      // Server/DB error
                    $msg[] = 'Oops, something went wrong.';
                }

                if ($success)
                {
                    $msg[] = 'Please login again with your new password.';
                    \Session::set_flash('success', $msg); 
                    Response::redirect('/user/login');
                }
                else
                {
                    \Session::set_flash('error', $msg);
                    Response::redirect('/welcome');
                }  
            }
            else
            {
                //in case client-side validation didn't run, server-side validation will fail as well, so display the errors if that happens
                $errors = $validation->error();
                $error_messages = array();

                foreach ($errors as $field=>$error)
                {
                    switch ($field)
                    {
                        case 'password':
                            $error_messages[] = 'Your password must contain at least 8 characters (case-sensitive, no spaces)';
                            break;

                        default:
                            $error_messages[] = $error->get_message();
                    }        
                }

                Session::set_flash('error', $error_messages);
            }
        }
    }
}
