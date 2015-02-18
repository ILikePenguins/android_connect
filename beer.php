<?php
require_once('BaseController.php');

class Beer extends BaseController
{
	protected $required_fields = array
	(
		'name',
		'quantity_keg', 'cost_pint',
		'quantity_bottle', 'cost_bottle','event_id',
	);


	// function getBeerId() ///////// how to get beerid
	// {
	// 	$dbh = PDOManager::getPDO();
	// 	$sth = $dbh->prepare("	SELECT id FROM beers 
	// 							WHERE name=:name 
	// 							AND event_id=:event_id");
	// 	$sth->execute(array(':name' => $_POST['name'],':event_id' => $_POST['event_id']));
	// 	$result = $sth->fetchColumn();
	// 	return $result;
	// }


	function create() 
	{
		// FIXME: Check if the name already exists, and if so, use the existing ID instaed.
		// At the second party with the same beer, we don't want to re-create another beer row if one already exists!
		// Instead, just add to product_info directly re-using the beer id
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("INSERT INTO beers (name) VALUES(:name)");
		$sth->execute(array(':name' => $_POST['name']));
		$beer_id = $dbh->lastInsertId();

		if ($_POST['quantity_keg'] > 0)
		{
			$sth = $dbh->prepare("INSERT INTO product_info (beer_id, event_id, type, quantity, cost_each) VALUES(:beer_id, :event_id, :type, :quantity, :cost_each)");
			$sth->execute(array(':beer_id' => $beer_id, ':event_id' => $_POST['event_id'], ':type' => TYPE_KEG, ':quantity' => $_POST['quantity_keg'] * PINTS_PER_KEG, ':cost_each' => $_POST['cost_pint']));
			$id = $dbh->lastInsertId();
		}

		if ($_POST['quantity_bottle'] > 0)
		{
			$sth = $dbh->prepare("INSERT INTO product_info (beer_id, event_id, type, quantity, cost_each) VALUES(:beer_id, :event_id, :type, :quantity, :cost_each)");
			$sth->execute(array(':beer_id' => $beer_id, ':event_id' => $_POST['event_id'], ':type' => TYPE_BOTTLE, ':quantity' => $_POST['quantity_bottle'], ':cost_each' => $_POST['cost_bottle']));
			$id = $dbh->lastInsertId();
		}
		
		return generate_response(STATUS_SUCCESS, "Beer added successfully");
	}

	function retrieveBottles()
	{
		
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	SELECT b.name, p.*
							 	FROM product_info as p
							  	INNER JOIN beers as b ON b.id=p.beer_id
							  	WHERE p.type=:type and p.event_id=:event_id");

		$sth->execute(array( ':type' => TYPE_BOTTLE,':event_id' => $_POST['event_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	function retrievePints()
	{

		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	SELECT b.name, p.*
								FROM product_info as p
								INNER JOIN beers as b ON b.id=p.beer_id
								WHERE p.type=:type  and p.event_id=:event_id");

		$sth->execute(array(':type' => TYPE_KEG,':event_id' => $_POST['event_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

		function retrieveBottlesAndPints()
	{

		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	SELECT b.name, p.*
								FROM product_info as p
								INNER JOIN beers as b ON b.id=p.beer_id
								WHERE p.event_id=:event_id");

		$sth->execute(array(':event_id' => $_POST['event_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	// Gets the information for a product at an event
	function retrieveProductInfo() 
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	SELECT b.name, p.*
								FROM product_info as p
								INNER JOIN beers as b ON b.id=p.beer_id
								WHERE p.id=:product_id");

		$sth->execute(array(':product_id' => $_POST['product_id']));
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		return $result;
	}


	function updateBottleQuantity()
	{
		//decrement quantity of bottles
		$id=$this->getBeerId();

		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	UPDATE product_info
								SET quantity = quantity-1
								WHERE beer_id=:beer_id
								AND event_id=:event_id ");

		$sth->execute(array(':beer_id' => $_POST['beer_id'], ':event_id' => $_POST['event_id']));
		//$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return generate_response(STATUS_SUCCESS, "Beer has been updated successfully");
	}

	function getCostBeer()
	{
		$id=$this->getBeerId();

		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	SELECT cost_each
								FROM product_info
								WHERE beer_id=:beer_id
								AND event_id=:event_id");

		$sth->execute(array(':beer_id' => $_POST['beer_id'], ':event_id' => $_POST['event_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	function deleteBeer()
	{
		$id=$this->getBeerId();
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	SELECT cost_each
								FROM product_info
								WHERE beer_id=:beer_id
								AND event_id=:event_id");

		$sth->execute(array(':beer_id' => $_POST['beer_id'], ':event_id' => $_POST['event_id']));

	}

	function delete()
	{
		return 'delete';
	}
}