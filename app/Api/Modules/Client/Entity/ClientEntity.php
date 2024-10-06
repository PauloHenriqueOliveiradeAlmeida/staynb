<?php

namespace App\Api\Modules\Client\Entity;

use App\Api\Modules\Client\Dtos\ClientDto;
use App\Api\Shared\Database\Connection;

class ClientEntity
{
	public function __construct(
		private readonly Connection $connection = new Connection()
	) {}

	public function create(ClientDto $clientDto)
	{
		return $this->connection->query(
			"INSERT INTO clients (name, CPF, phone_number, email) VALUES (:name, :cpf, :phone_number, :email)",
			(array) $clientDto
		);
	}

	public function selectAll()
	{
		return $this->connection->query(
			"SELECT id, name, email, phone_number, CPF FROM clients"
		);
	}

	public function selectById(int $id)
	{
		return $this->connection->query("SELECT name, email, phone_number, CPF FROM clients WHERE id = :id", ['id' => $id]);
	}

	public function delete(int $id)
	{
		return $this->connection->query("DELETE FROM clients WHERE id = :id", ['id' => $id]);
	}

	public function update(int $id, ClientDto $clientDto)
	{
		return	$this->connection->query(
			"UPDATE clients SET name = :name, CPF = :cpf, phone_number = :phone_number, email = :email WHERE id = :id",
			[
				...(array) $clientDto,
				'id' => $id
			]
		);
	}
}
