<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
// use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard', [
            'categoryCount' => Category::count(),
            'customerCount' => Customer::count(),
            // 'productCount'  => Product::count(),
            'supplierCount' => Supplier::count(),
            'userCount' => User::count(),
        ]);
    }
}
