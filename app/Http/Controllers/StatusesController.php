<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Auth;

class StatusesController extends Controller
{
    //该控制器内的方法都需登录后才可访问
    public function __construct()
    {
        $this->middleware('auth');
    }

    //创建微博功能
    public function store(Request $request)
    {
        $this->validate($request,[
            'content' => 'required|max:140'
        ]);

        //创建微博
        Auth::user()->statuses()->create([
            'content' => $request['content']
        ]);
        return redirect()->back();
    }

    //删除微博功能
    public function destroy(Status $status)
    {
        //删除授权验证，不通过则抛403异常
        $this->authorize('destroy',$status);
        //调用 Eloquent 模型的 delete 方法对该微博进行删除
        $status->delete();
        session()->flash('success','微博已被成功删除!');
        return redirect()->back();
    }
}
