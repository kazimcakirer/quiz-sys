<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;

class AdController extends Controller
{
    //
    public function index()
    {
        $all = Ad::all();
        // dd($all);
        $cols=['動態文字廣告','顯示','編輯','刪除',];
        $rows=[];

        foreach($all as $a){
            $tmp=[
                               [
                    'tag'=>'',
                    'text'=>$a->text,
                ],
                [
                    'tag'=>'button',
                    'type'=>'button',
                    'btn_color'=>'btn-success',
                    'action'=>'show',
                    'id'=>$a->id,
                    'text'=>($a->sh==1)?'顯示':'隱藏',
                ],
                [
                    'tag'=>'button',
                    'type'=>'button',
                    'btn_color'=>'btn-info',
                    'action'=>'edit',
                    'id'=>$a->id,
                    'text'=>'編輯',
                ],
                [
                    'tag'=>'button',
                    'type'=>'button',
                    'btn_color'=>'btn-danger',
                    'action'=>'delete',
                    'id'=>$a->id,
                    'text'=>'刪除',
                ],
            ];

            $rows[]=$tmp;
        }

        // dd($rows);

        $this->view['header']='動態廣告文字管理';
        $this->view['module']='Ad';
        $this->view['cols']=$cols;
        $this->view['rows']=$rows;
        return view('backend.module', $this->view);
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //
    //     $view = [
    //         'action' => '/admin/ad',
    //         'modal_header' => '新增網站標題',
    //         'modal_body' => [
    //             [
    //                 'label' => '標題區圖片',
    //                 'tag' => 'input',
    //                 'type' => 'file',
    //                 'name' => 'img',
    //             ],
    //             [
    //                 'label' => '標題區替代文字',
    //                 'tag' => 'input',
    //                 'type' => 'text',
    //                 'name' => 'text',
    //             ],
    //         ],
    //     ];
    //     return view('modals.base_modal', $view);
    // }

    public function create()
    {
        //
        $view = [
            'action'=>'/admin/ad',
            'modal_header' => '新增動態廣告文字',
    'modal_body'=>[
        [
            'label'=>'動態廣告文字',
            'tag'=>'input',
            'type'=>'text',
            'name'=>'text',
            // 'value'=>'請輸入文字',
        ],
    ],
    ];
        return view('modals.base_modal', $view);
    }

    public function store(Request $request)
    {
            $ad = new Ad;
            $ad->text = $request->input('text');
            $ad->save();

        return redirect('/admin/ad');
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
        $ad = Ad::find($id);
        $view = [
            'action' => '/admin/ad/' . $id,
            'method' => 'patch',
            'modal_header' => '編輯動態廣告文字',
            'modal_body' => [
                [
                    'label' => '動態廣告文字',
                    'tag' => 'input',
                    'type' => 'text',
                    'name' => 'text',
                    'value' => $ad->text,
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
        $ad = Ad::find($id);

        if ($ad->text != $request->input('text')) {
            $ad->text = $request->input('text');
            $ad->save();
        }


        return redirect('/admin/ad');
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
        Ad::destroy($id);
    }

    /**
     * 改變資料的顯示狀態
     *
     */
    public function display($id)
    {
        $ad = Ad::find($id);
        
        $ad->sh=($ad->sh+1)%2;

        $ad->save();
    }

}
