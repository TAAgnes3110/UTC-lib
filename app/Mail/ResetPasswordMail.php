<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   *
   * @param string $token Token reset password hoặc OTP
   * @param string $email Email người nhận
   * @param string $name Tên người dùng
   * @param string $resetUrl URL reset password (nếu dùng link)
   * @param int $expiresIn Thời gian hết hạn (phút)
   */
  public function __construct(
    public string $token,
    public string $email,
    public string $name,
    public ?string $resetUrl = null,
    public int $expiresIn = 60
  ) {}

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Đặt lại mật khẩu',
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.reset-password',
      with: [
        'token' => $this->token,
        'email' => $this->email,
        'name' => $this->name,
        'resetUrl' => $this->resetUrl,
        'expiresIn' => $this->expiresIn,
      ],
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array<int, \Illuminate\Mail\Mailables\Attachment>
   */
  public function attachments(): array
  {
    return [];
  }
}
