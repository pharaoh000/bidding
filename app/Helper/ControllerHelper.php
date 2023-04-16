<?php 

namespace App\Helpers;

use File;
use Location\Coordinate;
use Location\Distance\Vincenty;
use Setting;
use Illuminate\Support\Facades\Mail;
use App\WalletRequests;

class Helper
{

    public static function upload_picture($picture)
    {
        $file_name = time();
        $file_name .= rand();
        $file_name = sha1($file_name);
        if ($picture) {
            $ext = $picture->getClientOriginalExtension();
            $picture->move(public_path() . "/uploads", $file_name . "." . $ext);
            $local_url = $file_name . "." . $ext;

            $s3_url = url('/').'/uploads/'.$local_url;
            
            return $s3_url;
        }
        return "";
    }


    public static function delete_picture($picture) {
        File::delete( public_path() . "/uploads/" . basename($picture));
        return true;
    }

    public static function generate_booking_id() {
        return Setting::get('booking_prefix').mt_rand(100000, 999999);
    }

    public static function site_sendmail($user){

        $site_details=Setting::all();

        Mail::send('emails.invoice', ['Email' => $user], function ($mail) use ($user,$site_details) {
           
            //$mail->to('tamilvanan@blockchainappfactory.com')->subject('Invoice');

            $mail->to($user->user->email, $user->user->first_name.' '.$user->user->last_name)->subject('Invoice');
        });

        /*if( count(Mail::failures()) > 0 ) {

           echo "There was one or more failures. They were: <br />";

           foreach(Mail::failures() as $email_address) {
               echo " - $email_address <br />";
            }

        } else {
            echo "No errors, all sent successfully!";
        }*/

        return true;
    }

    public static function site_registermail($user){

        $site_details=Setting::all();
        
        Mail::send('emails.welcome', ['user' => $user], function ($mail) use ($user) {
           // $mail->from('harapriya@appoets.com', 'Your Application');

            //$mail->to('tamilvanan@blockchainappfactory.com')->subject('Invoice');

            $mail->to($user->email, $user->first_name.' '.$user->last_name)->subject('Welcome');
        });

        return true;
    }

    public function formatPagination($pageobj){

        $results = new \stdClass();

        $results->links=$pageobj->links();
        $results->count=$pageobj->count();
        $results->currentPage=$pageobj->currentPage();
        $results->firstItem=$pageobj->firstItem();
        $results->hasMorePages=$pageobj->hasMorePages();
        $results->lastItem=$pageobj->lastItem();
        $results->lastPage=$pageobj->lastPage();
        $results->nextPageUrl=$pageobj->nextPageUrl();
        $results->perPage=$pageobj->perPage();
        $results->previousPageUrl=$pageobj->previousPageUrl();
        $results->total=$pageobj->total();
        //$results->url=$pageobj->url();  

        return $results;
    }

    public static function generate_request_id($type) {

        if($type=='provider'){
            $tr_str='PSET';
        }
        else{
            $tr_str='FSET';
        }

        $typecount=WalletRequests::where('request_from',$type)->count();

        if(!empty($typecount))
            $next_id=$typecount+1;
        else
            $next_id=1;

        $alias_id=$tr_str.str_pad($next_id, 6, 0, STR_PAD_LEFT); 
            
        return $alias_id;

    }

    public static function calculateDistanceBetweenCoordinates($coordinateOne, $coordinateTwo) {
	    $coordinate1 = new Coordinate( $coordinateOne['lat'], $coordinateOne['lon'] );
	    /** Set Distance Calculation Source Coordinates ****/
	    $coordinate2 = new Coordinate( $coordinateTwo['lat'], $coordinateTwo['lon'] );
	    /** Set Distance calculation Destination Coordinates ****/
	    $calculator = new Vincenty();
	    /***Distance between two coordinates using spherical algorithm (library as mjaschen/phpgeo) ***/
	    $myDistance = $calculator->getDistance( $coordinate1, $coordinate2 );
	    return round( $myDistance );
    }

}
