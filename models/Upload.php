<?php
namespace app\models;

use Yii;
use yii\helpers\Url;

class Upload
{
	public static function download($model, $attr, $filename){
		$attr_db = $attr . '_file';
		if($model->{$attr_db}){
		    $file = Yii::getAlias('@upload/' . $model->{$attr_db});
		    
		    if (file_exists($file)) {
		        $ext = pathinfo($model->{$attr_db}, PATHINFO_EXTENSION);
		        $filename = $filename . '.' . $ext ;
		        self::sendFile($file, $filename, $ext);
		    }else{
		        echo 'file not exist';
		    }
		}else{
		    echo 'file not exist';
		}
	}
	
	public static function sendFile($file, $filename, $ext){
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-Disposition: inline; filename=" . $filename);
		header("Content-Type: " . self::mimeType($ext));
		header("Content-Length: " . filesize($file));
		header("Content-Transfer-Encoding: binary");
		readfile($file);
		exit;
	}
	
	public static function mimeType($ext){
		switch($ext){
			case 'pdf':
			$mime = 'application/pdf';
			break;
			
			case 'jpg':
			case 'jpeg':
			$mime = 'image/jpeg';
			break;
			
			case 'gif':
			$mime = 'image/gif';
			break;
			
			case 'png':
			$mime = 'image/png';
			break;
			
			default:
			$mime = '';
			break;
		}
		
		return $mime;
	}
	
	public static function showFile($model, $attr, $controller){
		$db_file = $attr . '_file';
		if($model->{$db_file}){
			//pdf Url::to('@web/images/')
			$ext = pathinfo($model->{$db_file}, PATHINFO_EXTENSION);
			if($ext == 'pdf'){
				$link = Url::to('@web/images/') . 'pdf.png';
			}else{
				$link = Url::to([$controller . '/download', 'attr' => $attr, 'id' => $model->id]);
			}
			
			return '<a href="'. Url::to([$controller . '/download', 'attr' => $attr, 'id' => $model->id]) . '" target="_blank">
			<img src="'. $link . '" width="40" /></a>';
			
			
		}
		
		
	}
	
	public static function showFilePath($model, $attr, $controller){
	    $db_file = $attr . '_file';
	    if($model->{$db_file}){
	        //pdf Url::to('@web/images/')
	        $ext = pathinfo($model->{$db_file}, PATHINFO_EXTENSION);
	        if($ext == 'pdf'){
	            $link = Url::to('@web/images/') . 'pdf.png';
	        }else{
	            $link = Url::to('@web/images/') . 'image.png';
	        }
	        
	        return '<a href="'. Url::to([$controller . '/download-path', 'attr' => $attr, 'id' => $model->id]) . '" target="_blank">
			<img src="'. $link . '" width="40" /></a>';
	        
	        
	    }
	    
	    
	}
}
