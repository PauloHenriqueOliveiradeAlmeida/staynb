<?php

require_once __DIR__ . "/../../../../shared/database/connection.php";

class Amenity
{
	private readonly string $name;

	public readonly int $id;

	public function __construct(?string $name = '')
	{
		$this->name = $name;
	}

	public function create()
	{
		$connection = new Connection();
		$query = $connection->queryDB(
			"INSERT INTO amenities (name) VALUES (?)",
			[$this->name]
		);
		$this->id = $connection->id_inserted;
		return $query;
	}

	public function selectAll()
	{
		$connection = new Connection();
		$query = $connection->queryDB("SELECT * FROM amenities");

		return $query->fetch_all(MYSQLI_ASSOC) ?? [];
	}

	public function selectByPropertyId(int $property_id)
	{
		$connection = new Connection();
		$query = $connection->queryDB("SELECT * FROM properties_amenities
		JOIN amenities ON properties_amenities.amenityId = amenities.id
		WHERE properties_amenities.propertyId = ?", [$property_id]);

		return $query->fetch_all(MYSQLI_ASSOC) ?? [];
	}

	public static function findByName($name)
	{
		$connection = new Connection();
		$query = $connection->queryDB(
			"SELECT * FROM amenities WHERE name = ?",
			[$name]
		);
		return $query->fetch_assoc();
	}

	public static function addAmenityToProperty($property_id, $amenity_id)
	{
		$connection = new Connection();
		$query = $connection->queryDB(
			"INSERT INTO properties_amenities (propertyId, amenityId) VALUES (?, ?)",
			[$property_id, $amenity_id]
		);
		return $query;
	}

	public function deleteMany(array $ids)
	{
		$connection = new Connection();
		$query = $connection->queryDB(
			"DELETE FROM properties_amenities WHERE propertyId IN (?)",
			[implode(",", $ids)]
		);
		return $query;
	}
}
