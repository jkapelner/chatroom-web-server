<?php

class Controller_Ajax_Blog extends Controller_Ajax
{ 
    public function action_index($blog_id = null)
    {
        $result = false;
        $limit = min((integer)\Input::param('limit', BLOG_DISPLAY_LIMIT), BLOG_DISPLAY_LIMIT);
        $offset = (integer)\Input::param('offset', 0);
        $page = (integer)\Input::param('page', 0);
        $user_id = \Input::param('user_id', null);
        
        if ($page > 0) {
            $limit = BLOG_DISPLAY_LIMIT;
            $offset = BLOG_DISPLAY_LIMIT * ($page - 1);
        }
                
        try
        {
            $result = array_merge(array('status' => 1), Model_Project::load($blog_id, $user_id, $offset ? false : true/*only get the count for the 1st page of data*/, $offset, $limit));
        }
        catch (Exception $e)
        {
            $result = array('status' => 0, 'error' => $e->getMessage());
        }
        
        return $this->response($result);           
    }
        
}
