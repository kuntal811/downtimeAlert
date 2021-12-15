<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Process any number of cURL requests in parallel, but limit
 * the number of simultaneous requests to $parallel.
 *
 * @param array $urls          Array with URLs to process
 * @param int   $parallel      Number of concurrent requests
 * @param array $extraOptions  User defined CURLOPTS
 * @return array[]
 */
function parallelCurl($urls = [], $parallel = 10, $extraOptions = []) {

    // $extraOptions override the hardcoded ones.
    $options = $extraOptions + [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_HEADER         => 1,
        CURLOPT_NOBODY         => true,
    ];

    // The curl_multi handle.
    $mh = curl_multi_init();

    // Array with curl handles.
    $chs = [];

    // Create the individual curl handles and set options.
    foreach ($urls as $key => $url) {
        $chs[$key] = curl_init($url);
        curl_setopt_array($chs[$key], $options);
    }

    $curls = $chs;
    $open = null;

    // Perform the requests requests & dynamically (re)fill available slots
    // up to the specified limit ($parallel) until all urls are processed.
    while (0 < $open || 0 < count($curls)) {
        if ($open < $parallel && 0 < count($curls)) {
            curl_multi_add_handle($mh, array_shift($curls));
        }

        curl_multi_exec($mh, $open);
        usleep(11111);
    }

    // Extract downloaded data from curl handle.
    foreach ($chs as $key => $ch) {
        $res[$key]['info'] = curl_getinfo($ch);
        $response = curl_multi_getcontent($ch);

        // Separate response header & body.
        $res[$key]['head'] = substr($response, 0, $res[$key]['info']['header_size']);
        $res[$key]['body'] = substr($response, $res[$key]['info']['header_size']);

        curl_multi_remove_handle($mh, $ch);
    }

    // Close the curl_multi handle.
    curl_multi_close($mh);

    // Finally return all results.
    //echo "result";
    return isset($res) ? $res : [];
}






function fetch_ssl_details($url){
    static $call_count = 0;
    $call_count += 1;

    $agent = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_8; pt-pt) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27";

    // initializes curl session
    $ch = curl_init();
    $option = [
        // sets the URL to fetch
        CURLOPT_URL             => $url,
        CURLOPT_CERTINFO        => true,
        
        // sets the content of the User-Agent header
        CURLOPT_USERAGENT       => $agent,
        
        // make sure you only check the header - taken from the answer above
        CURLOPT_NOBODY          => true,
        

        // follow "Location: " redirects
        CURLOPT_FOLLOWLOCATION  => true,

        // return the transfer as a string
        CURLOPT_RETURNTRANSFER  => 1,

        // disable output verbose information
        CURLOPT_VERBOSE         => false,

        // max number of seconds to allow cURL function to execute
        CURLOPT_TIMEOUT         => 5
    
    ];
    // set option
    curl_setopt_array($ch, $option);
    // execute
    curl_exec($ch);


    //print_r(curl_getinfo($ch));
    $regex = "/O = [a-zA-Z0-9\'\",\s.]*[CN|OU]/";
    
    if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200){
        try{
            if(curl_getinfo($ch)['certinfo'][0]['Issuer'])
                preg_match($regex,  curl_getinfo($ch)['certinfo'][0]['Issuer'], $matches);

            if($matches){

                $filter1 = preg_replace("/^O = /", "", $matches[0]);
                $filter2 = preg_replace("/, CN$/", "", $filter1);
                $filter3 = preg_replace("/, OU$/", "", $filter2);
        
        
                /*
                print_r($filter3);
        
                print_r(curl_getinfo($ch)['certinfo'][0]['Signature Algorithm']);
        
                print_r(curl_getinfo($ch)['certinfo'][0]['Start date']);
        
                print_r(curl_getinfo($ch)['certinfo'][0]['Expire date']);
        
                curl_close($ch);
                */
        
                if(curl_getinfo($ch)['certinfo'][0]['Start date'])
                    $start_date = new DateTime( curl_getinfo($ch)['certinfo'][0]['Start date']  );
                if(curl_getinfo($ch)['certinfo'][0]['Expire date'])
                    $end_date = new DateTime( curl_getinfo($ch)['certinfo'][0]['Expire date']  );
        
                $ssl_details = [
                    'issuer'    =>  $filter3,
                    'algorithm' =>  curl_getinfo($ch)['certinfo'][0]['Signature Algorithm'],
                    'start_date'=>  $start_date->format('Y-m-d') ,
                    'end_date'  =>  $end_date->format('Y-m-d') ,
                ];
        
                return $ssl_details;
            }
        }catch(Exception $e){
            return false;
        }
    }else{
        if($call_count < 4)
            fetch_ssl_details($url); //retry to fetch data

        return false;
    }
    

}





function get_domain_details($url){

    // $url should not contain protocol(i.e. http, https)
    $ch = curl_init('https://who.is/whois/'.$url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);


    $name_pattern = "/(Name<\/div>\s*<div class=\"col-md-8 queryResponseBodyValue\">)[a-zA-Z0-9.,\s]*(<\/div>)/";
    $pattern="/\d{4}-\d{2}-\d{2}/";
    //$pattern = "/(queryResponseBodyValue\">)[a-zA-Z0-9,-.:/\s]*(</div>)/";
    $dates;
    $organization;
    preg_match_all($pattern,$response,$dates);
    preg_match($name_pattern,$response,$organization);
    $filter1 = preg_replace("/(Name<\/div>\s*<div class=\"col-md-8 queryResponseBodyValue\">)/", "", $organization);
    $filter2 = preg_replace("/<\/div>/","",$filter1);

    $response=[
        'organization'      =>  array_key_exists(0, $filter2)?$filter2[0]  :NULL,
        'expire_on'         =>  array_key_exists(0, $dates) ? (array_key_exists(0, $dates[0])?$dates[0][0] : NULL):NULL,
        'registered_on'     =>  array_key_exists(0, $dates) ? (array_key_exists(1, $dates[0])?$dates[0][0] : NULL):NULL,
        'updated_on'        =>  array_key_exists(0, $dates) ? (array_key_exists(2, $dates[0])?$dates[0][0] : NULL):NULL,
    ];
    
    return $response;
}

?>