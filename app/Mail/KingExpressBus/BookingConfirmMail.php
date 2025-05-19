<?php

namespace App\Mail\KingExpressBus;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

// Implement ShouldQueue để gửi mail qua hàng đợi (nên dùng)
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// Để ghi log nếu cần

// implements ShouldQueue // Bỏ comment dòng này nếu bạn muốn sử dụng Queue
class BookingConfirmMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Dữ liệu chi tiết đặt vé sẽ được truyền vào view.
     * Thuộc tính public sẽ tự động có sẵn trong view.
     *
     * @var array
     */
    public array $bookingDetails;

    /**
     * Create a new message instance.
     *
     * @param array $bookingDetails Mảng chứa thông tin chi tiết đặt vé.
     */
    public function __construct(array $bookingDetails)
    {
        $this->bookingDetails = $bookingDetails;
        Log::info('BookingConfirmMail created with details:', $bookingDetails); // Ghi log để debug (tùy chọn)
    }

    /**
     * Get the message envelope.
     * Thiết lập người nhận, tiêu đề email.
     */
    public function envelope(): Envelope
    {
        // Tạo tiêu đề email động hơn
        $subject = sprintf(
            'Xác nhận đặt vé #%s - %s đi %s ngày %s',
            $this->bookingDetails['booking_id'] ?? 'Mới', // Lấy booking_id nếu có
            $this->bookingDetails['start_province'] ?? 'N/A',
            $this->bookingDetails['end_province'] ?? 'N/A',
            $this->bookingDetails['departure_date'] ?? 'N/A'
        );

        return new Envelope(
        // Có thể thêm from() nếu muốn chỉ định người gửi cụ thể
        // from: new Address('noreply@kingexpressbus.com', 'King Express Bus'),
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     * Chỉ định view và truyền dữ liệu vào view.
     */
    public function content(): Content
    {
        return new Content(
            view: 'kingexpressbus.mail.booking_confirm',
        // Không cần dùng with() vì $bookingDetails là public property
        // with: [
        //     'details' => $this->bookingDetails, // Truyền dữ liệu vào view với tên biến 'details'
        // ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Trả về mảng rỗng nếu không có file đính kèm
        return [];
        // Ví dụ đính kèm file PDF vé điện tử (nếu có):
        // return [
        //     Attachment::fromPath('/path/to/your/eticket.pdf')
        //         ->as('VeDienTu-' . $this->bookingDetails['booking_id'] . '.pdf')
        //         ->withMime('application/pdf'),
        // ];
    }
}
