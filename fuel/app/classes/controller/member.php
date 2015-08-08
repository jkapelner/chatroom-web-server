<?php


class Controller_Member extends Controller_User
{
    public function before()
    {
        parent::before();
        
        try 
        {
            if (!$this->user)
            {
                \Session::set_flash('error', "Please login to access this page.");
                //non logged in users must login first
                $destination = Uri::create(Input::uri());
                Response::redirect(Uri::create('user/login', array(), array('destination' => $destination))); //require login first  
            }
        }
        catch (Exception $e)
        {
            \Session::set_flash('error', $e->getMessage());
            \Response::redirect('/welcome/404');
        } 
    }
    
    public function action_view($user_id = null)
    {
        if (empty($user_id))
            $user_id = $this->user->id;
        
        try 
        {
            $this->include_client_scripts('jquery_forms');
            $this->template->content = \View::forge('member/account');
            $this->template->content->user = ($this->user->id == $user_id) ? $this->user : \Warden\Model_User::authenticate($user_id, true/*force*/);
            
            if (!$this->template->content->user) {
                \Session::set_flash('error', "User '$user_id' wasn't found in our system.");
                \Response::redirect('/welcome/404');             
            }
            
            $this->template->content->editable = (($this->user->id == $user_id) && \Access::can('edit_own_account', $this->user)) || \Access::can('edit_any_account', $this->user);
            $this->template->content->can_unlock = $this->template->content->user->is_access_locked() && \Access::can('unlock_any_user', $this->user);
            $this->template->content->title = ($this->user->id == $user_id) ? 'My Account' : $this->template->content->user->username;
            $this->template->title = $this->template->content->title;
            
            if (\Access::can('assign_roles', $this->user)) 
            {
                $result = \Warden\Model_Role::find('all');
                $this->template->content->roles = array();

                foreach ($result as $row) {
                    $this->template->content->roles[$row['id']] = $row['name'];
                }
            }
        }
        catch (Exception $e)
        {
            \Session::set_flash('error', $e->getMessage());
            \Response::redirect('/welcome/404');
        }       
    }
	
	public function action_chat()
	{
		$this->template->content = \View::forge('member/chat');
		$config = Config::get('db.nodejs.chat', array());
		$host = empty($config['host']) ? Config::get('base_url') : $config['host'];
		$port = empty($config['port']) ? 0 : (integer)$config['port'];
		$this->template->content->chat_url = $port ? http_build_url('/', array('host' => $host, 'port' => $port)) : $host;
	}
    
	public function action_blogs($user_id = null)
	{
        if (empty($user_id))
            $user_id = $this->user->id;
			
        $blogView = $this->load_blog_view($user_id);
        
        if (empty($blogView)) {
            Response::redirect('/welcome/404');
        }
        
        $this->template->content = $blogView;
        $this->template->title = 'My Blogs';
	}
	
    public function action_account($user_id = null)
    {
        if (empty($user_id))
            $user_id = $this->user->id;
        
        if ( (($this->user->id != $user_id) && !\Access::can('edit_any_account', $this->user)) || !\Access::can('edit_own_account', $this->user))
        {
            //user must either be editing their own profile, or have special privileges to edit someone else's
            \Response::redirect('/welcome/404');
        }

        $post = \Input::post();
        
        if (!empty($post))
        {   
            //add server-side validation to the form fields (because you can't always rely on the client)
            $validation = Validation::forge();
            $validation->add('username', 'Username')->add_rule('required')->add_rule('match_pattern', '#^[A-Za-z0-9]{6,}$#');
            $validation->add('password', 'Password')->add_rule('required')->add_rule('match_pattern', '#^\S{8,}$#');
            $validation->add('password_confirm', 'Confirm Password')->add_rule('required')->add_rule('match_field', 'password');

            if ($validation->run())
            {
                //if server-side validation passed
                try {
                    $user = ($user_id == $this->user->id) ? $this->user : \Warden\Model_User::find($user_id);
                    $user->username = $validation->input('username');
                    $user->password = $validation->input('password');
                    $user->save();
                    Session::set_flash('success', 'Successfully saved changes.');
                } catch (\Orm\ValidationFailed $ex) {
                    Session::set_flash('error', $ex->getMessage());
                } catch (Exception $ex) {
                    $msg = $ex->getMessage(); 
                    Session::set_flash('error', $msg ? $msg : 'Oops, something went wrong.');
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

                        default:
                            $error_messages[] = $error->get_message();
                    }        
                }

                Session::set_flash('error', $error_messages);
            }
        }
        
        \Response::redirect(Uri::create('/member/view/' . $user_id, array(), array(), false /* logged in, so force http as we don't need ssl */));   
    }
       
    public function action_roles($user_id = null)
    {
        if (empty($user_id))
            $user_id = $this->user->id;
        
        if (!\Access::can('assign_roles', $this->user))
        {
            //user must either be editing their own account, or have special privileges to edit someone else's
            \Response::redirect('/welcome/404');
        }

        $post = \Input::post();
        
        if (!empty($post))
        {
            $roles = empty($post['roles']) ? array() : $post['roles'];
                        
            try {
                //load the user, assign the new roles and save
                $user = ($user_id == $this->user->id) ? $this->user : \Warden\Model_User::find($user_id);
                \Access::set_roles($roles, $user);
                $user->save();

                Session::set_flash('success', 'Successfully saved changes.');
            } catch (\MongoOrm\ValidationFailed $ex) {
                Session::set_flash('error', $ex->getMessage());
            } catch (Exception $ex) {
                $msg = $ex->getMessage(); 
                Session::set_flash('error', $msg ? $msg : 'Oops, something went wrong.');
            }

        }
        
        \Response::redirect('/member/view/' . $user_id);
    }

    public function action_unlock()
    {
        if (!\Access::can('unlock_any_user', $this->user))
        {
            //user must either be editing their own account, or have special privileges to edit someone else's
            \Response::redirect('/welcome/404');
        }

        $post = \Input::post();
        
        if (empty($post) || empty($post['user_id']))
        {
            //user_id of user to unlock must be posted
            \Response::redirect('/welcome/404');
        }
      
        $user_id = $post['user_id'];

        try {
            //load the user, assign the new roles and save
            $user = ($user_id == $this->user->id) ? $this->user : \Warden\Model_User::find($user_id);
            
            if (!$user->is_access_locked()) {
                throw new Exception('User is not locked.');
            }
            
            $user->unlock_access(true/*save*/);
            
            Session::set_flash('success', 'User is unlocked.');
        } catch (\MongoOrm\ValidationFailed $ex) {
            Session::set_flash('error', $ex->getMessage());
        } catch (Exception $ex) {
            $msg = $ex->getMessage(); 
            Session::set_flash('error', $msg ? $msg : 'Oops, something went wrong.');
        }
        
        \Response::redirect('/member/view/' . $user_id);
    }

}
