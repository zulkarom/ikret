<?php
/* $path = "C:\millions\8.1.25\htdocs\projects8.1\icreate\ikret";


$files = array_diff(scandir($path), array('.', '..'));
print_r($files); */

//echo '<pre>';
//showFiles($dir = '.');


/* $test_path='./views/goo';
echo basename($test_path);

die(); */

$path = '.';
showFiles($path, true, ' -');




function showFiles($path, $nofilter, $dash){
    $arr_folders = ['controllers', 'models', 'assets', 'views', 'web', 'committee',
'layouts',
'program',
'program-registration',
'session',
'site',
'user', 'qrscanner'];
    if(is_dir($path)){
        $dir = basename($path);
        //echo $dir;
        if($nofilter || in_array($dir,$arr_folders)){
            
            if ($handle = opendir($path)) {
    
                while (false !== ($entry = readdir($handle))) {
            
                    if ($entry != "." && $entry != "..") {
                        
                        
                        //show content
                        $ext  = pathinfo($entry, PATHINFO_EXTENSION);
                        //echo '<br />...........'.$ext;
                        if($ext == 'php'){
                            echo '<br /><h2>'.$dash.$entry . '</h2>';
                            echo '<table border="1"><tr><td>';
                            echo '<pre>';
                            echo htmlentities(file_get_contents($path.'/'.$entry));
                            echo '</pre>';
                            echo '</td></tr></table>';
                        }
                        showFiles($path.'/'.$entry, false, $dash.'-');
            
                    }
                }
            
                closedir($handle);
            }
        }
    
    }else{
       // echo basename($path);
    }
}