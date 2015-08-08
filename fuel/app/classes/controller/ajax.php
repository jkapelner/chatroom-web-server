<?php

class Controller_Ajax extends Controller_Rest
{
	public function action_update_agency_viewcount($comid)
	{
        $result = false;
        
        if (!empty($comid))
        {
            try
            {
                $result = Model_Agency::update_viewcount($comid);
            }
            catch (Exception $e)
            {

            }
        }
        
        return $this->response($result);
    }
    
    public function action_location($zipcode)
    {
        $result = false;
        
        if (!empty($zipcode))
        {
            try
            {
                $result = ZipCode::find_by_zip($zipcode);
            }
            catch (Exception $e)
            {

            }
        }
        
        return $this->response($result);        
    }
}
