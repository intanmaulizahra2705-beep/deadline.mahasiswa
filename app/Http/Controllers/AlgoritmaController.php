<?php

namespace App\Http\Controllers;

use App\Services\PriorityScheduler;
use Illuminate\Http\Request;

class AlgoritmaController extends Controller
{
    public function __construct(private PriorityScheduler $scheduler)
    {
    }

    public function index(Request $request)
    {
        $items = $this->scheduler->aktifTerurut($request->user());

        return view('algoritma.index', [
            'items' => $items,
            'cfg'   => config('priority'),
        ]);
    }
}
