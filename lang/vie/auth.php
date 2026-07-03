<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'unauthenticated' => 'Cần xác thực. Vui lòng đăng nhập.',
    'token_failed' => 'Mã xác thực không hợp lệ.',
    'token_no_cookie' => 'Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.',
    'token_expired' => 'Refresh token không hợp lệ hoặc đã hết hạn. Vui lòng đăng nhập lại.',
    'account_invalid' => 'Tài khoản không tồn tại hoặc đã bị khóa.',
    'password_incorrect' => 'Mật khẩu không đúng.',
    'logout_success' => 'Đăng xuất thành công.',
    'login_success' => 'Đăng nhập thành công.',
    'login_failed' => 'Địa chỉ email hoặc mật khẩu của bạn không đúng.',
    'register_success' => 'Đăng ký tài khoản thành công. Vui lòng kiểm tra gmail để xác thực tài khoản',
    'permission_denied' => 'Tài khoản không có quyền truy cập',
    'register_failed' => 'Đăng ký mới không thành công.',
    'email_error' => 'Email không tồn tại',
    'not_active' => 'Tài khoản của bạn chưa được xác thực. Vui lòng kiểm tra email để xác thực!',
    'locked' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên để mở khóa!',
    'reset_password_failed' => 'Liên kết đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu gửi lại liên kết mới.',
    'verify_success' => 'Tài khoản của bạn đã được xác thực thành công. Bạn có thể đăng nhập ngay bây giờ.',
    'verify_link_expired' => 'Liên kết xác thực đã hết hạn hoặc không hợp lệ. Vui lòng đăng ký lại.',
    'mail' => [
        'register' => [
            'mail_address' => 'Email',
            'text_verify' => 'Vui lòng bấm vào đây để xác thực tài khoản và đăng nhập!',
        ],
        'forgot_password' => [
            'text' => 'Vui lòng mở liên kết này để thực hiện cập nhật mật khẩu mới!',
        ],
    ],
];
