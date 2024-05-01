<?php
namespace common\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\Html;
/**
 * Class Menu
 * Theme menu widget.
 */
class Menu_crypto
{
	public $items;
	public $tree = false;
	
	public function __construct($items = [])
	{
		$this->items = $items;
	}

   public static function widget($items){
	   
	   $widget =  new self($items);
	   return $widget->menus();
		
	}
   
   public function menus(){
	   $html = '';  
	   foreach($this->items as $item){
			   switch($item['level']){
				   case 0:
				   $html .= '<li class="nav-title">'.$item['label'].'</li>';
				   break;
				   
				   case 1:
				   $html .= $this->item1($item);
				   break;
				   
				   case 2:
				   $html .= $this->item2($item);
				   
			   }
	   }
			   
	return $html;
   }
   
   protected function item1($item){
	   $active = $this->isItemActive($item) ? 'active' : '';
	   return '<li>
				   <a href="'.Url::to($item['url']).'" class="'.$active.'">
				   <div class="nav_icon_small"><img src="'.$item['icon'].'" alt=""></div>
				   <div class="nav_title">
				   <span>
					'.$item['label'].'
					</span>
				   </div></a></li>';
   }

   protected function children($item){
	   $active = $this->isItemActive($item) ? 'active' : '';
	   return '<li>
				   <a href="'.Url::to($item['url']).'" class="'.$active.'">
				   '.$item['label'].'
				   </a></li>';
   }
   
   protected function item2($item){
	   $this->tree = false;
	   $anak = '';
	   $expand = $this->isItemActive($item) ? 'true' : 'false';
	   $mm = $this->isItemActive($item) ? 'mm-show' : '';
	   $active = $this->isItemActive($item) ? 'mm-active' : '';
	   $children = $item['children'];
			if($children){
				foreach($children as $child){
					$anak .= $this->children($child);
				}
			}
	   
	   $html =  '<li class="'.$active.'">
			<a href="#" class="has-arrow '.$active.'" aria-expanded="'.$expand.'">
				<div class="nav_icon_small"><img src="'.$item['icon'].'" alt=""></div>
				<div class="nav_title">
				   <span>
					'.$item['label'].'
					</span>
				</div>
			</a>
			<ul class="mm-collapse '.$mm.'">';
			
			$html .= $anak;
						
                            
      $html .= '</ul></li>';
	  return $html;
   }
   
    protected function isItemActive($item)
    {
		if(array_key_exists('url', $item)){
			
			$arr = explode('/', $item['url'][0]);
			
			/* echo $arr[1]; echo Yii::$app->controller->id;
			
			echo $arr[2]; echo  Yii::$app->controller->action->id;
			
			die(); */
			
			if($arr[1] == Yii::$app->controller->id && 
			$arr[2] == Yii::$app->controller->action->id){
				$this->tree = true;
				return true;
			}
			
			if($arr[1] == Yii::$app->controller->module->id && 
			$arr[2] == Yii::$app->controller->id &&
			$arr[3] == Yii::$app->controller->action->id){
				$this->tree = true;
				return true;
			}
		}
		
	
        return false;
    }
}
