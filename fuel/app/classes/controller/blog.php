<?php

class Controller_Blog extends Controller_Base
{
    public function action_index()
    {
        $this->action_view();
    }
    
    public function action_view($blog_id = null)
    {
        $blogView = $this->load_blog_view(null/*user_id*/, $blog_id);
        
        if (empty($blogView)) {
            Response::redirect('/welcome/404');
        }
        
        $this->template->content = $blogView;
        $this->template->title = 'Blogs';
    }
    
}
