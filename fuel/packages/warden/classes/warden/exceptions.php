<?php
/**
 * Warden: User authorization & authentication library for FuelPHP.
 *
 * @package    Warden
 * @subpackage Warden
 * @version    2.0
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  (c) 2011 - 2013 Andrew Wayne
 */
namespace Warden;

/**
 * Warden\Failure
 *
 * @package    Warden
 * @subpackage Warden
 */
class Failure extends \FuelException
{
    private $reason;
    private $user;
    
    public function __construct($lang_key, array $params = array(), $user = null)
    {
        $this->reason = $lang_key;
        $this->user = $user;
        parent::__construct(__("warden.failure.{$lang_key}", $params));
    }
    
    public function reason()
    {
        return $this->reason;
    }
    
    public function get_user()
    {
        return $this->user;
    }
}

/**
 * Warden\AccessDenied
 *
 * Thrown when a user isn't allowed to access a given controller action.
 * This usually happens within a call to Warden::authorize() but can be
 * thrown manually.
 *
 * <code>
 * throw new Warden\AccessDenied('Not authorized!', 'read', 'Article');
 * </code>
 */
class AccessDenied extends \FuelException
{
  public $action;
  public $resource;

  public function __construct($message = null, $action = null, $resource = null)
  {
    $this->action = $action;
    $this->resource = $resource;

    $message || $message = __('warden.unauthorized.default');

    if (empty($message)) {
      $message = 'You are not authorized to access this page.';
    }

    parent::__construct($message);
  }
}
