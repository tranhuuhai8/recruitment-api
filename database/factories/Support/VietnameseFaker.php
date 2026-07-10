<?php

namespace Database\Factories\Support;

use Faker\Factory as FakerFactory;
use Faker\Generator;

class VietnameseFaker
{
    private static ?Generator $vnFaker = null;

    private static array $usedCompanyNames = [];

    private static array $jobTitles = [
        'Lập trình viên Backend (PHP/Laravel)',
        'Lập trình viên Frontend (ReactJS)',
        'Lập trình viên Mobile (Flutter)',
        'Kỹ sư DevOps',
        'Chuyên viên kiểm thử phần mềm (Tester)',
        'Nhân viên kinh doanh',
        'Chuyên viên chăm sóc khách hàng',
        'Chuyên viên Marketing Online',
        'Nhân viên nhân sự (HR)',
        'Kế toán tổng hợp',
        'Trưởng phòng kinh doanh',
        'Nhân viên telesale',
        'Chuyên viên tuyển dụng',
        'Nhân viên thiết kế đồ họa',
        'Biên tập viên nội dung',
        'Nhân viên vận hành kho',
        'Tài xế giao hàng',
        'Nhân viên thu ngân',
        'Quản lý cửa hàng',
        'Chuyên viên phân tích dữ liệu',
        'Kỹ sư xây dựng',
        'Kiến trúc sư',
        'Nhân viên phiên dịch tiếng Nhật',
        'Nhân viên phiên dịch tiếng Trung',
        'Giáo viên tiếng Anh',
        'Dược sĩ',
        'Điều dưỡng viên',
        'Chuyên viên pháp lý',
        'Chuyên viên SEO',
        'Nhân viên thiết kế UI/UX',
        'Kỹ sư cơ khí',
        'Kỹ sư điện',
        'Chuyên viên thu mua',
        'Nhân viên vận hành sản xuất',
        'Trưởng nhóm phát triển sản phẩm',
        'Thực tập sinh IT',
        'Nhân viên bảo trì hệ thống',
        'Chuyên viên phân tích nghiệp vụ (BA)',
        'Quản trị viên hệ thống mạng',
        'Nhân viên content Tiktok',
    ];

    private static array $companyTypes = ['Công ty TNHH', 'Công ty Cổ phần', 'Công ty TNHH MTV'];

    private static array $companyCores = [
        'Việt Thành', 'An Phát', 'Minh Khang', 'Hoàng Long', 'Đại Dương', 'Phú Quý',
        'Thành Công', 'Sao Việt', 'Tân Tiến', 'Việt Anh', 'Hồng Phát', 'Nam Việt',
        'Toàn Cầu', 'Á Châu', 'Việt Nhật', 'Kim Cương', 'Hưng Thịnh', 'Gia Bảo',
        'Phương Nam', 'Đông Á', 'Thịnh Vượng', 'Việt Tín', 'An Khang', 'Bình Minh',
    ];

    private static array $companySuffixes = [
        'Công nghệ', 'Giải pháp', 'Thương mại', 'Dịch vụ', 'Đầu tư', 'Xây dựng',
        'Truyền thông', 'Logistics', 'Giáo dục', 'Bất động sản', 'Thực phẩm',
        'Nội thất', 'Du lịch', 'Y tế', 'Tài chính', 'Sản xuất', 'Xuất nhập khẩu',
    ];

    private static array $companyDescriptions = [
        'là đơn vị hoạt động lâu năm trong lĩnh vực, luôn đặt chất lượng sản phẩm và dịch vụ lên hàng đầu.',
        'không ngừng đổi mới sáng tạo nhằm mang lại giá trị tốt nhất cho khách hàng.',
        'sở hữu đội ngũ nhân sự trẻ, năng động và giàu kinh nghiệm.',
        'cam kết đồng hành cùng người lao động trong quá trình phát triển sự nghiệp.',
        'hướng tới mục tiêu trở thành thương hiệu uy tín hàng đầu trong ngành.',
    ];

    private static array $jobDescriptionIntros = [
        'Chúng tôi đang tìm kiếm ứng viên tiềm năng cho vị trí này.',
        'Đây là cơ hội tốt để bạn phát triển sự nghiệp trong môi trường chuyên nghiệp.',
        'Ứng viên sẽ được làm việc trực tiếp với đội ngũ giàu kinh nghiệm.',
        'Vị trí này phù hợp với người có tinh thần trách nhiệm cao và ham học hỏi.',
    ];

    private static array $jobRequirements = [
        'Có kinh nghiệm làm việc tối thiểu 1 năm ở vị trí tương đương.',
        'Tốt nghiệp Cao đẳng/Đại học chuyên ngành liên quan.',
        'Có khả năng làm việc độc lập và theo nhóm.',
        'Chịu được áp lực công việc cao.',
        'Ưu tiên ứng viên có kỹ năng giao tiếp tốt.',
        'Thành thạo tin học văn phòng.',
        'Có tinh thần cầu tiến, ham học hỏi.',
    ];

    private static array $jobBenefits = [
        'Lương thưởng cạnh tranh, thưởng hiệu suất theo quý.',
        'Được đóng bảo hiểm đầy đủ theo quy định.',
        'Môi trường làm việc năng động, trẻ trung.',
        'Cơ hội đào tạo và thăng tiến rõ ràng.',
        'Du lịch công ty hàng năm.',
        'Được cấp trang thiết bị làm việc đầy đủ.',
    ];

    private static array $applicantIntros = [
        'Tôi là người năng động, ham học hỏi và mong muốn phát triển trong môi trường chuyên nghiệp.',
        'Với kinh nghiệm làm việc thực tế, tôi tự tin có thể đóng góp tốt cho công việc.',
        'Tôi luôn nỗ lực hoàn thành công việc đúng deadline và đảm bảo chất lượng.',
        'Mong muốn tìm kiếm một môi trường làm việc ổn định để gắn bó lâu dài.',
    ];

    private static array $coverLetterClosings = [
        'Rất mong nhận được phản hồi sớm từ phía công ty.',
        'Tôi tin rằng mình phù hợp với vị trí này và mong có cơ hội trao đổi thêm trong buổi phỏng vấn.',
        'Cảm ơn quý công ty đã dành thời gian xem xét hồ sơ của tôi.',
    ];

    private static array $reviewContents = [
        'Môi trường làm việc khá tốt, đồng nghiệp thân thiện.',
        'Lương thưởng đúng hạn, chế độ đãi ngộ hợp lý.',
        'Quản lý hỗ trợ nhiệt tình, có lộ trình thăng tiến rõ ràng.',
        'Khối lượng công việc khá nhiều nhưng bù lại học được nhiều kinh nghiệm.',
        'Công ty có văn hóa làm việc chuyên nghiệp, đáng để cân nhắc ứng tuyển.',
        'Chưa thực sự hài lòng về chính sách phúc lợi, mong công ty cải thiện thêm.',
        'Đồng nghiệp hòa đồng, sếp trực tiếp dễ gần và luôn lắng nghe góp ý.',
    ];

    private static array $contactTitles = [
        'Hỏi về quy trình ứng tuyển',
        'Phản hồi về lỗi hiển thị tin tuyển dụng',
        'Yêu cầu hỗ trợ đăng tin tuyển dụng',
        'Góp ý về giao diện website',
        'Hỏi về chính sách bảo mật thông tin',
        'Yêu cầu hỗ trợ khôi phục tài khoản',
        'Phản hồi về dịch vụ chăm sóc khách hàng',
        'Hỏi về gói dịch vụ dành cho doanh nghiệp',
    ];

    private static array $contactContents = [
        'Tôi gặp khó khăn khi thao tác trên hệ thống, mong bộ phận hỗ trợ kiểm tra giúp.',
        'Vui lòng hướng dẫn tôi các bước cần thiết để hoàn tất yêu cầu trên.',
        'Tôi muốn biết thêm thông tin chi tiết trước khi quyết định sử dụng dịch vụ.',
        'Mong nhận được phản hồi sớm từ đội ngũ hỗ trợ.',
    ];

    private static array $adminNotes = [
        'Đã liên hệ lại qua điện thoại.',
        'Cần chuyển cho bộ phận kỹ thuật xử lý.',
        'Khách hàng hài lòng với phản hồi.',
        'Đang theo dõi thêm phản hồi từ khách hàng.',
    ];

    private static array $jobFavoriteNotes = [
        'Công việc phù hợp với kinh nghiệm hiện tại.',
        'Mức lương hấp dẫn, lưu lại để cân nhắc sau.',
        'Địa điểm làm việc gần nhà, cần ứng tuyển sớm.',
        'Đang chờ công ty phản hồi phỏng vấn.',
    ];

    private static array $mailTemplateNames = [
        'Thông báo trúng tuyển',
        'Thông báo từ chối hồ sơ',
        'Nhắc nhở hoàn thiện hồ sơ',
        'Thông báo có việc làm mới phù hợp',
        'Thông báo tin tuyển dụng sắp hết hạn',
        'Chào mừng thành viên mới',
        'Khôi phục mật khẩu',
        'Xác thực địa chỉ email',
        'Thông báo có ứng viên mới ứng tuyển',
        'Nhắc nhở phỏng vấn',
        'Thông báo cập nhật chính sách',
        'Bản tin việc làm hàng tuần',
        'Thông báo công ty được theo dõi có tin mới',
    ];

    private static array $mailBodyParagraphs = [
        'Đây là thông báo tự động từ hệ thống, vui lòng không phản hồi trực tiếp email này.',
        'Nếu cần hỗ trợ thêm, vui lòng liên hệ bộ phận chăm sóc khách hàng của chúng tôi.',
        'Cảm ơn bạn đã đồng hành cùng nền tảng tuyển dụng của chúng tôi.',
        'Vui lòng đăng nhập vào tài khoản để xem chi tiết đầy đủ.',
    ];

    private static function vn(): Generator
    {
        return self::$vnFaker ??= FakerFactory::create('vi_VN');
    }

    public static function personName(): string
    {
        return self::vn()->name();
    }

    public static function address(): string
    {
        return self::vn()->address();
    }

    public static function jobTitle(): string
    {
        return fake()->randomElement(self::$jobTitles);
    }

    public static function companyName(): string
    {
        $name = '';

        for ($i = 0; $i < 30; $i++) {
            $name = fake()->randomElement(self::$companyTypes) . ' '
                . fake()->randomElement(self::$companyCores) . ' '
                . fake()->randomElement(self::$companySuffixes);

            if (!isset(self::$usedCompanyNames[$name])) {
                self::$usedCompanyNames[$name] = true;

                return $name;
            }
        }

        $name .= ' ' . fake()->unique()->numberBetween(1000, 9999);
        self::$usedCompanyNames[$name] = true;

        return $name;
    }

    public static function companyDescription(): string
    {
        return fake()->randomElement(self::$companyCores) . ' '
            . fake()->randomElement(self::$companyDescriptions);
    }

    public static function jobDescription(): string
    {
        $requirements = fake()->randomElements(self::$jobRequirements, fake()->numberBetween(3, 5));
        $benefits = fake()->randomElements(self::$jobBenefits, fake()->numberBetween(2, 4));

        return fake()->randomElement(self::$jobDescriptionIntros) . "\n\n"
            . "Yêu cầu công việc:\n- " . implode("\n- ", $requirements) . "\n\n"
            . "Quyền lợi:\n- " . implode("\n- ", $benefits);
    }

    public static function applicantIntro(): string
    {
        return fake()->randomElement(self::$applicantIntros);
    }

    public static function coverLetter(): string
    {
        return self::applicantIntro() . ' ' . fake()->randomElement(self::$coverLetterClosings);
    }

    public static function reviewContent(): string
    {
        return fake()->randomElement(self::$reviewContents);
    }

    public static function contactTitle(): string
    {
        return fake()->randomElement(self::$contactTitles);
    }

    public static function contactContent(): string
    {
        return fake()->randomElement(self::$contactContents);
    }

    public static function adminNote(): string
    {
        return fake()->randomElement(self::$adminNotes);
    }

    public static function jobFavoriteNote(): string
    {
        return fake()->randomElement(self::$jobFavoriteNotes);
    }

    public static function mailTemplateName(): string
    {
        return fake()->randomElement(self::$mailTemplateNames);
    }

    public static function mailBodyParagraph(): string
    {
        return fake()->randomElement(self::$mailBodyParagraphs);
    }
}
