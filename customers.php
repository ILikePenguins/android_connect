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
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("INSERT INTO customers (name, event_id) VALUES(:name, :event_id)");
		$sth->execute(array(':name' => $_POST['name'], ':event_id' => $_POST['event_id']));
		
		return generate_response(STATUS_SUCCESS, "Customer added successfully");
	}

	// Gets list of customers at an event
	function retrieveCustomersByEvent() {
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("	SELECT c.*
								FROM events_customers as ec
								INNER JOIN customers as c ON c.id=ec.customer_id
								WHERE ec.event_id=:event_id");

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