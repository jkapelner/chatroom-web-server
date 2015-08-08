<?php


class Observer_Blog extends Orm\Observer
{    
    public function before_save(Orm\Model &$model)
    {
        $model->title = trim($model->title);
    }
}

class Model_Blog extends \Orm\Model
{
    protected static $_table_name = 'blogs';
    protected static $_primary_key = array('id');

	protected static $_properties = array(
        'id',
        'user_id',
        'title' => array(
            'validation' => array(
                'required',
            )
        ),
        'post' => array(
            'validation' => array(
                'required',
            )
        ),
        'publish_flag',
        'public_flag',
        'created_at',
        'updated_at',
	);
      
	protected static $_observers = array(
        'blog',
        'Orm\\Observer_Validation',
        'Orm\\Observer_CreatedAt' => array(
            'events' => array('before_insert'),
            'mysql_timestamp' => true,
            'overwrite' => true,
        ),
        'Orm\\Observer_UpdatedAt' => array(
            'events' => array('before_save'),
            'mysql_timestamp' => true,
        ),
	);

    public static function load($blog_id = null, $user_id = null, $include_count = true, $offset = 0, $limit = BLOG_DISPLAY_LIMIT)
    {
        $filters = array();
        $current_user = \Warden::current_user();
        $result = array('data' => array(), 'count' => 0);
        
        if (empty($blog_id))
        {
            if (empty($user_id)) {
                $filters['public_flag'] = true; //only load public blogs if not loading a specific user's blog
                
                if (!$current_user || !\Access::can('publicize_any_blog', $current_user)) {
                    $filters['publish_flag'] = true; //only load published blogs if the user is not privileged to publicize any blog
                }
            }
            else {         
                if (!$current_user || ($user_id != $current_user->id)) {
                    $filters['publish_flag'] = true; //only load published blogs if the user is loading a blog that's not their own
                }

                $filters['user_id'] = $user_id; //load blogs for the specified user only
            }
            
            $result['data'] = static::get_where($filters, $offset, $limit);
        }
        else //unique id is passed, so get the one result by id
        {
            $result['data'] = static::get_where(array('id' => $blog_id, 0, 1));
        }
		
		if (!empty($result['data']))
		{            
			$result['count'] = $include_count ? static::get_count($filters) : count($result['data']);
		}
                   
        return $result;
    }
    
    public static function get_count($filters = null)
    {
        $where = static::get_where_clause($filters);
        
        return static::count(empty($where) ? array() : array('where' => $where));
    }
    
    public static function get_where($filters, $offset = 0, $limit = BLOG_DISPLAY_LIMIT)
    {
        $where = static::get_where_clause($filters);
        $table = static::table();
		
        if (!empty($where)) {
            $options['where'] = $where;
        }
        
        return \DB::select($table . '.*', array('users.username', 'author'))
			->from($table)
			->join('users')
			->on('users.id', '=', $table . '.user_id')
			->where($where)
			->order_by('updated_at', 'desc')
			->offset($offset)
			->limit($limit)
			->execute();
    }
        
    private static function get_where_clause($filters = null)
    {
        $where = array();
        
        if (is_array($filters)) 
        {
            if (isset($filters['user_id'])) {
                $where['user_id'] = (integer)($filters['user_id']);
            }
            
            if (isset($filters['publish_flag'])) {
                $where['publish_flag'] = (integer)$filters['publish_flag'];
            }
            
            if (isset($filters['public_flag'])) {
                $where['public_flag'] = (integer)$filters['public_flag'];
            }
			
            if (isset($filters['id'])) {
                $where['id'] = (integer)$filters['id'];
            }
        }
        
        return $where;
    }

}
