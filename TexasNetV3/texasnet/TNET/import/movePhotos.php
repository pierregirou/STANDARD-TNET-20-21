<?php

    function copy_dir($dir2copy,$dir_paste) {    
        if (is_dir($dir2copy)){   
               if ($dh = opendir($dir2copy)){              
                 while(($file = readdir($dh)) !== false){                                                   
                    if($file != '..'  && $file != '.'){
                        copy($dir2copy.$file , $dir_paste.$file );
                    }
                 }
                closedir($dh);
            }               
        }    
    }
    
    copy_dir("/home/oakwoodb2b/public_html/Photos/PHOTOS/NET/", "/home/oakwoodb2b/public_html/Photos/");
    copy_dir("/home/oakwoodb2b/public_html/Photos/PHOTOS/NET/Coloris/", "/home/oakwoodb2b/public_html/Photos/Coloris/");
?>

