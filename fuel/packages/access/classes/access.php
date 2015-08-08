<?php

namespace Access;

//site access roles and permissions
class Access
{
    //fixed enums of user roles - values should match id's in the database
    const ROLE_ADMIN        = 1;
    const ROLE_DEVELOPER    = 2;
    const ROLE_EDITOR       = 3;
    const ROLE_PENDING      = 4;
    const ROLE_STANDARD     = 5;
    const ROLE_SILVER       = 6;
    const ROLE_GOLD         = 7;
    const ROLE_DUMMY        = 8;
    
    private static $_permissions = array();
    
    public static function can($permission, $user = null)
    {
        if (empty(self::$_permissions))
        {
            $config = \Config::get('access', array());
            
            if (!empty($config) && !empty($config['permissions']))
                self::$_permissions = $config['permissions'];
        }
        
        if (array_key_exists($permission, self::$_permissions))
        {
            if (self::$_permissions[$permission] == 'all')
                return true;

            if (empty($user))
            {
                $user = \Warden::current_user();
            }
            elseif (!is_object($user))
            {
                $user = \Warden\Model_User::find($user);
            }
        
            if ($user && is_object($user))
            {
                if (!empty($user->roles))
                {
                    foreach ($user->roles as $role)
                    {
                        if (in_array($role['name'], self::$_permissions[$permission]))
                            return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    public static function is_member($role_id, $user = null)
    {
        if (empty($user)) {
            $user = \Warden::current_user();
        }
        else if (!is_object($user)) {
            $user = \Warden\Model_User::find($user);
        }
        
        if ($user && is_object($user))
        {
            if (array_key_exists($role_id, $user->roles))
                return true;
        }
        
        return false;
    }
    
    public static function set_roles($role_ids, $user = null)
    {
        if (empty($user)) {
            $user = \Warden::current_user();
        }
        else if (!is_object($user)) {
            $user = \Warden\Model_User::find($user);
        }

        if (!$user || !is_object($user)) 
            throw new \Exception("Cannot set roles for a user that doesn't exist");
        
        try
        {
            $user->roles = array();
            
            if (is_array($role_ids))
            {
                foreach ($role_ids as $role_id)
                {
                    $role = \Warden\Model_Role::find($role_id);
                    $user->roles[$role_id] = $role;                    
                }
            }
            
            $user->save();
        }
        catch (\Exception $e)
        {
            throw $e;
        }        
    }
    
    public static function assign_role($role_id, $user = null)
    {
        if (empty($user)) {
            $user = \Warden::current_user();
        }
        else if (!is_object($user)) {
            $user = \Warden\Model_User::find($user);
        }

        if (!$user || !is_object($user)) 
            throw new \Exception("Cannot assign role to a user that doesn't exist");
        
        try
        {
            $role = \Warden\Model_Role::find($role_id);
            $user->roles[$role_id] = $role;
            $user->save();
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }
    
    public static function unassign_role($role_id, $user = null)
    {
        if (empty($user)) {
            $user = \Warden::current_user();
        }
        else if (!is_object($user)) {
            $user = \Warden\Model_User::find($user);
        }

        if (!$user || !is_object($user)) 
            throw new \Exception("Cannot assign role to a user that doesn't exist");
        
        try
        {
            if (isset($user->roles[$role_id]))
            {
                unset($user->roles[$role_id]);
                $user->save();
            }
        }
        catch (\Exception $e)
        {
            throw $e;
        }
    }
}
