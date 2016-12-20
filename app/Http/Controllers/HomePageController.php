<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ContactPostRequest;
use Mail;

class HomePageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('homepage.welcome');
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
     * Send email from user to administrator.
     *
     * @param  name, email, content
     */
     public function contactus(ContactPostRequest $request)
     {
        Mail::send('mail', ['name' => $request['name'], 'content' => $request['content']], function ($message) use ($request){
            
            $message->from($request['email'], $request['name']);

            $message->to('40243137@gm.nfu.edu.tw')->subject('空塵計聯絡信');
        });

        $isdisplay = false;

        return redirect('/');
     }

}
