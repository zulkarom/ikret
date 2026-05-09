<?php
use yii\helpers\Html;
//use yii\bootstrap5\ActiveForm;
use kartik\widgets\ActiveForm;
use yii\helpers\Url;
$web = Yii::getAlias('@web');

$this->title = 'I-CREATE - Login';

?>

<div class="row">
<div class="col-md-3"></div>
    <div class="col-md-5">


    <div class="d-flex justify-content-center py-4">
                <a href="index.html" class="logo d-flex align-items-center w-auto">
          
                  <span class="d-none d-lg-block">I-CREATE</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                    <p class="text-center small">Enter your username or email & password to login</p>
                  </div>

             
                 
                  <?php
                  if($attendanceToken){
                    $url = ['/site/login', 't' => $attendanceToken];
                  }else{
                    $url = ['/site/login'];
                  }
                  
                  $form = ActiveForm::begin(['id' => 'login-form', 'class' => 'row g-3 needs-validation', 'action' => $url]); ?>

                    <div class="col-12">

                    <?= $form
            ->field($model, 'username', [
              'template' => '{label}{input}<i style="font-size:11px">Email / Matric number / Staff No. or any unique username</i>{error}',
              'addon' => ['append' => ['content'=>'<i class="bi bi-person"></i>']]
            ])
            ->label('Username or Email')
            ->textInput(['autocomplete' => 'username']) ?>
            <div id="login-username-status" class="form-text small"></div>
            </div>

            <div class="col-12 d-none mt-2" id="inline-register-fullname-wrap">
              <label class="form-label" for="inline-register-fullname">Full Name</label>
              <div class="input-group">
                <input type="text" id="inline-register-fullname" name="InlineRegister[fullname]" class="form-control" style="text-transform: uppercase" autocomplete="name">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
              </div>
              <i style="font-size:11px">Required for new account registration.</i>
            </div>

            <div class="col-12 d-none mt-2" id="inline-register-email-wrap">
              <label class="form-label" for="inline-register-email">Email</label>
              <div class="input-group">
                <input type="email" id="inline-register-email" name="InlineRegister[email]" class="form-control" autocomplete="email">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              </div>
              <i style="font-size:11px">If this email already exists, enter its current password to login.</i>
              <div id="inline-register-email-status" class="form-text small"></div>
            </div>

            <div class="col-12 mt-2">
            <?= $form
            ->field($model, 'password', ['addon' => ['append' => ['content'=>'<i class="bi bi-lock"></i>']]])
    
            ->passwordInput(['autocomplete' => 'current-password']) ?>
            <div class="form-check mt-3 mb-2">
                <input type="checkbox" class="form-check-input" id="show-login-password">
                <label class="form-check-label small" for="show-login-password">Show password</label>
            </div>
            </div>



            <div class="col-12 mt-2">
                <?= $form->field($model, 'rememberMe')->checkbox() ?>
            <!-- /.col -->
    
            <!-- /.col -->
        </div>


       

            
                    <div class="col-12">
          
                      <?= Html::submitButton('Login', ['class' => 'btn btn-primary w-100', 'name' => 'login-button', 'id' => 'login-submit-button']) ?>
                    </div>
                    <br />
                    <div class="col-12">
                      <p class="small mb-0">I forgot my password <a href="<?=Url::to(['/site/forgot-password'])?>">Recover Password</a></p>
                    </div>
                    <div class="col-12">
                      <p class="small mb-0">Don't have account? <a href="<?=Url::to(['/site/register'])?>">Create an account</a></p>
                    </div>
                    <?php ActiveForm::end(); ?>

                </div>
              </div>





    </div>
    
</div>
             
<?php
$usernameStatusUrl = Url::to(['site/login-username-status']);
$emailStatusUrl = Url::to(['site/login-email-status']);
$this->registerJs(<<<JS
$('#show-login-password').on('change', function(){
    var passwordInput = $('#loginform-password');
    passwordInput.attr('type', $(this).is(':checked') ? 'text' : 'password');
});

var usernameStatusUrl = '$usernameStatusUrl';
var emailStatusUrl = '$emailStatusUrl';
var usernameRequest = null;
var emailRequest = null;

function resetInlineRegisterFields() {
    $('#inline-register-fullname-wrap, #inline-register-email-wrap').addClass('d-none');
    $('#inline-register-fullname, #inline-register-email').prop('required', false);
    $('#inline-register-email-status').text('').removeClass('text-success text-warning text-muted');
    $('#login-submit-button').text('Login');
}

function showInlineRegisterFields(username) {
    $('#inline-register-email-wrap').removeClass('d-none');
    $('#inline-register-email').prop('required', true);
    $('#inline-register-fullname-wrap').addClass('d-none');
    $('#inline-register-fullname').prop('required', false);
    $('#inline-register-email-status').text('').removeClass('text-success text-warning text-muted');
    $('#login-submit-button').text('Continue');

    if (username.indexOf('@') !== -1) {
        $('#inline-register-email').val(username);
        checkInlineEmail();
    } else {
        $('#inline-register-email').val('');
    }
}

function checkInlineEmail() {
    var email = $.trim($('#inline-register-email').val());
    var status = $('#inline-register-email-status');
    var usernameStatus = $('#login-username-status');

    $('#inline-register-fullname-wrap').addClass('d-none');
    $('#inline-register-fullname').prop('required', false);

    if (!email) {
        status.text('').removeClass('text-success text-warning text-muted');
        usernameStatus.text('Username not found. Enter your email to continue.').removeClass('text-success text-muted').addClass('text-warning');
        $('#login-submit-button').text('Continue');
        return;
    }

    status.text('Checking email...').removeClass('text-success text-warning').addClass('text-muted');

    if (emailRequest) {
        emailRequest.abort();
    }

    emailRequest = $.ajax({
        url: emailStatusUrl,
        data: {email: email},
        dataType: 'json'
    }).done(function(response){
        if (response.exists) {
            status.text('Email found. Enter the current password to login.').removeClass('text-warning text-muted').addClass('text-success');
            usernameStatus.text('Your username will be updated.').removeClass('text-success text-muted').addClass('text-warning');
            $('#inline-register-fullname-wrap').addClass('d-none');
            $('#inline-register-fullname').prop('required', false);
            $('#login-submit-button').text('Login');
        } else {
            status.text('Email not found. Fill in your full name to register.').removeClass('text-success text-muted').addClass('text-warning');
            usernameStatus.text('Username not found. Enter your email to continue.').removeClass('text-success text-muted').addClass('text-warning');
            $('#inline-register-fullname-wrap').removeClass('d-none');
            $('#inline-register-fullname').prop('required', true);
            $('#login-submit-button').text('Register & Login');
        }
    }).fail(function(xhr, statusText){
        if (statusText !== 'abort') {
            status.text('Unable to check email now.').removeClass('text-success').addClass('text-warning');
            $('#login-submit-button').text('Continue');
        }
    });
}

function checkLoginUsername() {
    var username = $.trim($('#loginform-username').val());
    var status = $('#login-username-status');

    if (!username) {
        status.text('').removeClass('text-success text-warning text-muted');
        resetInlineRegisterFields();
        return;
    }

    status.text('Checking username...').removeClass('text-success text-warning').addClass('text-muted');

    if (usernameRequest) {
        usernameRequest.abort();
    }

    usernameRequest = $.ajax({
        url: usernameStatusUrl,
        data: {username: username},
        dataType: 'json'
    }).done(function(response){
        if (response.exists) {
            status.text('Account found. Enter your password to login.').removeClass('text-warning text-muted').addClass('text-success');
            resetInlineRegisterFields();
        } else {
            status.text('Username not found. Enter your email to continue.').removeClass('text-success text-muted').addClass('text-warning');
            showInlineRegisterFields(username);
        }
    }).fail(function(xhr, statusText){
        if (statusText !== 'abort') {
            status.text('Unable to check username now. You can still submit the form.').removeClass('text-success').addClass('text-warning');
        }
    });
}

$('#loginform-username').on('change blur', checkLoginUsername);
$('#inline-register-email').on('change blur', checkInlineEmail);

checkLoginUsername();
JS);
?>
