<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ArtWork;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display main dashboard overview
     */
    public function index()
    {
        // Ticket Sales Statistics
        $todayTicketSales = Order::whereDate('order_date', today())
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalTicketsSold = Ticket::whereHas('order', function($q) {
            $q->whereDate('order_date', today());
        })->count();

        $monthlyRevenue = Order::whereMonth('order_date', now()->month)
            ->whereYear('order_date', now()->year)
            ->where('status', 'completed')
            ->sum('total_amount');

        $pendingOrders = Order::where('status', 'pending')
            ->count();

        // Charts Data - Sales by Day (Last 7 Days)
        $salesByDay = [];
        $last7Days = collect(range(6, 0))->map(function ($day) {
            $date = now()->subDays($day);
            return [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('D'),
                'sales' => Order::whereDate('order_date', $date)
                    ->where('status', 'completed')
                    ->sum('total_amount')
            ];
        });

        // Recent Transactions
        $recentTransactions = Order::with(['user', 'orderDetails.ticket.ticketType', 'payment'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('order_date', 'desc')
            ->limit(10)
            ->get();

        // Art Statistics
        $totalArtworks = ArtWork::count();
        $artworksByDept = ArtWork::select('department_id', DB::raw('count(*) as count'))
            ->groupBy('department_id')
            ->limit(5)
            ->get();

        // Top Ticket Types
        $topTicketTypes = Ticket::select('ticket_type_id', DB::raw('count(*) as count'), DB::raw('sum(price) as revenue'))
            ->groupBy('ticket_type_id')
            ->with('ticketType')
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', [
            'todayTicketSales' => $todayTicketSales,
            'totalTicketsSold' => $totalTicketsSold,
            'monthlyRevenue' => $monthlyRevenue,
            'pendingOrders' => $pendingOrders,
            'last7Days' => $last7Days,
            'recentTransactions' => $recentTransactions,
            'totalArtworks' => $totalArtworks,
            'artworksByDept' => $artworksByDept,
            'topTicketTypes' => $topTicketTypes,
        ]);
    }

    /**
     * Display transactions module
     */
    public function transactions()
    {
        $perPage = request('per_page', 25);
        $search = request('search');
        $filter = request('filter', 'all');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $query = Order::with(['user', 'payment', 'orderDetails.ticket.ticketType'])
            ->orderBy('order_date', 'desc');

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%$search%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%")
                            ->orWhere('email', 'like', "%$search%");
                    });
            });
        }

        // Filter by status
        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('order_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('order_date', '<=', $dateTo);
        }

        $transactions = $query->paginate($perPage);

        // Summary Statistics
        $stats = [
            'total_transactions' => Order::count(),
            'total_revenue' => Order::where('status', 'completed')->sum('total_amount'),
            'total_tickets_sold' => Ticket::count(),
            'pending_count' => Order::where('status', 'pending')->count(),
            'completed_count' => Order::where('status', 'completed')->count(),
            'cancelled_count' => Order::where('status', 'cancelled')->count(),
        ];

        // Chart data - Weekly sales
        $weeklySales = collect(range(6, 0))->map(function ($day) {
            $date = now()->subDays($day);
            return [
                'label' => $date->format('D'),
                'value' => Order::whereDate('order_date', $date)
                    ->where('status', 'completed')
                    ->sum('total_amount'),
                'count' => Order::whereDate('order_date', $date)->count(),
            ];
        });

        // Chart data - Monthly sales (last 12 months)
        $monthlySales = collect(range(11, 0))->map(function ($month) {
            $date = now()->subMonths($month);
            return [
                'label' => $date->format('M'),
                'value' => Order::whereMonth('order_date', $date->month)
                    ->whereYear('order_date', $date->year)
                    ->where('status', 'completed')
                    ->sum('total_amount'),
            ];
        });

        return view('admin.dashboard.transactions', [
            'transactions' => $transactions,
            'stats' => $stats,
            'weeklySales' => $weeklySales,
            'monthlySales' => $monthlySales,
            'search' => $search,
            'filter' => $filter,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    /**
     * Display artworks module
     */
    public function artworks()
    {
        $perPage = request('per_page', 25);
        $search = request('search');
        $department = request('department');
        $sortBy = request('sort_by', 'latest');

        $query = ArtWork::query();

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhereHas('constituents', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        // Filter by department
        if ($department) {
            $query->where('department_id', $department);
        }

        // Sort
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('art_work_id', 'asc');
                break;
            case 'title_az':
                $query->orderBy('title', 'asc');
                break;
            case 'title_za':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->orderBy('art_work_id', 'desc');
        }

        $artworks = $query->with(['department', 'images', 'constituents'])->paginate($perPage);

        // Statistics
        $stats = [
            'total_artworks' => ArtWork::count(),
            'total_departments' => ArtWork::distinct('department_id')->count(),
            'total_images' => DB::table('art_work_images')->count(),
            'total_artists' => DB::table('constituents')->count(),
        ];

        // Get departments for filter
        $departments = DB::table('departments')->get();

        return view('admin.dashboard.artworks', [
            'artworks' => $artworks,
            'stats' => $stats,
            'departments' => $departments,
            'search' => $search,
            'department' => $department,
            'sortBy' => $sortBy,
        ]);
    }

    /**
     * Store new artwork
     */
    public function storeArtwork()
    {
        $validated = request()->validate([
            'title' => 'required|string|max:255',
            'accession_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'department_id' => 'required|integer',
            'type_id' => 'nullable|integer',
            'repository_id' => 'nullable|integer',
            'classification_id' => 'nullable|integer',
            'location_id' => 'nullable|integer',
            'year_created' => 'nullable|integer',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        try {
            $artwork = ArtWork::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'department_id' => $validated['department_id'],
                'accession_number' => $validated['accession_number'] ?? 'ACC-' . time(),
                'type_id' => $validated['type_id'] ?? 1,
                'repository_id' => $validated['repository_id'] ?? 1,
                'classification_id' => $validated['classification_id'] ?? 1,
                'location_id' => $validated['location_id'] ?? 1,
                'met_object_id' => rand(100000, 999999),
                'accession_year' => $validated['year_created'] ?? null,
                'is_on_view' => 0,
                'is_highlight' => 0,
                'is_public_domain' => 0,
                'is_timeline_work' => 0,
            ]);

            // Handle images
            if (request()->hasFile('images')) {
                foreach (request()->file('images') as $image) {
                    $path = $image->store('artworks', 'public');
                    $artwork->images()->create(['image_url' => $path]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Artwork added successfully',
                'data' => $artwork->load('images')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add artwork: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update artwork
     */
    public function updateArtwork($id)
    {
        try {
            $artwork = ArtWork::findOrFail($id);

            $validated = request()->validate([
                'title' => 'sometimes|string|max:255',
                'accession_number' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'department_id' => 'sometimes|integer',
                'type_id' => 'nullable|integer',
                'repository_id' => 'nullable|integer',
                'classification_id' => 'nullable|integer',
                'location_id' => 'nullable|integer',
                'year_created' => 'nullable|integer',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            $updateData = [];
            if (isset($validated['title'])) $updateData['title'] = $validated['title'];
            if (isset($validated['description'])) $updateData['description'] = $validated['description'];
            if (isset($validated['department_id'])) $updateData['department_id'] = $validated['department_id'];
            if (isset($validated['accession_number'])) $updateData['accession_number'] = $validated['accession_number'];
            if (isset($validated['type_id'])) $updateData['type_id'] = $validated['type_id'];
            if (isset($validated['repository_id'])) $updateData['repository_id'] = $validated['repository_id'];
            if (isset($validated['classification_id'])) $updateData['classification_id'] = $validated['classification_id'];
            if (isset($validated['location_id'])) $updateData['location_id'] = $validated['location_id'];
            if (isset($validated['year_created'])) $updateData['accession_year'] = $validated['year_created'];

            $artwork->update($updateData);

            // Handle new images
            if (request()->hasFile('images')) {
                foreach (request()->file('images') as $image) {
                    $path = $image->store('artworks', 'public');
                    $artwork->images()->create(['image_url' => $path]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Artwork updated successfully',
                'data' => $artwork->load('images')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update artwork: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete artwork
     */
    public function destroyArtwork($id)
    {
        try {
            $artwork = ArtWork::findOrFail($id);
            
            // Delete images
            foreach ($artwork->images as $image) {
                \Storage::disk('public')->delete($image->image_url);
                $image->delete();
            }

            $artwork->delete();

            return response()->json([
                'success' => true,
                'message' => 'Artwork deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete artwork: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get transaction details (for modal/view)
     */
    public function getTransactionDetail($orderId)
    {
        try {
            $transaction = Order::with(['user', 'payment', 'orderDetails.ticket.ticketType'])
                ->findOrFail($orderId);

            return view('admin.dashboard.modals.transaction-detail', ['transaction' => $transaction])->render();
        } catch (\Exception $e) {
            return '<p>Error loading transaction details</p>';
        }
    }

    /**
     * Get artwork details (for modal/view)
     */
    public function getArtworkDetail($artworkId)
    {
        try {
            $artwork = ArtWork::with(['department', 'images', 'constituents'])->findOrFail($artworkId);
            
            return view('admin.dashboard.modals.artwork-detail', ['artwork' => $artwork])->render();
        } catch (\Exception $e) {
            return '<p>Error loading artwork details</p>';
        }
    }

    /**
     * Edit artwork - get data for modal
     */
    public function getArtworkForEdit($artworkId)
    {
        $filter = request('filter', 'all');
        $dateFrom = request('date_from');
        $dateTo = request('date_to');

        $query = Order::with(['user', 'payment', 'orderDetails.ticket.ticketType']);

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }
        if ($dateFrom) {
            $query->whereDate('order_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('order_date', '<=', $dateTo);
        }

        $transactions = $query->get();

        $csv = "Transaction ID,Date,Customer,Tickets,Total,Status,Payment Method\n";
        foreach ($transactions as $trans) {
            $csv .= sprintf(
                "%s,%s,%s,%d,%s,%s,%s\n",
                $trans->order_code,
                $trans->order_date->format('Y-m-d H:i'),
                $trans->user->name ?? 'Guest',
                $trans->orderDetails->sum('quantity'),
                number_format($trans->total_amount, 2),
                $trans->status,
                $trans->payment->payment_method ?? 'N/A'
            );
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="transactions_' . now()->format('Y-m-d_His') . '.csv"');
    }
}
