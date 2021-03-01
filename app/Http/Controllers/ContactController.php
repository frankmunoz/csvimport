<?php

namespace App\Http\Controllers;

use App\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(){
        //$contact = \DB::table('contacts')->select('id','name','birthdate','phone','address','credit_card','franchise','email')->get();
        $contacts = Contact::paginate(env('PAGE_SIZE'));
        return view('contact.index', compact('contacts') );
    }

}
