# product_catalogue_project



The "Symfony Product Catlog Application" is used to create and show product catalog which includes,Only  authorised ROLE_ADMIN(admin) can create the product,ROLE_USER can view the product.
ROLE_USER can add comments to the product.
ROLE_ADMIN can  Approve/Reject/Delete  the Comment using authorised ROLE_ADMIN for the selected product. 
view application in locale language English,Uk, User management with two roles ROLE_ADMIN and ROLE_USER,
I have used datafixtures to create the users in application.

Data Fixtures
---------------

Use the below command to add users to DB

	php bin/console doctrine:fixtures:load
 
The user passwords are stored in encrypted format only.
 
 
 Use the below  user credentials to login in to product_catlog_system 
 
 Admin_ROle
 ---------------
 User name : admin@gmail.com
 Password : admin@123$
 
 User _Role
 -----------------
 User name : user1@gmail.com
 Password : user1@123$
 
Admin Role Functionalities:
----------------------------

When we login as admin he will be redirected to Product List Page.clicking on the view detials will take you to product details page. where he can approve/reject/delete the comment added by the User_Role user .Upon admin approval only comments are visible to  frontend user.


Admin panel -> Admin can view authorised(his own) product listing page. Admin can add product. Admin can edit.
 
 
User Role Functionalities:
----------------------------

When we login as  user he will be redirected to  product List Page.Clicking on view details link will take you to product details page, where user can view admin approved comments if any for the product. can publish the comment for the product.

Requirements
-----------------
PHP 7.2.9 or higher;
PDO-mysql PHP extension enabled;
and the [usual Symfony application requirements]
Installation

Installation
------------------
[Download Symfony] to install the symfony binary on your computer and run this command:

you can use Composer:

$ composer create-project symfony/website-skeleton symfony_proj_files

OR 

$ git clone https://github.com/sowjanya17-php/symfony_proj_files.git
$ cd symfony_proj_files
$ composer update

Database
----------------

Using symfony commands::

PHP BIN/CONSOLE DOCTRINE:DATABASE:CREATE

PHP BIN/CONSOLE DOCTRINE:MIGRATIONS:MIGRATE 

To load dummy data into database,

PHP BIN/CONSOLE DOCTRINE:FIXTURES:LOAD


Tests
---------

Execute this command to run tests:

 php bin/phpunit tests/Util/ProductControllerTest.php

-
 
 
 
 
 
