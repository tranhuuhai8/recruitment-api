<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute chưa được chấp nhận',
    'active_url'           => ':attribute không phải là URL hợp lệ',
    'after'                => ':attribute phải là ngày sau :date',
    'after_or_equal'       => ':attribute phải là ngày sau hoặc bằng :date',
    'alpha'                => ':attribute chỉ được chứa chữ cái',
    'alpha_dash'           => ':attribute chỉ được chứa chữ cái, số, dấu gạch ngang (-) và gạch dưới (_)',
    'alpha_num'            => ':attribute chỉ được chứa chữ cái và số',
    'array'                => ':attribute phải là dạng mảng',
    'before'               => ':attribute phải là ngày trước :date',
    'before_or_equal'      => ':attribute phải là ngày trước hoặc bằng :date',
    'between'              => [
        'numeric' => ':attribute phải nằm trong khoảng :min đến :max',
        'file'    => ':attribute phải nằm trong khoảng :min đến :max kilobyte',
        'string'  => ':attribute phải có độ dài từ :min đến :max ký tự',
        'array'   => ':attribute phải có từ :min đến :max phần tử',
    ],
    'boolean'              => ':attribute chỉ được nhận giá trị true hoặc false',
    'confirmed'            => ':attribute không khớp với xác nhận',
    'date'                 => ':attribute không phải là định dạng ngày hợp lệ',
    'date_format'          => ':attribute phải khớp với định dạng :format',
    'different'            => ':attribute phải khác với :other',
    'digits'               => ':attribute phải gồm :digits chữ số',
    'digits_between'       => ':attribute phải gồm từ :min đến :max chữ số',
    'dimensions'           => ':attribute phải có kích thước hình ảnh hợp lệ',
    'distinct'             => ':attribute có giá trị bị trùng lặp',
    'email'                => ':attribute phải là địa chỉ email hợp lệ',
    'exists'               => ':attribute không hợp lệ',
    'file'                 => ':attribute không phải là tệp hợp lệ',
    'filled'               => ':attribute là bắt buộc',
    'gt'                   => [
        'numeric' => ':attribute phải lớn hơn :value',
        'file'    => ':attribute phải lớn hơn :value kilobyte',
        'string'  => ':attribute phải nhiều hơn :value ký tự',
        'array'   => ':attribute phải có nhiều hơn :value phần tử',
    ],
    'gte'                  => [
        'numeric' => ':attribute phải lớn hơn hoặc bằng :value',
        'file'    => ':attribute phải lớn hơn hoặc bằng :value kilobyte',
        'string'  => ':attribute phải nhiều hơn hoặc bằng :value ký tự',
        'array'   => ':attribute phải có ít nhất :value phần tử',
    ],
    'image'                => ':attribute phải là ảnh định dạng jpg, png, bmp, gif hoặc svg',
    'in'                   => ':attribute không hợp lệ',
    'in_array'             => ':attribute phải tồn tại trong :other',
    'integer'              => ':attribute phải là số nguyên',
    'ip'                   => ':attribute phải là địa chỉ IP hợp lệ',
    'ipv4'                 => ':attribute phải là địa chỉ IPv4 hợp lệ',
    'ipv6'                 => ':attribute phải là địa chỉ IPv6 hợp lệ',
    'json'                 => ':attribute phải là chuỗi JSON hợp lệ',
    'lt'                   => [
        'numeric' => ':attribute phải nhỏ hơn :value',
        'file'    => ':attribute phải nhỏ hơn :value kilobyte',
        'string'  => ':attribute phải ít hơn :value ký tự',
        'array'   => ':attribute phải có ít hơn :value phần tử',
    ],
    'lte'                  => [
        'numeric' => ':attribute phải nhỏ hơn hoặc bằng :value',
        'file'    => ':attribute phải nhỏ hơn hoặc bằng :value kilobyte',
        'string'  => ':attribute phải ít hơn hoặc bằng :value ký tự',
        'array'   => ':attribute không được nhiều hơn :value phần tử',
    ],
    'max'                  => [
        'numeric' => ':attribute phải nhỏ hơn hoặc bằng :max',
        'file'    => ':attribute phải nhỏ hơn hoặc bằng :max KB',
        'string'  => ':attribute phải ít hơn hoặc bằng :max ký tự',
        'array'   => ':attribute không được vượt quá :max phần tử',
        'total_file_upload' => 'Tổng số tệp tải lên không được vượt quá :max tập tin',
    ],
    'mimes'                => ':attribute phải là tệp kiểu :values',
    'mimetypes'            => ':attribute phải là tệp kiểu :values',
    'min'                  => [
        'numeric' => ':attribute phải lớn hơn hoặc bằng :min',
        'file'    => ':attribute phải có ít nhất :min KB',
        'string'  => ':attribute phải có ít nhất :min ký tự',
        'array'   => ':attribute phải có ít nhất :min phần tử',
    ],
    'not_in'               => ':attribute không hợp lệ',
    'not_regex'            => 'Định dạng :attribute không hợp lệ',
    'numeric'              => ':attribute phải là số',
    'present'              => ':attribute không tồn tại',
    'regex'                => ':attribute không hợp lệ',
    'required'             => ':attribute là bắt buộc',
    'required_if'          => ':attribute là bắt buộc khi :other là :value',
    'required_unless'      => ':attribute là bắt buộc trừ khi :other có giá trị trong :values',
    'required_with'        => ':attribute là bắt buộc khi có :values',
    'required_with_all'    => ':attribute là bắt buộc khi có tất cả :values',
    'required_without'     => ':attribute là bắt buộc khi không có :values',
    'required_without_all' => ':attribute là bắt buộc khi không có bất kỳ giá trị nào trong :values',
    'same'                 => ':attribute và :other phải giống nhau',
    'size'                 => [
        'numeric' => ':attribute phải bằng :size',
        'file'    => ':attribute phải có dung lượng :size KB',
        'string'  => ':attribute phải có :size ký tự',
        'array'   => ':attribute phải chứa :size phần tử',
    ],
    'string'               => ':attribute phải là chuỗi',
    'timezone'             => ':attribute phải là múi giờ hợp lệ',
    'unique'               => ':attribute đã tồn tại',
    'already_exist'        => ':attribute đã được đăng ký',
    'uploaded'             => 'Tải lên :attribute thất bại',
    'url'                  => ':attribute phải có định dạng URL hợp lệ',

    'attributes' => [
        'logo' => 'Ảnh đại diện',
        'cover_img' => 'Ảnh bìa',
        'name' => 'Tên',
        'short_name' => 'Tên viết tắt',
        'description' => 'Mô tả',
        'mail_address' => 'Địa chỉ email',
        'password' => 'Mật khẩu',
        'telephone' => 'Số điện thoại',
        'gender' => 'Giới tính',
        'birthday' => 'Ngày sinh',
        'city_id' => 'Thành phố',
        'salary_min' => 'Lương tối thiểu',
        'salary_max' => 'Lương tối đa',
    ],

    'custom' => [
        'password' => [
            'regex' => 'Mật khẩu phải đủ mạnh (bao gồm ít nhất 8 ký tự, chữ viết hoa, viết thường, số và ký tự đặc biệt).',
        ],
    ],
];
