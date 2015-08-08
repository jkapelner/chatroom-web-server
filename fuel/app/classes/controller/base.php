<?php

abstract class Controller_Base extends Controller_Template
{
    protected $user = null;
    private $client_scripts_included;
    
    public function before()
    {
        parent::before();
		
        // setup login and logout callbacks
        Warden::after_authentication(function($user){            
			Session::set('warden.user.username', $user->username); //add username to the session so it can be used for chat
        });
        
        Warden::before_logout(function($user){
            Session::delete('warden.user.username'); //delete username from the session when we logout
        });

        // Assign current_user to the instance so controllers can use it
        $this->user = Warden::check() ? Warden::current_user() : null;
        // Set a global variable so views can use it
        View::set_global('current_user', $this->user);

        $this->client_scripts_included = array();
        $this->template->scripts = array();
        $this->template->css = array();
        $this->template->metatags = array();
        $this->include_client_scripts();
	}

    public function after($response)
    {
        if (empty($response))
        {
            $response = $this->template;
        }
        
        if (empty($response->title)) {
            $response->title = \Config::get('title_prefix', '');
        }
        else {
            $response->title = \Config::get('title_prefix', '') . ' - ' . $response->title;
        }
                        
        return parent::after($response);
    }
    
    protected function include_client_scripts($scripts = 'default')
    {
        if (empty($scripts))
            return;
        
        if (!is_array($scripts))
            $scripts = array($scripts);
        
        foreach ($scripts as $script)
        {
            if (empty($this->client_scripts_included[$script]))
            {
                switch ($script)
                {
                    case 'default':
						$this->template->scripts[] = $this->create_js_link("//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js");
						$this->template->scripts[] = $this->create_js_link("//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.4/js/bootstrap.min.js");
						$this->template->css[] = $this->create_css_link("//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.4/css/bootstrap.min.css");
						$this->template->css[] = $this->create_css_link(Uri::create('assets/css/style.css'));
                        break;      
                    
                    case 'jquery_forms':
						$this->template->scripts[] = $this->create_js_link("//cdnjs.cloudflare.com/ajax/libs/qtip2/2.2.0/jquery.qtip.min.js");
						$this->template->scripts[] = $this->create_js_link("//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/jquery.validate.min.js");
						$this->template->scripts[] = $this->create_js_link("//cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.14.0/additional-methods.min.js");
                        $this->template->scripts[] = $this->create_js_link("//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js");
                        $this->template->scripts[] = $this->create_js_link(Uri::create('assets/js/jquery-forms-config.js'));
						array_unshift($this->template->css, $this->create_css_link('//cdnjs.cloudflare.com/ajax/libs/qtip2/2.2.0/jquery.qtip.min.css'));
                        array_unshift($this->template->css, $this->create_css_link("//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.css"));                       
                        break;
                }
                
                $this->client_scripts_included[$script] = true; //don't include the same script more than once
            }
        }
    }
    
    protected function create_css_link($url)
    {
        return '<link href="' . $url . '" rel="stylesheet" type="text/css" media="all" />' . "\n";
    }
    
    protected function create_js_link($url)
    {
        return '<script type="text/javascript" src="' . $url . "\"></script>\n";
    }
    
    protected function add_metatag($data)
    {
        if (!empty($data)) {
            $tag = '<meta ';
            
            foreach ($data as $key=>$value) {
                $tag .= $key . '="' . $value . '" ';
            }
            
            $tag .= "/>\n";
            $this->template->metatags[] = $tag;
        }
    }
	
    protected function load_blog_view($user_id, $blog_id = null)
    {
        $blogView = \View::forge('pages/blog');
        $results = Model_Blog::load($blog_id, $user_id);
		$blogView->is_confirmed = $this->user && ($this->user->id == $user_id);
        $blogView->user_id = $this->user ? $this->user->id : $user_id;
        $blogView->blog_id = $blog_id;
        $blogView->blogs = $results['data'];
        $blogView->count = $results['count'];
        $blogView->is_public = empty($user_id) ? true : false;
        $blogView->can_edit_own = $this->user && \Access::can('edit_own_blog', $this->user);
        $blogView->can_edit_any = $this->user && \Access::can('edit_any_blog', $this->user);
        $blogView->can_delete_own = $this->user && \Access::can('delete_own_blog', $this->user);
        $blogView->can_delete_any = $this->user && \Access::can('delete_any_blog', $this->user);
        $blogView->can_make_own_public = $this->user && \Access::can('publicize_own_blog', $this->user);
        $blogView->can_make_any_public = $this->user && \Access::can('publicize_any_blog', $this->user);
        $blogView->addable = empty($blog_id) && $this->user && $blogView->can_edit_own &&
                (($blogView->is_public && $blogView->can_make_own_public) || //privileged user can add to the public blog
                $blogView->is_confirmed); //user can add their own blog
        $blogView->include_edit_form = $blogView->addable || ($blogView->can_edit_any && $blogView->count);
        $blogView->force_public = $blogView->is_public && $blogView->can_make_any_public;
        $blogView->title = null;
        
        if ($blogView->include_edit_form) {
            $this->include_client_scripts(array('jquery_forms'));
        }
        
        return $blogView;
    }
}
