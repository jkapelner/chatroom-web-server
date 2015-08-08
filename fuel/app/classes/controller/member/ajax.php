<?php

abstract class Controller_Member_Ajax extends Controller_Rest
{
    protected $user = null;
    
    public function before()
    {
        parent::before();
        // Assign current_user to the instance so controllers can use it
        $this->user = Warden::check() ? Warden::current_user() : null;
        
        if (!$this->user)
        {
            $this->response(array('status'=> 0, 'error'=> 'Not Authorized'), 401);
        }
    }
    
}
