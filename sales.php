<?php
require_once('BaseController.php');
require_once('Beer.php');
class Sales extends BaseController
{
	protected $required_fields = array
	(
		'name','event_id'
	);

	// All information about all sales... EVER
	function retrieve()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT * FROM sales");
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	// All information about everything a customer purchased at an event
	function retrieveSalesForCustomerByEvent()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT * FROM sales WHERE customer_id=:customer_id");
		$sth->execute(array(':customer_id' => $POST_['customer_id'], ':event_id' => $POST_['event_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	// Essentially, a sum of the cost of the results of retrieveSalesForCustomer()
	function getTotalForCustomerByEvent()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT SUM(s.cost_total) FROM sales AS s GROUP BY s.customer_id WHERE s.customer_id=:customer_id AND s.event_id=:event_id");
		$sth->execute(array(':event_id' => $_POST['event_id'], ':customer_id' => $_POST['customer_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}

	function retrieveCustomersByEvent()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT c.name FROM sales AS s INNER JOIN customers AS c ON s.customer_id = c.id WHERE s.event_id=:event_id");
		$sth->execute(array(':event_id' => $_POST['event_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}

	// Get the total sales amount for an event
	function getTotalForEvent()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT SUM(s.cost_total) FROM sales AS s GROUP BY s.event_id WHERE s.event_id=:event_id");
		$sth->execute(array(':event_id' => $_POST['event_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}

	// Sell some shit. Yeah.
	function newSale() 
	{
		$dbh = PDOManager::getPDO();

	
		$beerController = new Beer();
		$product_info = $beerController->retrieveProductInfo();

		$sth = $dbh->prepare("INSERT INTO sales (beer_id, event_id, customer_id, type, quantity, cost_each, cost_total)
							 VALUES (:beer_id, :event_id, :customer_id, :type, :quantity, :cost_each, :cost_total)");
		$sth->execute(array(
			'beer_id' => $product_info['beer_id'],
			'event_id' => $_POST['event_id'],
			'customer_id' => $_POST['customer_id'],
			'type' => $product_info['type'],
			'quantity' => $_POST['quantity'],
			'cost_each' => $product_info['cost_each'],
			'cost_total' => $product_info['quantity'] * $product_info['cost_each'],
		));
		return $dbh->lastInsertId();
	}

	// Update the quantity for a particular sale (bottle or pint)
	// Requires sale ID
	function updateSaleQty()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("UPDATE sales SET quantity=:quantity WHERE id=:sale_id");
		$sth->execute(array(':sale_id' => $_POST['sale_id'], ':quantity' => $_POST['quantity']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	// Update the quantity for a particular sale (bottle or pint)
	// Requires sale ID
	function incrementSaleQty()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("UPDATE sales SET quantity=:quantity WHERE id=:sale_id");
		$sth->execute(array(':sale_id' => $_POST['sale_id'], ':quantity' => $_POST['quantity']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	function getBottlesPurchasedByCustomer()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("  SELECT SUM(s.quantity), SUM(s.cost_total)
								FROM sales AS s 
								WHERE s.event_id=:event_id AND s.type=:type
								GROUP BY s.customer_id");
		$sth->execute(array(':event_id' => $_POST['event_id'], ':type' => TYPE_BOTTLE));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}


	// Returns each sale row for a customer given event_id, customer_id
	function getSalesForCustomer() 
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT s.* 
							FROM sales AS s 
							WHERE s.event_id=:event_id AND s.customer_id=:customer_id");
		$sth->execute(array(':event_id' => $_POST['event_id'], ':customer_id' => $_POST['customer_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}


	function getPintsPurchasedByCustomer()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT SUM(s.quantity), SUM(s.cost_total)
		 						FROM sales AS s 
		 						WHERE s.event_id=:event_id AND s.type=:type
		 						GROUP BY s.customer_id");
		$sth->execute(array(':event_id' => $_POST['event_id'], ':type' => TYPE_KEG));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}

	function getTotalForCustomer()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT SUM(s.cost_total) FROM sales AS s WHERE s.event_id=:event_id GROUP BY s.customer_id ");
		$sth->execute(array(':event_id' => $_POST['event_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}

	function delete()
	{
		return 'delete';
	}
}