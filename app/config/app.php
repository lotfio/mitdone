<?php

/**
 * MITDone PHP MVC Framework 2018 
 *
 * @author      MITDone llc <dev@mitdone.com>
 * @copyright   2018 MITDone llc
 * @license     MIT
 *
 * @link        https://mitdone.com
 *
 */

define('DS', DIRECTORY_SEPARATOR);
define('AVAIL_LANG', ["ar","en",'fr']);
define('DEF_LANG', AVAIL_LANG[1]);

define('PROTOCOL',  $_SERVER['REQUEST_SCHEME'] ?? 'http');
define('HOST',      $_SERVER['HTTP_HOST']);
//define('BASE_URI',  '/'); // if no sub directories keep this empty do not add slash / NGINX
define('BASE_URI',  '/doctortech-lotfio/'); // APACHE

define('URL', PROTOCOL ."://". HOST . "/");

// base folder
define('ROOT', dirname(dirname(__DIR__)) . DS);

// app folders
define('APP',         ROOT    . 'app'         . DS);
define('CONFIG',      APP     . 'config'      . DS);
define('CONTROLLERS', APP     . 'Controllers' . DS);
define('MODELS',      APP     . 'Models'      . DS);
define('SYS',         APP     . 'System'      . DS);
define('HELPERS',     SYS     . 'helpers'     . DS);
define('VIEWS',       APP     . 'resources'   . DS . 'views'     . DS);
define('STORAGE',     APP     .  'storage'    . DS);
define('ST_IMAGES',   STORAGE .  'images'      . DS);
define('LOG',         STORAGE .  'log'      . DS);
define('CACHE',       STORAGE .  'cache'      . DS);
define('LANG',        STORAGE .  'languages'  . DS);

// PUBLIC FOLDERS for php
define('PUB_FOLDER',    ROOT           . 'public' . DS);
define('ASSETS_FOLDER', PUB_FOLDER     . 'assets' . DS);
define('CSS_FOLDER',    ASSETS_FOLDER  . 'css'    . DS);
define('JS_FOLDER',     ASSETS_FOLDER  . 'js'     . DS);
define('IMG_FOLDER',    ASSETS_FOLDER  . 'img'    . DS);

define('PHP_UP_FILE', PUB_FOLDER  . 'uploads' . DS .  'files' . DS);
define('PHP_UP_IMG',  PUB_FOLDER  . 'uploads' . DS . 'img' . DS);

// public folders http
define('PUB',    URL      . 'public'   . DS);
define('ASSETS', URL      . BASE_URI   . 'assets' . DS);
define('CSS',    ASSETS   . 'css'      . DS);
define('JS',     ASSETS   . 'js'       . DS);
define('img',    ASSETS   . 'img'      . DS);

define('UPLOADS',  URL      . BASE_URI . 'uploads'. DS);
define('UP_FILES', UPLOADS  . 'files' . DS);
define('UP_IMG',   UPLOADS  . 'img'   . DS);

// security
define('CSRF', "__CSRF");
define('AUTH_SESS_NAME', 'auth');


define('Support', 'https://www.mitdone.com/support/projects/1000000478145');

define('log_file', LOG . 'v3.json');
define('PROJECT_HELP_CODE', '1000000478145');
// SMS config
define('Moblie_SMS', 'https://mobily.ws/api/msgSend.php?' );
define('SMS_USER', '966557243434' );
define('SMS_PASS', 'asd147' );
define('APP_NAME_EN', 'DoctorTec' );
define('SEND_CODE_DELAY', 10 * 60);
define('COUNTY_CODE', 966);
// app config
define('Root', BASE_URI);
define('UPLOAD_DIR_IMAGE', ST_IMAGES);
define('KEY_HOLDER',"KEY_HOLDER");
define('MAX_SIZE', 8 * 1024 * 1024);
define('MAX_LAVEL', 8);  //Admin Access Lavel
define('INSTALL', FALSE); // 1 for install the tabels in database and trun it back to 0
define('CLEAR', FALSE); // 1 for clear tables whene you reinstall . just keep it 1
define('ImagesRoot', "http://" . $_SERVER['HTTP_HOST'] . "/panel/admin/api/v2/sheared/files/images/" );
const Allowed =  ['user','sheared'] ;
const RequireLoginMethods =  ['orders','info','order','chat','buy','likepost','comment','rate','cancelorder','update','Transactions','communcate','devices','token','notifications','finishjob','resend','token','changephone','join','info','addoffer','code'];

define('MAX_IN_PAGE', 10);
define('PRIVET_PATH', './app/storage/privet/');
define('APP_NAME', 'دكتور تيك'); // App Name
define('NEW_OFFER_NOTIC', ' هناك عرض جديد على طلبك من طرف ');
define('ACCEPT_OFFER_NOTIC', ' تم قبول عرضك لخدمة صيانة . من طرف المستخدم ');
define('DELEVERED_ORDER_NOTIC', 'تم إكمال طلب الصيانة من طرف * يمكنك تقيم الخدمة الان');
define('APPROVED_NOTIC', 'تم قبول طلبك للإنضمام للمهندسين . يمكنك البدأ بتقديم خدماتك الأن .  يرجى أعادة تسجيل دخولك للتطبيق');
define('NEW_ORDER_NOTIC', 'يوجد طلب صيانة جديد . يبعد عنك العميل حوالى ');
define('RATE_NOTIC_ENG', 'تم تقيم خدمتك ب * نجوم من طرف العميل #');
define('RATE_NOTIC_USER', 'تم تقيمك ب * نجوم من طرف المهندس #');
define('ADDED_AMOUNT', 'تم تحديث حالة المطالبات المالية بتسديد * ريال ');
define('KM_UINT', 'كيلو متر');
define('M_UINT', 'متر');
define('API_ACCESS_KEY', 'AIzaSyBNz2ea2uBehqSXKHpKpbjzcgegHol54YE' );
define('API_NOTIFICATION_KEY', 'AAAAhaTHTaM:APA91bFRfiacA2cLmppisQoD5EC0QysI-OyqSNcvEc391OrTWhLKaSjCrU19kriCx2d3w2dimP_WbXLZoXXGDA-1adkKCjLsVXx8IAUkbPs-rC6XyT0sElscVM0swH_tCe2txvhfNdkT' );
define('NOTIC_URL', 'https://fcm.googleapis.com/fcm/send');


define('DELETE_USER', '0'); // access key aver change
define('ADD_FOUNDS', '1');// access key aver change
define('ADD_ADMIN', '2');// access key aver change
define('CHANGESETTINGS', '3'); // access key aver change
define('POST', '4'); // access key aver change
define('STORE_MANAGER', '5'); // access key aver change
define('ADMIN_CHANGE', '6'); // access key aver change
define('PHONES', '7');
define('APPROV_REQUEST', '8');
define('CHANGE_DISTANCE', '9');
define('ORDERS', '10');
define('MASSAGING', '11');