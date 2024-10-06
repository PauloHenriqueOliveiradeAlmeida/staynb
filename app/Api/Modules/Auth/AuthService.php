<?php

namespace App\Api\Modules\Auth;

use App\Api\Modules\Auth\Dtos\FirstAccessDto;
use App\Api\Modules\Auth\Dtos\LoginDto;
use App\Api\Modules\Auth\Dtos\ResetPasswordDto;
use App\Api\Modules\Auth\Dtos\SendEmailDto;
use App\Api\Modules\Auth\Entity\UserCodeEntity;
use App\Api\Modules\Collaborator\Entity\CollaboratorEntity;
use App\Api\Shared\Services\Code\CodeService;
use App\Api\Shared\Services\Hash\HashService;
use App\Api\Shared\Services\Mailer\IMailer;
use App\Api\Shared\Services\Mailer\MailerService;
use App\Api\Shared\Services\Token\TokenService;
use PDOException;
use Raven\Falcon\Http\Exceptions\BadRequestException;
use Raven\Falcon\Http\Exceptions\NotFoundException;
use Raven\Falcon\Http\Exceptions\UnauthorizedException;
use Raven\Falcon\Http\Response;

class AuthService
{
	private readonly MailerService $mailerService;
	public function __construct(
		private IMailer $iMailer,
		private readonly CollaboratorEntity $collaboratorEntity = new CollaboratorEntity,
		private readonly UserCodeEntity $userCodeEntity = new UserCodeEntity,
		private readonly HashService $hashService = new HashService,
		private readonly TokenService $tokenService = new TokenService,
		private readonly CodeService $codeService = new CodeService
	) {
		$this->mailerService = new MailerService($iMailer);
	}

	function login(LoginDto $loginDto)
	{
		try {
			$user = $this->collaboratorEntity->selectByEmail($loginDto->email);
			if (!(array) $user) throw new NotFoundException('Usuário não encontrado');
			if (!isset($user->password)) throw new NotFoundException('Primeiro acesso não realizado');

			if (!$this->hashService->verify($loginDto->password, $user->password)) throw new UnauthorizedException('Senha incorreta');

			$accessToken = $this->tokenService->generate([
				'id' => $user->id,
				'is_admin' => $user->is_admin
			], getenv("JWT_SECRET"));

			return Response::sendBody([
				'message' => 'Login efetuado com sucesso',
				'accessToken' => $accessToken
			]);
		} catch (PDOException $error) {
			throw new BadRequestException($error->getMessage());
		}
	}

	function sendFirstAccessEmail(SendEmailDto $sendFirstAccessEmailDto)
	{
		try {
			$user = $this->collaboratorEntity->selectByEmail($sendFirstAccessEmailDto->email);
			if (!$user) throw new NotFoundException('Usuário não encontrado');
			if ($user->password) throw new NotFoundException('Usuário já está ativo');

			$verificationCode = $this->codeService->generateRandom();

			$this->userCodeEntity->update($verificationCode, $user->id);
			$this->mailerService->sendRegistrationCode($sendFirstAccessEmailDto->email, $verificationCode);

			return Response::sendBody([
				"message" => "Email enviado com sucesso"
			]);
		} catch (PDOException $error) {
			throw new BadRequestException($error->getMessage());
		}
	}

	function firstAccess(FirstAccessDto $firstAccessDto)
	{
		try {
			$user = $this->collaboratorEntity->selectByEmail($firstAccessDto->email);
			if (!$user) throw new NotFoundException('Usuário não encontrado');
			if ($user->password) throw new BadRequestException('Usuário já está ativo');

			$verificationCodeFromDatabase = $this->userCodeEntity->selectByUserId($user->id);
			if (!$verificationCodeFromDatabase) throw new NotFoundException('Ainda não foi enviado um email de primeiro acesso, solicite-o primeiramente');

			if ($verificationCodeFromDatabase->verification_code !== $firstAccessDto->verificationCode) throw new BadRequestException('O código de verificação não coincide');

			$hashedPassword = $this->hashService->hash($firstAccessDto->password);
			$this->collaboratorEntity->updatePassword($user->id, $hashedPassword);
			$this->userCodeEntity->delete($verificationCodeFromDatabase->id);

			return Response::sendBody([
				'message' => 'usuário verificado com sucesso'
			]);
		} catch (PDOException $error) {
			throw new BadRequestException($error->getMessage());
		}
	}

	function sendResetPasswordEmail(SendEmailDto $sendResetPasswordEmailDto)
	{
		try {
			$user = $this->collaboratorEntity->selectByEmail($sendResetPasswordEmailDto->email);
			if (!$user) throw new NotFoundException('Usuário não encontrado');
			if (!$user->password) throw new NotFoundException('Usuário não está ativo');

			$verificationCode = $this->codeService->generateRandom();
			$this->userCodeEntity->upsert($verificationCode, $user->id);
			$this->mailerService->sendResetPassword($sendResetPasswordEmailDto->email, $verificationCode);

			return Response::sendBody([
				"message" => "Email enviado com sucesso"
			]);
		} catch (PDOException $error) {
			throw new BadRequestException($error->getMessage());
		}
	}

	function resetPassword(ResetPasswordDto $resetPasswordDto)
	{
		try {
			$user = $this->collaboratorEntity->selectByEmail($resetPasswordDto->email);
			if (!$user) throw new NotFoundException('Usuário não encontrado');
			if (!$user->password) throw new BadRequestException('Usuário não está ativo');

			$verificationCodeFromDatabase = $this->userCodeEntity->selectByUserId($user->id);
			if (!$verificationCodeFromDatabase) throw new NotFoundException('Ainda não foi enviado um email de redefinição de senha, solicite-o primeiramente');

			if ($verificationCodeFromDatabase->verification_code !== $resetPasswordDto->verificationCode) throw new BadRequestException('O código de verificação não coincide');

			$hashedPassword = $this->hashService->hash($resetPasswordDto->password);
			$this->collaboratorEntity->updatePassword($user->id, $hashedPassword);
			$this->userCodeEntity->delete($verificationCodeFromDatabase->id);

			return Response::sendBody([
				'message' => 'Senha redefinida com sucesso'
			]);
		} catch (PDOException $error) {
			throw new BadRequestException($error->getMessage());
		}
	}
}
