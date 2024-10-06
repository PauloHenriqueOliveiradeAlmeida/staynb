<?php

namespace App\Api\Shared\Guards;

use App\Api\Modules\Collaborator\Entity\CollaboratorEntity;
use App\Api\Shared\Guards\Dtos\TokenPayloadDto;
use App\Api\Shared\Guards\Enums\UserLevelEnum;
use App\Api\Shared\Services\Token\TokenService;
use Raven\Falcon\Http\Exceptions\UnauthorizedException;
use Raven\Falcon\Http\Request;
use Raven\Falcon\Attributes\Middlewares\Guard\IGuard;

class UserGuard implements IGuard
{

	public function __construct(
		private readonly UserLevelEnum $userLevel,
		private readonly CollaboratorEntity $collaboratorEntity = new CollaboratorEntity,
		private readonly TokenService $tokenService = new TokenService
	) {}

	public function verify(Request $request): bool
	{
		$authorizationToken = explode(' ', $request->headers->authorization)[1];
		if (!$authorizationToken)
			throw new UnauthorizedException('Token não fornecido');

		/** @var TokenPayloadDto $payload */
		$payload = $this->tokenService->getPayload($authorizationToken, getenv("JWT_SECRET"));

		$user = $this->collaboratorEntity->selectById($payload->id);
		if (!$user) throw new UnauthorizedException('Token inválido');

		if ($this->userLevel === UserLevelEnum::ALL) return true;
		return $this->userLevel->value === (int) $user->is_admin;
	}
}
