<?php

namespace App\Api\Shared\Services\Mailer\Dtos;

use Raven\Cassowary\Validators\IsEmail;
use Raven\Cassowary\Validators\IsRequired;
use Raven\Cassowary\Validators\IsString;
use Raven\Cassowary\Validators\Length;

class SendConversationInviteDto
{
	#[IsRequired]
	#[IsString]
	#[Length(min: 3, max: 150)]
	public string $fullName;

	#[IsRequired]
	#[IsString]
	#[IsEmail]
	#[Length(min: 10, max: 200)]
	public string $email;

	#[IsRequired]
	#[IsString]
	#[Length(min: 7, max: 9)]
	public string $clientType;

	#[IsRequired]
	#[IsString]
	#[Length(min: 10, max: 200)]
	public string $message;
}
