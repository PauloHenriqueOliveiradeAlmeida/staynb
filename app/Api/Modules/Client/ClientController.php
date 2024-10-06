<?php

namespace App\Api\Modules\Client;

use App\Api\Modules\Client\Dtos\ClientDto;
use App\Api\Shared\Guards\Enums\UserLevelEnum;
use App\Api\Shared\Guards\UserGuard;
use App\Api\Shared\Services\Mailer\Dtos\SendConversationInviteDto;
use App\Api\Shared\Services\Mailer\Gateways\MailerSendGateway;
use Raven\Falcon\Attributes\Controller;
use Raven\Falcon\Attributes\HttpMethods\Delete;
use Raven\Falcon\Attributes\HttpMethods\Get;
use Raven\Falcon\Attributes\HttpMethods\Post;
use Raven\Falcon\Attributes\HttpMethods\Put;
use Raven\Falcon\Attributes\Middlewares\Guard\UseGuard;
use Raven\Falcon\Attributes\Request\Body;
use Raven\Falcon\Attributes\Request\Param;

#[Controller(endpoint: 'clients')]
class ClientController
{

	public function __construct(
		private readonly ClientService $clientService = new ClientService(new MailerSendGateway)
	) {}

	#[Post]
	#[UseGuard(new UserGuard(UserLevelEnum::ADMIN))]
	public function create(#[Body] ClientDto $clientDto)
	{
		return $this->clientService->create($clientDto);
	}

	#[Put(endpoint: ':id')]
	#[UseGuard(new UserGuard(UserLevelEnum::ADMIN))]
	public function update(#[Body] ClientDto $clientDto, #[Param(paramName: 'id')] int $id)
	{
		return $this->clientService->update($id, $clientDto);
	}

	#[Get]
	#[UseGuard(new UserGuard(UserLevelEnum::ALL))]
	public function getAll()
	{
		return $this->clientService->getAll();
	}

	#[Get(endpoint: ':id')]
	#[UseGuard(new UserGuard(UserLevelEnum::ALL))]
	public function getOne(#[Param(paramName: 'id')] int $id)
	{
		return $this->clientService->getById($id);
	}

	#[Delete(endpoint: ':id')]
	#[UseGuard(new UserGuard(UserLevelEnum::ADMIN))]
	public function delete(#[Param(paramName: 'id')] int $id)
	{
		return $this->clientService->delete($id);
	}

	#[Post(endpoint: 'send-conversation-invite-email')]
	public function sendConversationInviteEmail(#[Body] SendConversationInviteDto $sendConversationInviteDto)
	{
		return $this->clientService->sendConversationInviteEmail($sendConversationInviteDto);
	}
}
