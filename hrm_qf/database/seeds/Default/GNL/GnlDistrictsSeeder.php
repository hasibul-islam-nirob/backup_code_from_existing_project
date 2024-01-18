<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlDistrictsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $districts = DB::table('gnl_districts')->get();
        $sql = "INSERT INTO `gnl_districts` (`id`, `division_id`, `district_name`, `bn_name`, `lat`, `lon`, `url`, `is_active`, `is_delete`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
        (1, 1, 'Comilla', 'কুমিল্লা', '23.4682747', '91.1788135', 'www.comilla.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (2, 1, 'Feni', 'ফেনী', '23.023231', '91.3840844', 'www.feni.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (3, 1, 'Brahmanbaria', 'ব্রাহ্মণবাড়িয়া', '23.9570904', '91.1119286', 'www.brahmanbaria.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (4, 1, 'Rangamati', 'রাঙ্গামাটি', NULL, NULL, 'www.rangamati.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (5, 1, 'Noakhali', 'নোয়াখালী', '22.869563', '91.099398', 'www.noakhali.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (6, 1, 'Chandpur', 'চাঁদপুর', '23.2332585', '90.6712912', 'www.chandpur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (7, 1, 'Lakshmipur', 'লক্ষ্মীপুর', '22.942477', '90.841184', 'www.lakshmipur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (8, 1, 'Chattogram', 'চট্টগ্রাম', '22.335109', '91.834073', 'www.chittagong.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (9, 1, 'Coxsbazar', 'কক্সবাজার', NULL, NULL, 'www.coxsbazar.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (10, 1, 'Khagrachhari', 'খাগড়াছড়ি', '23.119285', '91.984663', 'www.khagrachhari.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (11, 1, 'Bandarban', 'বান্দরবান', '22.1953275', '92.2183773', 'www.bandarban.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (12, 2, 'Sirajganj', 'সিরাজগঞ্জ', '24.4533978', '89.7006815', 'www.sirajganj.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (13, 2, 'Pabna', 'পাবনা', '23.998524', '89.233645', 'www.pabna.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (14, 2, 'Bogura', 'বগুড়া', '24.8465228', '89.377755', 'www.bogra.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (15, 2, 'Rajshahi', 'রাজশাহী', NULL, NULL, 'www.rajshahi.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (16, 2, 'Natore', 'নাটোর', '24.420556', '89.000282', 'www.natore.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (17, 2, 'Joypurhat', 'জয়পুরহাট', NULL, NULL, 'www.joypurhat.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (18, 2, 'Chapainawabganj', 'চাঁপাইনবাবগঞ্জ', '24.5965034', '88.2775122', 'www.chapainawabganj.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (19, 2, 'Naogaon', 'নওগাঁ', NULL, NULL, 'www.naogaon.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (20, 3, 'Jashore', 'যশোর', '23.16643', '89.2081126', 'www.jessore.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (21, 3, 'Satkhira', 'সাতক্ষীরা', NULL, NULL, 'www.satkhira.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (22, 3, 'Meherpur', 'মেহেরপুর', '23.762213', '88.631821', 'www.meherpur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (23, 3, 'Narail', 'নড়াইল', '23.172534', '89.512672', 'www.narail.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (24, 3, 'Chuadanga', 'চুয়াডাঙ্গা', '23.6401961', '88.841841', 'www.chuadanga.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (25, 3, 'Kushtia', 'কুষ্টিয়া', '23.901258', '89.120482', 'www.kushtia.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (26, 3, 'Magura', 'মাগুরা', '23.487337', '89.419956', 'www.magura.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (27, 3, 'Khulna', 'খুলনা', '22.815774', '89.568679', 'www.khulna.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (28, 3, 'Bagerhat', 'বাগেরহাট', '22.651568', '89.785938', 'www.bagerhat.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (29, 3, 'Jhenaidah', 'ঝিনাইদহ', '23.5448176', '89.1539213', 'www.jhenaidah.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (30, 4, 'Jhalakathi', 'ঝালকাঠি', NULL, NULL, 'www.jhalakathi.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (31, 4, 'Patuakhali', 'পটুয়াখালী', '22.3596316', '90.3298712', 'www.patuakhali.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (32, 4, 'Pirojpur', 'পিরোজপুর', NULL, NULL, 'www.pirojpur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (33, 4, 'Barisal', 'বরিশাল', NULL, NULL, 'www.barisal.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (34, 4, 'Bhola', 'ভোলা', '22.685923', '90.648179', 'www.bhola.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (35, 4, 'Barguna', 'বরগুনা', NULL, NULL, 'www.barguna.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (36, 5, 'Sylhet', 'সিলেট', '24.8897956', '91.8697894', 'www.sylhet.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (37, 5, 'Moulvibazar', 'মৌলভীবাজার', '24.482934', '91.777417', 'www.moulvibazar.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (38, 5, 'Habiganj', 'হবিগঞ্জ', '24.374945', '91.41553', 'www.habiganj.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (39, 5, 'Sunamganj', 'সুনামগঞ্জ', '25.0658042', '91.3950115', 'www.sunamganj.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (40, 6, 'Narsingdi', 'নরসিংদী', '23.932233', '90.71541', 'www.narsingdi.gov.bd', 1, 0, NULL, 144, '2020-07-05 20:25:05', 144),
        (41, 6, 'Gazipur', 'গাজীপুর', '24.0022858', '90.4264283', 'www.gazipur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (42, 6, 'Shariatpur', 'শরীয়তপুর', NULL, NULL, 'www.shariatpur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (43, 6, 'Narayanganj', 'নারায়ণগঞ্জ', '23.63366', '90.496482', 'www.narayanganj.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (44, 6, 'Tangail', 'টাঙ্গাইল', NULL, NULL, 'www.tangail.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (45, 6, 'Kishoreganj', 'কিশোরগঞ্জ', '24.444937', '90.776575', 'www.kishoreganj.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (46, 6, 'Manikganj', 'মানিকগঞ্জ', NULL, NULL, 'www.manikganj.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (47, 6, 'Dhaka', 'ঢাকা', '23.7115253', '90.4111451', 'www.dhaka.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (48, 6, 'Munshiganj', 'মুন্সিগঞ্জ', NULL, NULL, 'www.munshiganj.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (49, 6, 'Rajbari', 'রাজবাড়ী', '23.7574305', '89.6444665', 'www.rajbari.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (50, 6, 'Madaripur', 'মাদারীপুর', '23.164102', '90.1896805', 'www.madaripur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (51, 6, 'Gopalganj', 'গোপালগঞ্জ', '23.0050857', '89.8266059', 'www.gopalganj.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (52, 6, 'Faridpur', 'ফরিদপুর', '23.6070822', '89.8429406', 'www.faridpur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (53, 7, 'Panchagarh', 'পঞ্চগড়', '26.3411', '88.5541606', 'www.panchagarh.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (54, 7, 'Dinajpur', 'দিনাজপুর', '25.6217061', '88.6354504', 'www.dinajpur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (55, 7, 'Lalmonirhat', 'লালমনিরহাট', NULL, NULL, 'www.lalmonirhat.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (56, 7, 'Nilphamari', 'নীলফামারী', '25.931794', '88.856006', 'www.nilphamari.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (57, 7, 'Gaibandha', 'গাইবান্ধা', '25.328751', '89.528088', 'www.gaibandha.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (58, 7, 'Thakurgaon', 'ঠাকুরগাঁও', '26.0336945', '88.4616834', 'www.thakurgaon.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (59, 7, 'Rangpur', 'রংপুর', '25.7558096', '89.244462', 'www.rangpur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (60, 7, 'Kurigram', 'কুড়িগ্রাম', '25.805445', '89.636174', 'www.kurigram.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (61, 8, 'Sherpur', 'শেরপুর', '25.0204933', '90.0152966', 'www.sherpur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (62, 8, 'Mymensingh', 'ময়মনসিংহ', NULL, NULL, 'www.mymensingh.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (63, 8, 'Jamalpur', 'জামালপুর', '24.937533', '89.937775', 'www.jamalpur.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (64, 8, 'Netrokona', 'নেত্রকোণা', '24.870955', '90.727887', 'www.netrokona.gov.bd', 1, 0, NULL, NULL, NULL, NULL),
        (65, 7, 'tesst', NULL, NULL, NULL, NULL, 1, 1, '2020-02-16 14:44:53', 1, '2020-02-16 08:45:01', 1),
        (66, 7, 'Rangpur Sadar', NULL, NULL, NULL, NULL, 1, 0, '2020-02-22 15:17:11', 1, '2020-02-22 09:17:11', NULL),
        (67, 6, 'Narayangonj', NULL, NULL, NULL, NULL, 1, 0, '2020-07-05 16:43:58', 99, '2020-07-05 20:43:58', NULL),
        (69, 6, 'naryangonj', NULL, NULL, NULL, NULL, 1, 0, '2020-07-06 13:55:33', 79, '2020-07-06 17:55:33', NULL)";
        if (count($districts) == 0) {
            DB::insert($sql);
        }
    }
}
