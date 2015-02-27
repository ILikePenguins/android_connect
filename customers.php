<?php
require_once('BaseController.php');

class Customers extends BaseController
{
	protected $required_fields = array
	(
		'name','event_id'
	);

	function create() 
	{
		//insert into customers
		//get that customer id
		//insert into events_customers
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("INSERT INTO customers (name) VALUES(:name)");
		$sth->execute(array(':name' => $_POST['name']));


		$customer_id = $dbh->lastInsertId();
		$sth = $dbh->prepare("INSERT INTO events_customers (customer_id, event_id) VALUES(:customer_id, :event_id)");
		$sth->execute(array(':customer_id' => $customer_id, ':event_id' => $_POST['event_id']));
		
		return generate_response(STATUS_SUCCESS, "Customer added successfully",$customer_id);
	}

	// Gets list of customers at an event
	function retrieveCustomersByEvent() {
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	SELECT c.*, paid
								FROM events_customers as ec
								INNER JOIN customers as c ON c.id=ec.customer_id
								WHERE ec.event_id=:event_id");

		$sth->execute(array(':event_id' => $_POST['event_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

		//gets list of customers at event with paid status from sals
		function retrieveCustomersByEvent2()
		 {
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	SELECT c.*, s.paid
								FROM events_customers as ec
								INNER JOIN customers as c ON c.id=ec.customer_id
								LEFT JOIN sales as s ON ec.customer_id=s.customer_id 
								WHERE ec.event_id=:event_id
								GROUP BY s.paid");

		$sth->execute(array(':event_id' => $_POST['event_id']));
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	function deleteCustomer()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	DELETE FROM customers
							 	WHERE name=:name AND event_id=:event_id ");
		$sth->execute(array(':name' => $_POST['name'], ':event_id' => $_POST['event_id']));

		return generate_response(STATUS_SUCCESS, "Customer deleted successfully");
	}

	function delete()
	 {
		return 'delete';
	}
}