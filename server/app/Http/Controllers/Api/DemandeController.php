<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Demande;

class DemandeController extends Controller
{
    public function index(Request $request)
    {
        $query = Demande::query();

        // Filtering
        if ($request->has('groupe_sanguin') && $request->groupe_sanguin) {
            $query->where('groupe_sanguin', $request->groupe_sanguin);
        }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Pagination (default 10 per page)
        return $query->paginate($request->get('per_page', 10));
    }
}
