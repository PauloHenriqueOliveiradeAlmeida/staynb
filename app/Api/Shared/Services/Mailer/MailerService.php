<?php

namespace App\Api\Shared\Services\Mailer;

use App\Api\Shared\Services\Mailer\Dtos\SendConversationInviteDto;

class MailerService
{
	public function __construct(
		private readonly IMailer $mailerGateway
	) {}

	public function sendRegistrationCode(string $destination, string $verificationCode)
	{
		return $this->mailerGateway->send($destination, 'Bem-vindo ao Lazynb!', getenv("MAILER_REGISTRATION_TEMPLATE_ID"), [
			'verification_code' => $verificationCode
		]);
	}

	public function sendResetPassword(string $destination, string $verificationCode)
	{
		return $this->mailerGateway->send($destination, 'Redefinição de senha', getenv("MAILER_RESET_PASSWORD_TEMPLATE_ID"), [
			'verification_code' => $verificationCode
		]);
	}

	public function sendConversationInvite(string $destination, SendConversationInviteDto $sendConversationInviteDto)
	{
		return $this->mailerGateway->send($destination, 'Solicitação de contato', getenv("MAILER_CONVERSATION_INVITE_TEMPLATE_ID"), (array) $sendConversationInviteDto);
	}
}
