<?php

require_once "configuration/connection.php";

class Collaborator
{
	public readonly string $name;
	public readonly string $CPF;
	public readonly string $phone_number;
	public readonly string $email;
	private readonly string $password;
	private readonly bool $is_admin;


	public function __construct(?string $name = null, ?string $CPF = null, ?string $phone_number = null, ?string $email = null)
	{
		$this->name = $name;
		$this->CPF = $CPF;
		$this->phone_number = $phone_number;
		$this->email = $email;
	}

	public function setPermission($is_admin)
	{
		$this->is_admin = $is_admin;
	}
	public function setPassword($password)
	{
		$this->password = password_hash($password, PASSWORD_DEFAULT, ["cost" => 15]);
	}

	public function create()
	{
		$connection = new Connection();

		$query = $connection->queryDB(
			"INSERT INTO collaborators (name, CPF, phone_number, email, is_admin, password) VALUES (?, ?, ?, ?, ?, ?)",
			[
				$this->name,
				$this->CPF,
				$this->phone_number,
				$this->email,
				$this->is_admin,
				$this->password
			]
		);
		return $query;
	}

	public function selectAll()
	{
		$connection = new Connection();
		$query = $connection->queryDB("SELECT id, name, email, phone_number, CPF FROM collaborators");
		return $query->fetch_all(MYSQLI_ASSOC);
	}
}