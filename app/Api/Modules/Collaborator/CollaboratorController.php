<?php

namespace App\Api\Modules\Collaborator;

use App\Api\Modules\Collaborator\CollaboratorService;
use App\Api\Modules\Collaborator\Dtos\CollaboratorDto;
use App\Api\Shared\Services\Mailer\Gateways\MailerSendGateway;
use Raven\Falcon\Attributes\Controller;
use Raven\Falcon\Attributes\HttpMethods\Delete;
use Raven\Falcon\Attributes\HttpMethods\Get;
use Raven\Falcon\Attributes\HttpMethods\Post;
use Raven\Falcon\Attributes\HttpMethods\Put;
use Raven\Falcon\Attributes\Request\Body;
use Raven\Falcon\Attributes\Request\Param;

#[Controller(endpoint: 'collaborators')]
class CollaboratorController
{

	public function __construct(
		private readonly CollaboratorService $collaboratorService = new CollaboratorService(new MailerSendGateway)
	) {}

	#[Post]
	public function create(#[Body] CollaboratorDto $collaboratorDto)
	{
		return $this->collaboratorService->create($collaboratorDto);
	}

	#[Put(endpoint: ':id')]
	public function update(#[Body] CollaboratorDto $collaboratorDto, #[Param(paramName: 'id')] int $id)
	{
		return $this->collaboratorService->update($id, $collaboratorDto);
	}

	#[Get]
	public function getAll()
	{
		return $this->collaboratorService->getAll();
	}

	#[Get(endpoint: ':id')]
	public function getOne(#[Param(paramName: 'id')] int $id)
	{
		return $this->collaboratorService->getById($id);
	}

	#[Delete(endpoint: ':id')]
	public function delete(#[Param(paramName: 'id')] int $id)
	{
		return $this->collaboratorService->delete($id);
	}
}