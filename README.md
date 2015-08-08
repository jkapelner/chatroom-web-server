#Chatroom sample application web server

* Version: 1.0

Installation instructions:

1. install php dependencies:
php composer.phar install

2. setup your database configuration settings for your environment (like mysql and redis connection):
copy 'fuel/app/config/db.php' to the folder for your server's environment (default environment is "development" so for that you would copy to 'fuel/spp/config/development/db.php') and override any settings in there

3. create user tables:
php oil r warden help

you will be prompted twice - answer "n" both times:
Create a default user role? [ y, n ]: n
Create an admin user? [ y, n ]: n

4. Create default roles, admin user and blog table:
php oil r migrate

you will be prompted to enter an admin username, email and password.

