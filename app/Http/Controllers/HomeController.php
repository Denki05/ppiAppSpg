<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function getNotifData(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $userId = Auth::id(); // Ambil ID pengguna yang sedang login

        // Ambil notifikasi yang belum dibaca
        $notifications = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('type', 'App\Notifications\JurnalSettledNotification')
            ->orderBy('created_at', 'desc')
            ->limit(10) // Batasi jumlah notifikasi
            ->get();

        // Tambahkan pesan default jika kosong
        $notifications = $notifications->map(function ($notif) {
            $data = json_decode($notif->data, true) ?: []; // Decode menjadi array
            $data['message'] = $data['message'] ?? 'No message'; // Pesan default
            $notif->data = $data;

            return $notif;
        });

        // Hitung jumlah notifikasi yang belum dibaca
        $notifCount = $notifications->count();

        return response()->json([
            'notifications' => $notifications,
            'notifCount' => $notifCount,
        ]);
    }

    public function unread_all_notif(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $userId = Auth::id(); // Ambil ID pengguna

        try {
            // Tandai semua notifikasi sebagai telah dibaca
            DB::table('notifications')
                ->where('notifiable_id', $userId)
                ->whereNull('read_at')
                ->where('type', 'App\Notifications\JurnalSettledNotification') // Pastikan hanya tipe tertentu
                ->update(['read_at' => now()]);

            return response()->json(['success' => true, 'message' => 'Semua notifikasi telah ditandai sebagai telah dibaca.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menandai semua notifikasi.', 'error' => $e->getMessage()]);
        }
    }
}
