<?php

class Controller_Member_Ajax_Blog extends Controller_Member_Ajax
{ 
   
    public function action_edit($blog_id = null)
	{
        $result = array('status' => false, 'error' => array());

        try
        {
            $input = Input::post();
            
            if (empty($input)) {
                throw new Exception('No data was posted');
            }
            
            $blog = null;
            $user_id = empty($input['user_id']) ? 0 : $input['user_id'];
            
            if (empty($blog_id))
            {
                $blog = Model_Blog::forge();
                $blog->user_id = $user_id;
            }
            else
            {
                $blog = Model_Blog::find($blog_id);

                if (!$blog) {
                    throw new Exception('Blog not found.');
                }
                
                if ($user_id != $blog->user_id) {
                    throw new Exception('Wrong user id passed for this blog.');
                }
            }
                
            if (!\Access::can('edit_any_blog', $this->user) && (!\Access::can('edit_own_blog', $this->user)))
            {
                throw new Exception('You are not authorized to edit this blog');
            }
 
            if (!\Access::can('publicize_any_blog', $this->user) && (!\Access::can('publicize_own_blog', $this->user)))
            {
                $input['public_flag'] = $blog->public_flag; //dont allow public flag to change if user doesn't have permission
            }
            elseif (!isset($input['public_flag']))
            {
                $input['public_flag'] = false;
            }
            
            if (!isset($input['publish_flag']))
            {
                $input['publish_flag'] = false;
            }

            $blog->from_array($input); //populate the blog fields from the input  

            try {
                $blog->save();
                $result['status'] = true;
                $result['data'] = $blog->to_array();
            } catch (\Orm\ValidationFailed $ex) {
                $result['error'] = $ex->getMessage();
            } catch (Exception $ex) {
                $msg = $ex->getMessage();
                $result['error'] = $msg ? $msg : 'Oops, something went wrong.';
            }
        }
        catch (Exception $ex)
        {
            $result['error'] = $ex->getMessage();
        }     
                
        return $this->response($result);
    }

    public function action_delete()
	{
        $result = array('status' => false, 'error' => array());

        try
        {
            $input = Input::post();
            
            if (empty($input) || empty($input['id']))
            {
                throw new Exception('You must specify a blog to delete.');
            }

            $blog = Model_Blog::find($input['id']);

            if (!$blog) {
                throw new Exception('Blog not found.');
            }

            if (!\Access::can('delete_any_blog', $this->user) && (!\Access::can('delete_own_blog', $this->user) || !Model_Agency_Contact::is_confirmed($blog->user_id, $this->user)))
            {
                throw new Exception('You are not authorized to delete this blog');
            }
 
            try {
                $blog->delete();
                $result['status'] = true;
            } catch (\Orm\ValidationFailed $ex) {
                $result['error'] = $ex->getMessage();
            } catch (Exception $ex) {
                $msg = $ex->getMessage();
                $result['error'] = $msg ? $msg : 'Oops, something went wrong.';
            }
        }
        catch (Exception $ex)
        {
            $result['error'] = $ex->getMessage();
        }     
                
        return $this->response($result);
    }

}
