<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BaseMail extends Mailable implements ShouldQueue
{
  use Queueable, SerializesModels;

  /**
   * Dữ liệu email
   */
  public array $mailData = [
    'from' => [],
    'subject' => '',
    'body' => '',
    'view' => '',
    'files' => [],
  ];

  /**
   * Create a new message instance.
   *
   * @param array $mailData Dữ liệu email
   */
  public function __construct(array $mailData)
  {
    $this->mailData = array_merge($this->mailData, $mailData);
  }

  /**
   * Get the message envelope.
   */
  public function envelope(): Envelope
  {
    if (!empty($this->mailData['from'])) {
      return new Envelope(
        from: new Address(
          $this->mailData['from']['address'],
          $this->mailData['from']['name'] ?? config('mail.from.name')
        ),
        subject: $this->mailData['subject'],
      );
    }

    return new Envelope(
      subject: $this->mailData['subject'],
    );
  }

  /**
   * Get the message content definition.
   */
  public function content(): Content
  {
    $view = $this->mailData['view'] ?? 'emails.base';

    return new Content(
      view: $view,
      with: [
        'body' => $this->mailData['body'],
        'data' => $this->mailData,
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
    return $this->mailData['files'] ?? [];
  }
}
