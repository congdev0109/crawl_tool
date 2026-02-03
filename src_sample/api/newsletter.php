<?php
header('Content-Type: application/json');

include "config.php";
$response = array();


if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $response['error'] = true;
    $response['message'] = 'Yêu cầu không hợp lệ';
    echo json_encode($response);
    exit;
}


if (!isset($_POST['fullname']) || !isset($_POST['email']) || !isset($_POST['phone']) || !isset($_POST['content'])) {
    $response['error'] = true;
    $response['message'] = 'Vui lòng điền đầy đủ thông tin';
    echo json_encode($response);
    exit;
}


if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $response['error'] = true;
    $response['message'] = 'Email không hợp lệ';
    echo json_encode($response);
    exit;
}

if (!preg_match('/^[0-9]{10}$/', $_POST['phone'])) {
    $response['error'] = true;
    $response['message'] = 'Số điện thoại không hợp lệ';
    echo json_encode($response);
    exit;
}

$dataNewsletter = array(
    'fullname' => htmlspecialchars($_POST['fullname']),
    'email' => htmlspecialchars($_POST['email']),
    'phone' => htmlspecialchars($_POST['phone']),
    'content' => htmlspecialchars($_POST['content']),
    'date_created' => time(),
    'type' => 'dangkynhantin'
);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!empty($_POST['recaptcha_response'])) {
        $recaptchaResponse = $_POST['recaptcha_response'];
        $secretKey = $config['googleAPI']['recaptcha']['secretkey'];
        $url = $config['googleAPI']['recaptcha']['urlapi'];
        
        $data = array(
            'secret' => $secretKey,
            'response' => $recaptchaResponse
        );

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        
        $context = stream_context_create($options);
        $result = json_decode(file_get_contents($url, false, $context));
        // dd($result);
        if($d->insert('newsletter', $dataNewsletter)) {
            
            $response['error'] = false;
            $response['message'] = 'Đăng ký nhận tin thành công';
        } else {
            $response['error'] = true;
            $response['message'] = 'Có lỗi xảy ra, vui lòng thử lại';
        }
        // if ($result->success && $result->score >= 0.5) {
        // } else {
        //     $response['error'] = true;
        //     $response['message'] = "Xác thực reCAPTCHA thất bại";
        // }
    } else {
        $response['error'] = true;
        $response['message'] = "Vui lòng xác nhận reCAPTCHA";
    }
}

echo json_encode($response);
exit;



// if (!empty($_POST['submit-newsletter'])) {
//     $responseCaptcha = $_POST['recaptcha_response_newsletter'];
//     $resultCaptcha = $func->checkRecaptcha($responseCaptcha);
//     $scoreCaptcha = (!empty($resultCaptcha['score'])) ? $resultCaptcha['score'] : 0;
//     $actionCaptcha = (!empty($resultCaptcha['action'])) ? $resultCaptcha['action'] : '';
//     $testCaptcha = (!empty($resultCaptcha['test'])) ? $resultCaptcha['test'] : false;
//     $dataNewsletter = (!empty($_POST['dataNewsletter'])) ? $_POST['dataNewsletter'] : null;

//     /* Valid data */
//     if (empty($dataNewsletter['email'])) {
//         $flash->set('error', emailkhongduoctrong);
//     }

//     if (!empty($dataNewsletter['email']) && !$func->isEmail($dataNewsletter['email'])) {
//         $flash->set('error', emailkhonghople);
//     }

//     $error = $flash->get('error');

//     if (!empty($error)) {
//         $func->transfer($error, $configBase, false);
//     }
//     // die($error);
//     /* Save data */
//     if (($scoreCaptcha >= 0.5 && $actionCaptcha == 'Newsletter') || $testCaptcha == true) {
//         foreach ($dataNewsletter as $column => $value) {
//             $dataNewsletter[$column] = htmlspecialchars($value);
//         }

//         if ($d->insert('newsletter', $dataNewsletter)) {
//             $func->transfer(dangkynhantinthanhcong, $configBase);
//         } else {
//             $func->transfer(dangkynhantinthatbai, $configBase, false);
//         }
//     } else {
//         $func->transfer(dangkynhantinthatbai, $configBase, false);
//     }
// }
