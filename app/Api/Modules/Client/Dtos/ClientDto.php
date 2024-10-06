<?php

namespace App\Api\Modules\Client\Dtos;

use App\Api\Shared\Decorators\Validators\IsCPF;
use Raven\Cassowary\Validators\IsEmail;
use Raven\Cassowary\Validators\IsRequired;
use Raven\Cassowary\Validators\IsString;
use Raven\Cassowary\Validators\Length;

class ClientDto
{
	#[IsRequired]
	#[IsString]
	#[Length(min: 3, max: 100)]
	public string $name;

	#[IsRequired]
	#[IsString]
	#[IsCPF]
	public string $cpf;

	#[IsRequired]
	#[IsString]
	#[Length(min: 3, max: 150)]
	#[IsEmail]
	public string $email;

	#[IsRequired]
	#[IsString]
	#[Length(min: 10, max: 11)]
	public string $phone_number;
}
