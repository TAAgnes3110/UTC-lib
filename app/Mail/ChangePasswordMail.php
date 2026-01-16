<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChangePasswordMail extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * Create a new message instance.
   *
   * @param string $name Tên người dùng
   * @param string $changedAt Thời gian đổi mật khẩu
   * @param string|null $ipAddress Địa chỉ IP (tùy chọn)
   */
  public function __construct(
    public string $name,
    public string $changedAt,
    public ?string $ipAddress = null
  ) {}

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      subject: 'Thông báo đổi mật khẩu thành công',
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    return new Content(
      view: 'emails.change-password',
      with: [
        'name' => $this->name,
        'changedAt' => $this->changedAt,
        'ipAddress' => $this->ipAddress,
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
