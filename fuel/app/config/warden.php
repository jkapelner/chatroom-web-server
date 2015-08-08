<?php
return array (
  'default_role' => 'standard',
  'password' => 
  array (
    'validate' => true,
    'min_length' => 6,
    'max_length' => 32,
  ),
  'rememberable' => 
  array (
    'in_use' => true,
    'key' => 'RememberMeToken',
    'ttl' => 1209600,
  ),
  'profilable' => true,
  'trackable' => true,
  'recoverable' => 
  array (
    'in_use' => true,
    'reset_password_within' => '+1 day',
    'url' => 'user/reset_password',
  ),
  'confirmable' => 
  array (
    'in_use' => true,
    'confirm_within' => '+1 day',
    'url' => 'user/confirm',
  ),
  'lockable' => 
  array (
    'in_use' => true,
    'maximum_attempts' => 10,
    'lock_strategy' => 'failed_attempts',
    'unlock_strategy' => 'both',
    'unlock_in' => '+1 day',
    'url' => 'user/unlock',
  ),
  'http_authenticatable' => 
  array (
    'in_use' => false,
    'method' => 'digest',
    'realm' => 'Protected by Warden',
    'users' => 
    array (
    ),
  ),
);
