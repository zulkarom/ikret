<?php
namespace app\models;

use Yii;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the message');
 * Yii::$app->session->setFlash('success', 'This is the message');
 * Yii::$app->session->setFlash('info', 'This is the message');
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 * Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class Alert extends \yii\bootstrap5\Widget
{
    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - key: the name of the session flash variable
     * - value: the bootstrap alert type (i.e. danger, success, info, warning)
     */
    public $alertTypes = [
        'error'   => 'alert-danger',
        'danger'  => 'alert-danger',
        'success' => 'alert-info',
        'info'    => 'alert-primary',
        'warning' => 'alert-warning'
    ];

    public $icons = [
        'error'   => 'bi bi-exclamation-octagon me-1',
        'danger'  => 'bi bi-exclamation-octagon me-1',
        'success' => 'bi bi-check-circle me-1',
        'info'    => 'bi bi-info-circle me-1',
        'warning' => 'bi bi-exclamation-octagon me-1'
    ];
    /**
     * @var array the options for rendering the close button tag.
     * Array will be passed to [[\yii\bootstrap\Alert::closeButton]].
     */
    public $closeButton = [];


    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $session = Yii::$app->session;
        $flashes = $session->getAllFlashes();
        $appendClass = isset($this->options['class']) ? ' ' . $this->options['class'] : '';

        foreach ($flashes as $type => $flash) {
            if (!isset($this->alertTypes[$type])) {
                continue;
            }

            foreach ((array) $flash as $i => $message) {
                echo '<div class="alert '.$this->alertTypes[$type].' alert-dismissible fade show" role="alert">
                <i class="'.$this->icons[$type].'"></i>
                '.$message.'
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';


            }

            $session->removeFlash($type);
        }
    }
}
