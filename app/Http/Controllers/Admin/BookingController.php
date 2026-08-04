<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBookingRequest;
use App\Http\Requests\Admin\UpdateBookingRequest;
use App\Models\Booking;
use App\Support\Admin\TableQuery;
use App\Support\Admin\Tables\BookingTableConfig;
use App\Support\DepartureAtExpression;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class BookingController extends Controller
{
    // ─── List ────────────────────────────────────────────────────────────────

    public function index(Request $request): View
    {
        [$tableQuery, $config] = $this->buildTableQuery($request);
        $tq = TableQuery::make($tableQuery, $config)->process($request);

        if ($request->header('X-Partial') === 'table') {
            return view('admin.bookings._table', [
                'paginator'      => $tq->paginator(),
                'config'         => $config,
                'activeTab'      => $tq->activeTab(),
                'activeSortKey'  => $tq->activeSortKey(),
                'activeSortDir'  => $tq->activeSortDir(),
                'activeSearch'   => $tq->activeSearch(),
                'activeFilters'  => $request->input('filter', []),
                'badges'         => $tq->tabBadges(),
            ]);
        }

        return view('admin.bookings.index', [
            'paginator'   => $tq->paginator(),
            'config'      => $config,
            'activeTab'   => $tq->activeTab(),
            'activeSearch'=> $tq->activeSearch(),
            'activeSortKey'  => $tq->activeSortKey(),
            'activeSortDir'  => $tq->activeSortDir(),
            'badges'      => $tq->tabBadges(),
            'activeFilters'  => $request->input('filter', []),
            'statusOptions'  => $this->statusOptions(),
            'paymentStatusOptions' => $this->paymentStatusOptions(),
        ]);
    }

    /**
     * AJAX/poll endpoint: renders only the table rows + pagination partial.
     * Alpine polls this every 60s when the browser tab is visible.
     * Accepts the same query params as index (tab, search, sort, direction, filter, page).
     */
    public function table(Request $request): View
    {
        [$tableQuery, $config] = $this->buildTableQuery($request);
        $tq = TableQuery::make($tableQuery, $config)->process($request);

        return view('admin.bookings._table', [
            'paginator'      => $tq->paginator(),
            'config'         => $config,
            'activeTab'      => $tq->activeTab(),
            'activeSortKey'  => $tq->activeSortKey(),
            'activeSortDir'  => $tq->activeSortDir(),
            'activeSearch'   => $tq->activeSearch(),
            'activeFilters'  => $request->input('filter', []),
            'badges'         => $tq->tabBadges(),
        ]);
    }

    // ─── Show (detail slide-over partial) ────────────────────────────────────

    public function show(int $booking): View
    {
        $model = Booking::with([
            'trip.route', 'trip.bus',
            'pickupStop', 'dropoffStop', 'user',
        ])->findOrFail($booking);

        return view('admin.bookings._detail', ['booking' => $model]);
    }

    // ─── Create ──────────────────────────────────────────────────────────────

    public function create(): View
    {
        return view('admin.bookings.form', [
            'booking'            => null,
            'paymentMethodOptions' => $this->paymentMethodOptions(),
            'paymentStatusOptions' => $this->paymentStatusOptions(),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $booking = Booking::create($request->fillableData());

        // Assign non-fillable payment fields explicitly when provided
        $this->assignExplicitPaymentFields($booking, $request);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Đã tạo đặt vé '.$booking->booking_code);
    }

    // ─── Edit ────────────────────────────────────────────────────────────────

    public function edit(int $booking): View
    {
        $model = Booking::with(['trip.route', 'pickupStop', 'dropoffStop', 'user'])->findOrFail($booking);

        return view('admin.bookings.form', [
            'booking'              => $model,
            'paymentMethodOptions' => $this->paymentMethodOptions(),
            'paymentStatusOptions' => $this->paymentStatusOptions(),
        ]);
    }

    public function update(UpdateBookingRequest $request, int $booking): RedirectResponse
    {
        $model = Booking::findOrFail($booking);
        $model->fill($request->fillableData());
        $model->save();

        // Assign non-fillable payment fields explicitly when provided
        $this->assignExplicitPaymentFields($model, $request);

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'Đã cập nhật đặt vé '.$model->booking_code);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Builds the base Eloquent query with eager loads and request filters applied.
     * Filters are applied before passing to TableQuery so the tab modifier is additive.
     *
     * @return array{0: \Illuminate\Database\Eloquent\Builder, 1: \App\Support\Admin\TableConfig}
     */
    private function buildTableQuery(Request $request): array
    {
        $query = Booking::query()
            ->with(['trip.route', 'pickupStop', 'dropoffStop', 'user'])
            ->select('bookings.*')
            ->addSelect(DB::raw(DepartureAtExpression::asSelect()));

        // Default sort (overridden by TableQuery when sort param is present)
        $query->orderBy('bookings.created_at', 'desc');

        // Apply explicit filters from the request before handing off to TableQuery
        $filters = $request->input('filter', []);

        if (! empty($filters['status'])) {
            $query->where('bookings.status', $filters['status']);
        }
        if (! empty($filters['payment_status'])) {
            $query->where('bookings.payment_status', $filters['payment_status']);
        }
        if (! empty($filters['from'])) {
            $query->whereDate('bookings.booking_date', '>=', $filters['from']);
        }
        if (! empty($filters['until'])) {
            $query->whereDate('bookings.booking_date', '<=', $filters['until']);
        }

        $config = BookingTableConfig::make();

        return [$query, $config];
    }

    /**
     * Explicitly assigns payment_status and payment_transaction_id without mass-assignment,
     * because these fields are intentionally excluded from Booking::$fillable.
     * Only updates when the admin has explicitly submitted these fields.
     */
    private function assignExplicitPaymentFields(Booking $booking, StoreBookingRequest|UpdateBookingRequest $request): void
    {
        $dirty = false;

        if ($request->filled('payment_status')) {
            $booking->payment_status = $request->input('payment_status');
            $dirty = true;
        }
        if ($request->filled('payment_transaction_id')) {
            $booking->payment_transaction_id = $request->input('payment_transaction_id');
            $dirty = true;
        }

        if ($dirty) {
            $booking->save();
            Log::info('Admin explicitly set payment fields for booking', [
                'booking_id' => $booking->id,
                'admin_id'   => auth()->id(),
            ]);
        }
    }

    private function statusOptions(): array
    {
        return collect(BookingStatus::cases())
            ->mapWithKeys(fn (BookingStatus $s) => [$s->value => $s->getLabel()])
            ->all();
    }

    private function paymentStatusOptions(): array
    {
        return collect(PaymentStatus::cases())
            ->mapWithKeys(fn (PaymentStatus $s) => [$s->value => $s->getLabel()])
            ->all();
    }

    private function paymentMethodOptions(): array
    {
        return collect(PaymentMethod::cases())
            ->mapWithKeys(fn (PaymentMethod $m) => [$m->value => $m->getLabel()])
            ->all();
    }
}
