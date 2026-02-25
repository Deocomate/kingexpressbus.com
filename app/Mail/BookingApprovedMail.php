<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BookingApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public array $bookingDetails;

    public function __construct(array $bookingDetails)
    {
        $this->bookingDetails = $bookingDetails;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = sprintf(
            'Xác nhận vé thành công #%s - %s đi %s ngày %s',
            $this->bookingDetails['booking_code'] ?? 'N/A',
            $this->bookingDetails['start_province'] ?? 'N/A',
            $this->bookingDetails['end_province'] ?? 'N/A',
            $this->bookingDetails['departure_date'] ?? 'N/A'
        );

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.booking_approved',
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

    public function failed(\Throwable $exception): void
    {
        Log::error('Job gửi mail xác nhận vé thất bại', [
            'booking_code' => $this->bookingDetails['booking_code'] ?? 'N/A',
            'error' => $exception->getMessage(),
        ]);
    }
}
