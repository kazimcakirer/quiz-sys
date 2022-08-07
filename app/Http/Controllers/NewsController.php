<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;

class NewsController extends HomeController
{
    //
    public function vue($route)
    {
        $news=[];
        $more = [];
        switch ($route) {
            case "index":
                $news = News::select("id", "text")->where("sh", 1)->get()->filter(function ($val, $idx) use (&$more) {
                    if ($idx > 4) {
                        $more = ['show' => true, 'href' => '/news'];
                    } else {
                        $val->short = mb_substr(str_replace("\r\n", " ", $val->text), 0, 25, 'utf8') . "...";
                        $val->text = str_replace("\r\n", " ", nl2br($val->text));
                        $val->show = false;
                        $more = ['show' => false];
                        return $val;
                    }
                });
                break;
            case "news":
                $news = News::select("id", "text")->where("sh", 1)->get()->filter(function ($val, $idx) use (&$more) {
                    $val->short = mb_substr(str_replace("\r\n", " ", $val->text), 0, 25, 'utf8') . "...";
                    $val->text = str_replace("\r\n", " ", nl2br($val->text));
                    $val->show = false;
                    $more = ['show' => false];
                    return $val;
                });
                break;
            }
            $news = ['news' => $news, 'more' => $more];
            // dd($news);
        return $news;
    }

    public function list()
    {
        parent::sideBar();
        $this->view['news'] = News::where('sh', 1)->paginate(5);
        return view('news', $this->view);
    }

    public function index()
    {
        $all = News::paginate(3);
        // dd($all);
        $cols = ['最新消息內容', '顯示', '編輯', '刪除',];
        $rows = [];

        foreach ($all as $a) {
            $tmp = [
                [
                    'tag' => '',
                    'text' => mb_substr($a->text, 0, 50, 'utf8'),
                ],
                [
                    'tag' => 'button',
                    'type' => 'button',
                    'btn_color' => 'btn-success',
                    'action' => 'show',
                    'id' => $a->id,
                    'text' => ($a->sh == 1) ? '顯示' : '隱藏',
                ],
                [
                    'tag' => 'button',
                    'type' => 'button',
                    'btn_color' => 'btn-info',
                    'action' => 'edit',
                    'id' => $a->id,
                    'text' => '編輯',
                ],
                [
                    'tag' => 'button',
                    'type' => 'button',
                    'btn_color' => 'btn-danger',
                    'action' => 'delete',
                    'id' => $a->id,
                    'text' => '刪除',
                ],
            ];

            $rows[] = $tmp;
        }

        // dd($rows);

        $this->view['header'] = '最新消息內容管理';
        $this->view['module'] = 'News';
        $this->view['cols'] = $cols;
        $this->view['rows'] = $rows;
        $this->view['all'] = $all;
        // $this->view['paginate']=$all->paginate(3);
        return view('backend.module', $this->view);
    }


    public function create()
    {
        //
        $view = [
            'action' => '/admin/news',
            'modal_header' => '新增最新消息內容',
            'modal_body' => [
                [
                    'label' => '最新消息內容',
                    'tag' => 'textarea',
                    'style' => 'width:200px;height:100px;',
                    'name' => 'text',
                    // 'value'=>'請輸入文字',
                ],
            ],
        ];
        return view('modals.base_modal', $view);
    }

    public function store(Request $request)
    {
        $news = new News;
        $news->text = $request->input('text');
        $news->save();

        return redirect('/admin/news');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $news = News::find($id);
        $view = [
            'action' => '/admin/news/' . $id,
            'method' => 'patch',
            'modal_header' => '編輯最新消息內容',
            'modal_body' => [
                [
                    'label' => '最新消息內容',
                    'tag' => 'textarea',
                    'style' => 'width:200px;height:100px;',
                    'name' => 'text',
                    'value' => $news->text,
                ],
            ],
        ];
        return view('modals.base_modal', $view);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $news = News::find($id);

        if ($news->text != $request->input('text')) {
            $news->text = $request->input('text');
            $news->save();
        }


        return redirect('/admin/news');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // 後端僅做資料處理，完成後的畫面由前端自行處理
        News::destroy($id);
    }

    /**
     * 改變資料的顯示狀態
     *
     */
    public function display($id)
    {
        $news = News::find($id);

        $news->sh = ($news->sh + 1) % 2;

        $news->save();
    }
}
