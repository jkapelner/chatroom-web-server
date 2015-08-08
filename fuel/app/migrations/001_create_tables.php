<?php

namespace Fuel\Migrations;

class Create_tables
{
	public function up()
	{
		//populate the system roles if they don't exist
		if (\DBUtil::table_exists('roles'))
		{
			if (\DB::count_records('roles') == 0)
			{
				$roles = array(
					\Access::ROLE_ADMIN => 'Admin',
					\Access::ROLE_DEVELOPER => 'Developer',
					\Access::ROLE_EDITOR => 'Editor',
					\Access::ROLE_PENDING => 'Pending',
					\Access::ROLE_STANDARD => 'Standard',
					\Access::ROLE_SILVER => 'Silver',
					\Access::ROLE_GOLD => 'Gold',
					\Access::ROLE_DUMMY => 'Dummy',
				);
				
				foreach ($roles as $id=>$role)
				{
					\DB::insert('roles')->set(array('id' => $id, 'name' => strtolower($role), 'Description' => $role))->execute();
				}
				
				\Cli::write("\nPopulated roles.");
			}
		}
		
		//create default admin user if we have no users
		if (\DBUtil::table_exists('users'))
		{
			if (\DB::count_records('users') == 0)
			{
				//create the admin user
				$data = array(
					'username' => \Cli::prompt("Please enter an admin username"),
					'email'    => \Cli::prompt("Please enter an admin email"),
					'password' => \Cli::prompt("Please enter an admin password")
				);

				try {
					$user = new \Warden\Model_User($data);

					if (\Config::get('warden.confirmable.in_use') === true) {
						$user->is_confirmed = true;
					}

					\Access::set_roles(array(\Access::ROLE_STANDARD, \Access::ROLE_ADMIN), $user); //this will assign the roles and save the user
					\Cli::write("\nCreated admin user.");
					\Cli::write(\Cli::color("\nUsername : {$user->username}", 'blue'));
					\Cli::write(\Cli::color("\nEmail    : {$user->email}", 'blue'));
					
				} catch (\Exception $e) {
					\Cli::error("\n:( Failed to create admin user because: " . $e->getMessage());
				}				
			}
		}
		
		//create the blog table if it doesnt exist
        if (!\DBUtil::table_exists('blogs')) 
        {
            \DBUtil::create_table('blogs', array(
                'id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true, 'auto_increment' => true),
                'user_id' => array('constraint' => 11, 'type' => 'int', 'unsigned' => true),
                'title' => array('constraint' => 255, 'type' => 'varchar'),
                'post' => array('type' => 'text'),
                'publish_flag' => array('constraint' => 11, 'type' => 'int', 'default' => 0, 'unsigned' => true),
                'public_flag' => array('constraint' => 11, 'type' => 'int', 'default' => 0, 'unsigned' => true),
                'created_at' => array('type' => 'timestamp', 'default' => \DB::expr('CURRENT_TIMESTAMP')),
                'updated_at' => array('type' => 'timestamp'),
            ), array('id'), true, 'InnoDB');
			
			\DBUtil::create_index('blogs', 'user_id', 'user_id');
        }
        
	}

	public function down()
	{
	}
}