<?php
require_once('BaseController.php');
require_once('Beer.php');
class Sales extends BaseController
{
	protected $required_fields = array
	(
		'name','event_id','amount'
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

	function saleIdExists($product_info)
	{
		$dbh = PDOManager::getPDO();
		//$beerController = new Beer();
		//$product_info = $beerController->retrieveProductInfo();

		$sth = $dbh->prepare("SELECT EXISTS
			(SELECT 1 FROM sales AS s WHERE s.beer_id=:beer_id 
				AND s.customer_id=:customer_id AND s.event_id=:event_id) ");
		$sth->execute(array(
			'beer_id' => $product_info['beer_id'],
			'event_id' => $_POST['event_id'],
			'customer_id' => $_POST['customer_id']
			));
		$result = $sth->fetch(PDO::FETCH_NUM);
		if(strcmp($result[0],"1")==0)	
			return true;
	
		else
			return false;
	}

	// Sell some shit. Yeah.
	//TODO***check if sale idfor that customer exists, if not make new one
	//check if entry exists where beer_id=:beer_id and customer_id=:customer_id and event_id=:event_id
	//get that sale id and update isntead of new entry

	// need to then update the quanitty in product_info
	function newSale() 
	{
		$dbh = PDOManager::getPDO();

	
		$beerController = new Beer();
		$product_info = $beerController->retrieveProductInfo();

		if( !$this->saleIdExists($product_info))
		{

			$sth = $dbh->prepare("INSERT INTO sales (beer_id, event_id, customer_id, type, quantity, cost_each, cost_total)
								 VALUES (:beer_id, :event_id, :customer_id, :type, :quantity, :cost_each, :cost_total)");
			$sth->execute(array(
				'beer_id' => $product_info['beer_id'],
				'event_id' => $_POST['event_id'],
				'customer_id' => $_POST['customer_id'],
				'type' => $product_info['type'],
				'quantity' => $_POST['quantity'],
				'cost_each' => $product_info['cost_each'],
				'cost_total' => $_POST['quantity'] * $product_info['cost_each'],
			));
			//update product_info
			$this->updateProductQty();
			return $dbh->lastInsertId();
		}
		else
		{
			$this->updateProductQty();
			return $this->updateSaleQty($product_info);
		}
	}


	function getSaleID($product_info)
	{
		$dbh = PDOManager::getPDO();
		//$beerController = new Beer();
		//$product_info = $beerController->retrieveProductInfo();

		$sth = $dbh->prepare("SELECT s.id
			 	FROM sales AS s WHERE s.beer_id=:beer_id 
				AND s.customer_id=:customer_id AND s.event_id=:event_id ");
		$sth->execute(array(
			'beer_id' => $product_info['beer_id'],
			'event_id' => $_POST['event_id'],
			'customer_id' => $_POST['customer_id']
			));
		$result = $sth->fetch(PDO::FETCH_NUM);
		return $result;
	}

	// Update the quantity for a particular sale (bottle or pint)
	// Requires sale ID
	function updateSaleQty($product_info)
	{
		$saleID=$this->getSaleID($product_info);
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("UPDATE sales SET quantity=:quantity , cost_total= :cost_total WHERE id=:sale_id");
		$sth->execute(array(':sale_id' => $saleID[0], ':quantity' => $_POST['quantity'],'cost_total' => $_POST['quantity'] * $product_info['cost_each']));

		//$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		//TODO check ifupdate was successful
		// $result = $sth->fetch(PDO::FETCH_NUM);
		// if(strcmp($result[0],"1")==0)	
		// 	returngenerate_response(STATUS_SUCCESS, "Beer updated successfully");
	
		// else
			return generate_response(STATUS_SUCCESS, "Beer updated successfully");
		//return 
	}

	function updateProductQty()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("UPDATE product_info SET quantity=:quantity WHERE id=:product_id");
		$sth->execute(array(':product_id' => $_POST['product_id'], ':quantity' => $_POST['amount']));
	
			return generate_response(STATUS_SUCCESS, "Beer updated successfully");
	
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
		$sth = $dbh->prepare("  SELECT SUM(s.quantity), SUM(s.quantity*s.cost_each)
								FROM sales AS s 
								WHERE s.event_id=:event_id AND s.type=:type AND s.customer_id=:customer_id
		 						GROUP BY s.customer_id");
		$sth->execute(array(':event_id' => $_POST['event_id'], ':type' => TYPE_BOTTLE,':customer_id' => $_POST['customer_id']));
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
		$sth = $dbh->prepare("SELECT SUM(s.quantity),SUM(s.quantity)*(s.cost_each)
		 						FROM sales AS s 
		 						WHERE s.event_id=:event_id AND s.type=:type AND s.customer_id=:customer_id
		 						GROUP BY s.customer_id");
		$sth->execute(array(':event_id' => $_POST['event_id'], ':type' => TYPE_KEG,':customer_id' => $_POST['customer_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}

	//need customer id?
	function getTotalForCustomer()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT SUM(s.cost_total) FROM sales AS s 
							WHERE s.event_id=:event_id and customer_id=:customer_id
						 	GROUP BY s.customer_id ");
		$sth->execute(array(':event_id' => $_POST['event_id'],':customer_id' => $_POST['customer_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}


	// get all beers and pints conusumed by customer
	// if beer_id doesnt exist in sales, quantity should be 0
	// 
	function retrieveBottlesAndPintsSales()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	SELECT b.name, p.id, p.beer_id, p.type, p.cost_each, p.event_id,p.quantity AS PQ, COALESCE(s.quantity, 0) AS quantity, s.quantity
								FROM product_info as p
								INNER JOIN beers as b ON b.id=p.beer_id AND p.event_id=:event_id 
								LEFT JOIN sales as s ON s.beer_id=p.beer_id AND s.event_id=:event_id AND s.customer_id=:customer_id");

		$sth->execute(array(':event_id' => $_POST['event_id'], ':customer_id' => $_POST['customer_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
		 
	}
	function delete()
	{
		return 'delete';
	}
}