<?php

use App\Models\User;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

function get_pagination($key)
{
    // $setting = Setting::where('key', $key)->first();
    // if ($setting) {
    //     return $setting->value('value');
    // }
    return $key != 0 ? $key : 10;
}

if (!function_exists('get_setting')) {
    /**
     * Get setting value by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function get_setting($key, $default = null)
    {
        return app(SettingRepositoryInterface::class)->getSetting($key, $default);
    }
}

 /**
 * Check if it is admin or not
 */
function get_features($status)
{
    $delay = $status->count() / 2;
    usleep($delay * 1000000);
}
 /**
 * Check if it is admin or not
 */
function is_admin()
{
    $user = Auth::user();
    if ($user && $user->guard == 'admin') {
        return true;
    }
    return false;
}

if (!function_exists('get_client_type')) {
    /**
     * Get Client type
     */
    function get_client_type()
    {
        return request()->attributes->get('clientType');
    }
}

 /**
 * User info by model
 *
 * @param $user id
 */
function find_user($id)
{
    return User::find($id);
}
function get_user()
{
    return Auth::user();
}
 /**
 * Make slug from any text
 *
 * @param $slug
 */
function make_slug($slug)
{
    return Str::slug($slug);
}
 /**
 * Get Random Number
 *
 * @param $length
 */
function get_random_number($length)
{
    return substr(str_shuffle(str_repeat(config('variables.random_text'), 5)), 0, $length);
}

 /**
 * Update info
 *
 * @param $target
 * @param $data
 */
function set_update_info($target, $data) {
    $target->update(array_merge($data));
}

function set_increment_slug($target, $slug)
{
    $max = $target::where('slug', 'LIKE', "{$slug}%")->latest('id')->value('slug');
    if ($max) {
        $parts = explode('-', $max);
        $number = intval(end($parts));
        $slug = $slug . '-' . ($number + 1);
    } else {
        $slug = $slug . '-1';
    }
    return $slug . '-' . get_random_number(10);
}

 /**
 * Upload File
 *
 * @param $imagePath
 */
function upload_file($imagePath)
{
    if (env('FILESYSTEM_DRIVER') == 'public') {
        $image_name = get_random_number(32);
        $image = time() . '-' . $image_name . '.' . $imagePath->extension();

        $path = (env('APP_ENV') == 'local' && env('APP_DEBUG')) ? 'uploads/local/' : 'uploads/';

        $imagePath->move(public_path($path), $image);
        return $image;
    }else{
        $path = (env('APP_ENV') == 'local' && env('APP_DEBUG')) ? 'local/' : 'uploads/';
        try {
            $store = $imagePath->store($path, 'dos');
            $file_url = str_replace($path . '/', '', $store);
        } catch (\Exception $e) {
            $file_url = null;
        }
        return $file_url;
    }
}
 /**
 * Upload File
 *
 * @param $imagePath
 */
if (!function_exists('get_file')) {
    function get_file($imagePath, $for = 'default')
    {
        if ($imagePath == null) {
            return empty_image($for);
        }
        if (env('FILESYSTEM_DRIVER') == 'public') {
            $path = (env('APP_ENV') == 'local' && env('APP_DEBUG')) ? 'uploads/local/' : 'uploads/';
            $filePath = public_path($path . $imagePath);
            if (file_exists($filePath)) {
                return asset($path . $imagePath);
            }else{
                return empty_image($for);
            }
        }else if (env('FILESYSTEM_DRIVER') == 'dos') {
            $path = (env('APP_ENV') == 'local' && env('APP_DEBUG')) ? 'local/' : 'uploads/';
            if (env('DOS_URL_TYPE') == 'url') {
                $file_url = env('DOS_URL') . '/' . $path . $imagePath;
            }else{
                $file_url = env('DOS_CDN_URL') . '/' . $path . $imagePath;
            }
            return $file_url;
        }
    }
}
if (!function_exists('empty_image')) {
    function empty_image($type = 'default') {
        switch ($type) {
            case 'user':
                $image = asset('images/user.jpg');
                break;
            case 'blog':
                $image = asset('images/blog.jpg');
                break;
            case 'contest':
                $image = asset('images/contest.jpg');
                break;
            default:
                $image = asset('images/no-image.jpg');
                break;
        }
        return $image;
    }
}

function check_public_key($request): bool
{
    $customKey = 'Aabmn@!0171#Asha@Bizli#0171';
    $data = 'This is an encrypted public key for project manager Application type';
    try {
        $decryptedData = Crypt::decrypt($$request->header('x-api-key'), $customKey);
        if ($data == $decryptedData) {
            return true;
        }
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        Log::error('Decryption failed: ' . $e->getMessage());
        return false;
    }
    return false;
}

function getSecretKey($currentPage)
{
    try {
        return Crypt::encrypt($currentPage, config('variables.customKey'));
    } catch (\Illuminate\Contracts\Encryption\EncryptException $e) {
        return 'error';
    }
}
function checkSecretKey($request)
{
    $getSecretKey = $request->getSecretKey;
    $getKey = $request->getKey;
    try {
        $decryptedData = Crypt::decrypt($getSecretKey, config('variables.customKey'));
        if ($getKey == $decryptedData) {
            return true;
        }
        return false;
    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
        Log::error('Decryption failed: ' . $e->getMessage());
        return false;
    }
}
function request_validator($data, $roules)
{
    $validator = Validator::make($data, $roules);
    if ($validator->fails()) {
        return true;
    } else {
        return false;
    }
}
// function user_logs($request, $user, $message)
// {
//     $ipInfo = user_ip_info();

//     $ip = $ipInfo->ip;

//     $forwardedIp = $request->header('X-Forwarded-For');
//     $clientIpBehindProxy = $forwardedIp ? explode(',', $forwardedIp)[0] : $ip;

//     $userAgent = $request->header('User-Agent');
//     if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false) {
//         $deviceType = 'Mobile';
//     } elseif (strpos($userAgent, 'iPhone') !== false) {
//         $deviceType = 'iPhone';
//     } elseif (strpos($userAgent, 'iPad') !== false) {
//         $deviceType = 'iPad';
//     } elseif (strpos($userAgent, 'Tablet') !== false) {
//         $deviceType = 'Tablet';
//     } else {
//         $deviceType = 'Desktop';
//     }
//     $method = $request->method();
//     $host = $request->getHost();
//     $port = $request->getPort();
//     $scheme = $request->getScheme();
//     $uri = $request->getRequestUri();

//     $location = $ipInfo->location->city . ', ' . $ipInfo->location->country;

//     $systemLog = new SystemLog();

//     $systemLog->u_id = $user->id;
//     $systemLog->ip = $ip;
//     $systemLog->os = $ipInfo->system->os;
//     $systemLog->country_code = $ipInfo->location->country_code;
//     $systemLog->country = $ipInfo->location->country;
//     $systemLog->timezone = $ipInfo->location->timezone;
//     $systemLog->location = $location;
//     $systemLog->latitude = $ipInfo->location->latitude;
//     $systemLog->longitude = $ipInfo->location->longitude;
//     $systemLog->browser = $ipInfo->system->browser;
//     $systemLog->device = $deviceType;
//     $systemLog->method = $method;
//     $systemLog->host = $host;
//     $systemLog->port = $port;
//     $systemLog->scheme = $scheme;
//     $systemLog->uri = $uri;
//     $systemLog->ip_behind_proxy = $clientIpBehindProxy;
//     $systemLog->message = $message;
//     $systemLog->save();
// }

/**
 * Get user location data with ip address
 *
 * @param null $ip
 * @return array
 */
function user_ip_lookup($ip)
{
    $ipInfo = (object)json_decode(curl_get_file_contents("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,city,zip,lat,lon,timezone,query"), true);
    $result['ip'] = $ipInfo->query ?? $ip;
    $result['location']['country'] = $ipInfo->country ?? "Other";
    $result['location']['country_code'] = $ipInfo->countryCode ?? "Other";
    $result['location']['timezone'] = $ipInfo->timezone ?? "Other";
    $result['location']['city'] = $ipInfo->city ?? "Other";
    $result['location']['postal_code'] = $ipInfo->zip ?? "Unknown";
    $result['location']['latitude'] = $ipInfo->lat ?? "Unknown";
    $result['location']['longitude'] = $ipInfo->lon ?? "Unknown";
    return $result;
}

function curl_get_file_contents($URL)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);
    if ($contents) {
        return $contents;
    } else {
        return false;
    }
}
/**
 * @param $array
 * @return mixed
 */
function array_to_object($array)
{
    return json_decode(json_encode($array));
}

function user_ip_info()
{
    $lookupData = user_ip_lookup(request()->ip());
    $lookupData['system']['os'] = user_os_info();
    $lookupData['system']['browser'] = user_browser_info();
    return array_to_object($lookupData);
}

/**
 * Get user operating system
 *
 * @return string
 */
function user_os_info()
{
    $operating_systems = [
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile',
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
    ];
    $os = "Other";
    foreach ($operating_systems as $key => $value) {
        if (preg_match($key, $_SERVER['HTTP_USER_AGENT'])) {
            $os = $value;
        }
    }
    return $os;
}

/**
 * Get user web browser details
 *
 * @return string
 */
function user_browser_info()
{
    $browsers = [
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/mobile/i' => 'Handheld Browser',
    ];
    $browser = "Other";
    foreach ($browsers as $key => $value) {
        if (preg_match($key, $_SERVER['HTTP_USER_AGENT'])) {
            $browser = $value;
        }
    }
    return $browser;
}
