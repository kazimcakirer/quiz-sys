<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
// 主選單及次選單表已設定關聯，因此次選單可以不在這裏引用
use App\Models\SubMenu;
use App\Models\Image;
use App\Models\Ad;
use App\Models\Mvim;
use App\Models\News;
use Auth;
// use App\Models\Total;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function home()
    {

        $this->sideBar();


        $mvims = Mvim::select('id', 'img')->where("sh", 1)->get()->map(function ($val, $idx) {
            $val->show = ($idx == 0) ? true : false;
            $val->img = asset("storage/" . $val->img);
            return $val;
        });
        $news = News::select("id", "text")->where("sh", 1)->get()->filter(function ($val, $idx) {
            if ($idx > 4) {
                $this->view['news']['more'] = ['show' => true, 'href' => '/news'];
            } else {
                $val->short = mb_substr(str_replace("\r\n", " ", $val->text), 0, 25, 'utf8') . "...";
                $val->text = str_replace("\r\n", " ", nl2br($val->text));
                $val->show = false;
                $this->view['news']['more'] = ['show' => false];
                return $val;
            }
        });


        // dd($news);

        $this->view['mvims'] = $mvims;
        $this->view['news']['data'] = $news;
        if (Auth::check()) {

            $this->view['auth'] = [
                'href' => '/admin',
                'class' => 'btn-success',
                'text' => '返回管理',
                'user' => Auth::user()->acc
            ];
        } else {
            $this->view['auth'] = [
                'href' => '/login',
                'class' => 'btn-primary',
                'text' => '管理登入',
                'user' => '訪客'
            ];
        }
// dd($this->view);
        return $this->view;
    }

    public function index()
    {
        //
        // $this->sideBar();





        // $mvims = Mvim::select('id', 'img')->where("sh", 1)->get()->map(function ($val, $idx) {
        //     $val->show = ($idx == 0) ? true : false;
        //     $val->img = asset("storage/" . $val->img);
        //     return $val;
        // });
        // $news = News::select("id", "text")->where("sh", 1)->get()->filter(function ($val, $idx) {
        //     if ($idx > 4) {
        //         $this->view['news']['more'] = ['show' => true, 'href' => '/news'];
        //     } else {
        //         $val->short = mb_substr(str_replace("\r\n", " ", $val->text), 0, 25, 'utf8') . "...";
        //         $val->text = str_replace("\r\n", " ", nl2br($val->text));
        //         $val->show = false;
        //         $this->view['news']['more'] = ['show' => false];
        //         return $val;
        //     }
        // });


        // // dd($news);

        // $this->view['mvims'] = $mvims;
        // $this->view['news']['data'] = $news;


        return view('main', $this->home());
    }

    protected function sideBar()
    {
        $ads = implode("　　", AD::where("sh", 1)->get()->pluck('text')->all());
        $menus = Menu::where('sh', 1)->get();
        $images = Image::select('id', 'img')->where('sh', 1)->get()->map(function ($val, $idx) {
            $val->img = asset("storage/" . $val->img);
            if ($idx > 2) {
                $val->show = false;
            } else {
                $val->show = true;
            }

            // dd($val);
            return $val;
        });



        foreach ($menus as $key => $menu) {
            // $subs = Submenu::where("menu_id", $menu->id)->get();
            // 資料表已設定關聯函式，可使用語法如下：
            $subs = $menu->subs;
            // dd($subs);
            $menu->subs = $subs;
            // dd($menu);
            $menus[$key] = $menu;
            $menu->show = false;
        }

        if (Auth::user()) {
            $this->view['user'] = Auth::user();
        }



        $this->view['ads'] = $ads;
        $this->view['menus'] = $menus;
        $this->view['images'] = ['data' => $images, 'page' => 0];
        // dd($this->view['images']);
        $this->view['site'] = [
            'ads' => $ads,
            'title' => $this->view['title'],
            'total' => $this->view['total'],
            'bottom' => $this->view['bottom'],
        ];
        // dd($this->view['site']);
    }
}
