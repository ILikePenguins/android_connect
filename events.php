<?php
require_once('BaseController.php');

class Events extends BaseController
{
	protected $required_fields = array
	(
		'name','date'
	);

	function create() 
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("INSERT INTO events (name, date) VALUES(:name, :date)");
		$sth->execute(array(':name' => $_POST['name'], ':date' => $_POST['date']));
		
		return generate_response(STATUS_SUCCESS, "event successfully added");
	}

	function retrieve()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT name,date FROM events");
		$sth->execute();
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	function getEventID()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("SELECT id FROM events WHERE name=:name
							 AND date=:date ");
		$sth->execute(array(':name' => $_POST['name'], ':date' => $_POST['date']));
		$result = $sth->fetchColumn();
		return $result;
	}
	function deleteEvent()
	{
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("DELETE FROM events WHERE name=:name
							 AND date=:date ");
		$sth->execute(array(':name' => $_POST['name'], ':date' => $_POST['date']));
		return generate_response(STATUS_SUCCESS, "event successfully deleted");
	}

	// Adds a customer to an event
	function addCustomer() {
		$dbh = PDOManager::getPDO();
		$sth = $dbh->prepare("INSERT INTO events_customers (event_id, customer_id) VALUES(:event_id, :customer_id)");
		$sth->execute(array(':event_id' => $_POST['event_id'], ':customer_id' => $_POST['customer_id']));
		
		return generate_response(STATUS_SUCCESS, "Customer successfully added to event");
	}

	function delete()
	 {
		return 'delete';
	}
}