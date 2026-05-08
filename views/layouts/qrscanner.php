<?php
use app\assets\ScannerAsset;
use yii\helpers\Url;

$url_result = Url::to(['/session/qrscanner-result', 't' => '']);

ScannerAsset::register($this);
$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@app/assets/qrscanner');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>ATTENDANCE SCANNER</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="description" content="QR Code Scanner is the fastest and most user-friendly web application.">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="QR Scanner">
<meta name="apple-mobile-web-app-status-bar-style" content="#e4e4e4">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="application-name" content="QR Scanner">
<meta name="msapplication-TileColor" content="#e4e4e4">
<meta name="msapplication-TileImage" content="<?= $directoryAsset ?>/images/touch/mstile-150x150.png">
<meta name="theme-color" content="#fff">
<link rel="apple-touch-icon" href="<?= $directoryAsset ?>/images/touch/apple-touch-icon.jpg">
<link rel="icon" type="image/png" href="<?= $directoryAsset ?>/images/touch/favicon-32x32.png" sizes="32x32">
<link rel="icon" type="image/png" href="<?= $directoryAsset ?>/images/touch/favicon-16x16.png" sizes="16x16">
<link rel="shortcut icon" href="<?= $directoryAsset ?>/images/touch/favicon.ico">
<link rel="manifest" href="<?= $directoryAsset ?>/manifest.json">
<link rel="preload" as="script" href="<?= $directoryAsset ?>/decoder.js">
<link href="<?= $directoryAsset ?>/styles.css" rel="stylesheet">
</head>

<body>
<div class="app__layout">
    <main class="app__layout-content">
        <video autoplay muted playsinline></video>
        <div class="app__snackbar"></div>
    </main>
</div>

<div class="app__overlay">
    <div class="app__overlay-frame"></div>
    <div class="custom-scanner"></div>
    <div class="app__help-text"></div>
</div>

<script>
(function () {
    var resultUrl = <?= json_encode($url_result) ?>;
    var decoderUrl = <?= json_encode($directoryAsset . '/decoder.js') ?>;
    var video = document.querySelector('video');
    var scannerLine = document.querySelector('.custom-scanner');
    var snackbar = document.querySelector('.app__snackbar');
    var overlay = document.querySelector('.app__overlay');
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d', { willReadFrequently: true });
    var worker = null;
    var detector = null;
    var scanning = false;
    var busy = false;
    var redirected = false;
    var lastScanAt = 0;
    var scanSize = 640;
    var scanEveryMs = 90;

    function showMessage(message, duration) {
        if (!message || !snackbar) {
            return;
        }

        var msg = document.createElement('div');
        msg.className = 'app__snackbar-msg';
        msg.textContent = message;
        snackbar.appendChild(msg);

        setTimeout(function () {
            if (msg.parentNode) {
                msg.parentNode.removeChild(msg);
            }
        }, duration || 4000);
    }

    function stopCamera() {
        scanning = false;

        if (video.srcObject) {
            video.srcObject.getTracks().forEach(function (track) {
                track.stop();
            });
        }

        if (worker) {
            worker.terminate();
            worker = null;
        }
    }

    function goToResult(value) {
        if (redirected || !value) {
            return;
        }

        redirected = true;
        stopCamera();
        window.location.href = resultUrl + encodeURIComponent(value);
    }

    function getScanCrop() {
        var width = video.videoWidth;
        var height = video.videoHeight;
        var sourceSize = Math.floor(Math.min(width, height) * 0.72);

        if (!sourceSize || sourceSize < 1) {
            return null;
        }

        return {
            sx: Math.floor((width - sourceSize) / 2),
            sy: Math.floor((height - sourceSize) / 2),
            size: sourceSize
        };
    }

    function drawScanFrame() {
        var crop = getScanCrop();

        if (!crop) {
            return false;
        }

        canvas.width = scanSize;
        canvas.height = scanSize;
        ctx.drawImage(
            video,
            crop.sx,
            crop.sy,
            crop.size,
            crop.size,
            0,
            0,
            scanSize,
            scanSize
        );

        return true;
    }

    function scanWithNativeDetector() {
        busy = true;

        detector.detect(canvas)
            .then(function (codes) {
                busy = false;

                if (codes && codes.length) {
                    goToResult(codes[0].rawValue || codes[0].displayValue);
                    return;
                }

                requestAnimationFrame(scanLoop);
            })
            .catch(function () {
                busy = false;
                detector = null;
                initWorker();
                requestAnimationFrame(scanLoop);
            });
    }

    function initWorker() {
        if (worker) {
            return;
        }

        worker = new Worker(decoderUrl);
        worker.onmessage = function (event) {
            busy = false;

            if (event.data && event.data.length > 0) {
                goToResult(event.data[0][2]);
                return;
            }

            requestAnimationFrame(scanLoop);
        };
    }

    function scanWithWorker() {
        initWorker();
        busy = true;
        worker.postMessage(ctx.getImageData(0, 0, scanSize, scanSize));
    }

    function scanLoop(timestamp) {
        if (!scanning || busy || redirected) {
            return;
        }

        if (timestamp - lastScanAt < scanEveryMs) {
            requestAnimationFrame(scanLoop);
            return;
        }

        lastScanAt = timestamp;

        if (!drawScanFrame()) {
            requestAnimationFrame(scanLoop);
            return;
        }

        if (detector) {
            scanWithNativeDetector();
        } else {
            scanWithWorker();
        }
    }

    function getCameraConstraints() {
        return {
            audio: false,
            video: {
                facingMode: { ideal: 'environment' },
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        };
    }

    function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showMessage('Camera scanning is not supported by this browser.', 10000);
            return;
        }

        if ('BarcodeDetector' in window) {
            try {
                detector = new BarcodeDetector({ formats: ['qr_code'] });
            } catch (error) {
                detector = null;
            }
        }

        navigator.mediaDevices.getUserMedia(getCameraConstraints())
            .then(function (stream) {
                video.srcObject = stream;
                return video.play();
            })
            .then(function () {
                scanning = true;
                scannerLine.style.display = 'block';
                overlay.style.borderStyle = 'solid';
                requestAnimationFrame(scanLoop);
            })
            .catch(function (error) {
                console.error('Unable to access the camera', error);
                showMessage('Unable to access the camera', 10000);
                scannerLine.style.display = 'none';
            });
    }

    window.addEventListener('pagehide', stopCamera);
    window.addEventListener('DOMContentLoaded', startCamera);
})();
</script>

</body>
</html>
