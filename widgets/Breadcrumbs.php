<?php
namespace app\widgets;

use Yii;
use yii\bootstrap5\Widget;

class Breadcrumbs extends Widget
{
    /**
     * {@inheritdoc}
     */
    public function run()
    {
		
        echo '<nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html"><i class="bi bi-house-door"></i></a></li>
          
        <li class="breadcrumb-item"><a href="#">Library</a></li>
        <li class="breadcrumb-item active">Default</li>
        </ol>
        </nav>';
    }
}
