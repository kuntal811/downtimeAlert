export function imageExists(image_url){

    var http = new XMLHttpRequest();
    
    http.open('HEAD', image_url, true);
    http.setRequestHeader("Access-Control-Allow-Origin", "*")
    http.send();
    return http.status !== 404;

}