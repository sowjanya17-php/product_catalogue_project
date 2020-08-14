<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
	/*
	The below test is to test the functionality of listing the products  for User Role
	*/
	public function testListProduct()
	{
		$client = static ::createClient();
		$client->request('GET', '/product/list');
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}

	/*
	The below test is to test the functionality of listing the products for Admin Role
	*/

	public function testAdminListProduct()
	{
		$client = static ::createClient();
		$client->request('GET', '/product/admin/list');
		$this->assertEquals(200, $client->getResponse()->getStatusCode());
	}

	
}
?>
