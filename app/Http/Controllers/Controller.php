<?php

namespace App\Http\Controllers;

use App\Models\Bottom;
use App\Models\Title;
use App\Models\Total;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $view = [];

    public function __construct()
    {
        $this->view['title'] = Title::select("id", "img", "text")->where("sh", 1)->first();
        $this->view['title']->img = asset("storage/" . $this->view['title']->img);
        if (!session()->has('visitor')) {
            $total = Total::first();
            $total->total++;
            $total->save();
            $this->view['total'] = $total->total;
            session()->put('visitor', $total->total);
        } else {
            $this->view['total'] = Total::first()->total;
        }
        $this->view['bottom'] = Bottom::first()->bottom;
    }
}
