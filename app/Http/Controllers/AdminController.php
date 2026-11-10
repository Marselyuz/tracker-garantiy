<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Receipt;
use App\Models\Item;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalReceipts = Receipt::count();
        $totalItems = Item::count();

       
        $users = User::withCount('receipts')->orderBy('created_at', 'desc')->get();

        return view('pages.admin_dashboard', compact('totalUsers', 'totalReceipts', 'totalItems', 'users'));
    }

    public function changeRole(Request $request, User $user)
    {
        
        if (auth()->id() === $user->id) {
            return back(); 
        }

        
        $validated = $request->validate([
            'role' => 'required|in:user,admin,banned'
        ]);

        
        $user->role = $validated['role'];
        $user->save();

        return back();
    }
}