<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CsvData;
use App\Http\Requests\CsvImportRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use mysql_xdevapi\Exception;

class ImportController extends Controller
{

    public function getImport()
    {
        return view('import');
    }

    public function parseImport(CsvImportRequest $request)
    {

        $path = $request->file('csv_file')->getRealPath();

        if ($request->has('header')) {
            $data = Excel::load($path, function($reader) {})->get()->toArray();
        } else {
            $data = array_map('str_getcsv', file($path));
        }

        if (count($data) > 0) {
                if ($request->has('header')) {
                $csv_header_fields = [];
                foreach ($data[0] as $key => $value) {
                    $csv_header_fields[] = $key;
                }
            }
            $csv_data = array_slice($data, 0, 2);

            $csv_data_file = CsvData::create([
                'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                'csv_header' => $request->has('header'),
                'csv_data' => json_encode($data),
                'csv_status' => 1
            ]);
        } else {
            return redirect()->back();
        }

        return view('import_fields', compact( 'csv_header_fields', 'csv_data', 'csv_data_file'));

    }

    public function processImport(Request $request)
    {
        $data = CsvData::find($request->csv_data_file_id);
        $csv_data = json_decode($data->csv_data, true);
        foreach ($csv_data as $row) {
            $contact = new Contact();
            $error = [];
            $creditCardNumber="";
            foreach (config('app.db_fields') as $index => $field) {
                $inter = array_flip($request->fields);
                if ($data->csv_header) {
                    switch ($field){
                        case "name":
                            $contact->$field = self::evalName($row[$inter[$field]],$error);
                            break;
                        case "birthdate":
                            $contact->$field = self::evalBirthdate($row[$inter[$field]],$error);
                            break;
                        case "phone":
                            $contact->$field = self::evalPhone($row[$inter[$field]],$error);
                            break;
                        case "address":
                            $contact->$field = self::evalAddress($row[$inter[$field]],$error);
                            break;
                        case "credit_card":
                            $creditCardNumber = $contact->$field = $row[$inter[$field]];
                            break;
                        case "email":
                            $contact->$field = self::evalEmail($row[$inter[$field]],$error);
                            break;
                        default:
                            $contact->$field = $row[$inter[$field]];
                            break;
                    }
                } else {
                    if($field=="birthdate"){
                        $contact->$field = self::evalBirthdate($row[$inter[$field]]);
                    }else{
                        $contact->$field = $row[$inter[$index]];
                    }
                }
            }

            $contact->csv_data_id = $request->csv_data_file_id;
            $contact->credit_card = $creditCardNumber?self::cryptCreditCardNumber($creditCardNumber):'';
            $contact->franchise = self::evalFranchise($creditCardNumber, $error);
            $contact->status = count($error)?2:1;
            $contact->error = implode(' | ',$error);
            $contact->user_id = auth()->user()->id;
            $contact->save();

            CsvData::where('id', $request->csv_data_file_id)->update(array('csv_status' => 2));
        }

        \DB::table('csv_data as f')
            ->join('contacts as c', 'c.csv_data_id', '=', 'f.id')
            ->where('C.status', 2)
            ->update(['f.csv_status' => '3']);

        \DB::table('csv_data as f')
            ->join('contacts as c', 'c.csv_data_id', '=', 'f.id')
            ->where('C.status',1)
            ->update(['f.csv_status' => '4']);

        return view('import_success');
    }

    private function cryptCreditCardNumber($creditCardNumber){
        $lastFourNumbers = substr($creditCardNumber, -4);
        return md5(substr($creditCardNumber,0,-4)).$lastFourNumbers;

    }

    private function evalBirthdate($row, &$error){
        $format1 = 'Y-m-d';
        $format2 = 'Ymd';
        if(empty($row)){
            $error[] = "Birthdate is empty";
            return $row;
        }else{
            $d1 = \DateTime::createFromFormat($format1, $row);
            $d2 = \DateTime::createFromFormat($format2, $row);
            if (!(($d1 && $d1->format($format1) == $row) || ($d2 && $d2->format($format2) == $row)) ){
                $error[] = "Date format not valid";
            }
            return $row;
        }
    }

    private function evalName($row, &$error){
        if(empty($row)){
            $error[] = "Name is empty";
            return '';
        }
        if(!preg_match("/^(?=.{3,120}$)[a-zA-Z](\s?[a-zA-Z-])*$/", trim($row))){
            $error[] = "Name format not valid";
        }
        return $row;
    }

    private function evalPhone($row, &$error){

        if(!preg_match("/^([0-9\s\-\+\(\)]*)$/", trim($row))){
            $error[] = "Phone number format not valid";
        }
        if(empty($row)){
            $error[] = "Phone number is empty";
            $row='';
        }
        return $row;
    }


    private function evalAddress($row, &$error){

        if(trim($row)==''){
            $error[] = "Address is empty";
            $row = " ";
        }
        return $row;
    }

    private function evalFranchise($creditCardNumber, &$error){
        $franchise = "";
        if(!empty($creditCardNumber)){
            $franchise = self::checkCreditCardNumber($creditCardNumber);
        }
        if(empty($franchise)){
            $error[] = "Credit Card Number not valid";
        }
        return $franchise;
    }

    function checkCreditCardNumber($cc, $extra_check = false){
        try {
            $cards = array(
                "visa" => "(4\d{12}(?:\d{3})?)",
                "amex" => "(3[47]\d{13})",
                "jcb" => "(35[2-8][89]\d\d\d{10})",
                "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",
                "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",
                "mastercard" => "(5[1-5]\d{14})",
                "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",
                "discover" => "/^6(?:011\d{12}|5\d{14}|4[4-9]\d{13}|22(?:1(?:2[6-9]|[3-9]\d)|[2-8]\d{2}|9(?:[01]\d|2[0-5]))\d{10})$/",

                "bankcard" => "^(?:4[0-9]{12}(?:[0-9]{3})?|[25][1-7][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$",
                "china-unionpay" => "^(62[0-9]{14,17})$",
                "visa-electron" => "^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14})$",
                "diners-club-carte-blanche" => "^3(?:0[0-5]|[68][0-9])[0-9]{11}$",
                "diners-club-international" => "^3(?:0[0-5]|[68][0-9])[0-9]{11}$",
                "diners-club-enroute" => "^3(?:0[0-5]|[68][0-9])[0-9]{11}$",
                "diners-club-us-ca" => "^3(?:0[0-5]|[68][0-9])[0-9]{11}$",
                "instapayment" => "^63[7-9][0-9]{13}$",
                "laser" => "^(6304|6706|6709|6771)[0-9]{12,15}$",
                "solo" => "^(6334|6767)[0-9]{12}|(6334|6767)[0-9]{14}|(6334|6767)[0-9]{15}$",
            );
            $names = array(
                "Visa",
                "American Express",
                "JCB",
                "Maestro",
                "Solo",
                "Mastercard",
                "Switch",
                "Discover",

                "Bankcard",
                "China-unionpay",
                "Visa-electron",
                "Diners-club-carte-blanche",
                "Diners-club-international",
                "Diners-club-enroute",
                "Diners-club-us-ca",
                "Instapayment",
                "Laser",
                "Solo",
            );
            $matches = array();
            $pattern = "#^(?:" . implode("|", $cards) . ")$#";
            $result = preg_match($pattern, str_replace(" ", "", $cc) , $matches);
            if ($extra_check && $result > 0)
            {
                $result = (validatecard($cc)) ? 1 : 0;
            }
            return ($result > 0) ? $names[sizeof($matches) - 2] : 'No found';
        }catch (\Exception $e){
            return 'Not found';
        }
    }

    function evalEmail($row, &$error)
    {
        $result = (false !== filter_var($row, FILTER_VALIDATE_EMAIL));

        if ($result)
        {
            list($user, $domain) = explode('@', $row);
            $result = checkdnsrr($domain, 'MX');
        }else{
            $error[] = "Email format not valid";
        }
        if(empty($row)){
            $error[] = "Email is empty";
            $row='';
        }

        return $row;
    }

}
