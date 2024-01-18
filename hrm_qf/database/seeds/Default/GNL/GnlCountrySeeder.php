<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GnlCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $country = DB::table('gnl_country')->get();
        $sql = "INSERT INTO `gnl_country` (`id`, `name`, `nationality`, `status`, `positionOrder`) VALUES
        (1, 'Afghanistan', 'Afghan', 1, 0),
        (2, 'Albania', 'Albanian', 1, 0),
        (3, 'Algeria', 'Algerian', 1, 0),
        (4, 'Argentina', 'Argentine', 1, 0),
        (5, 'Australia', 'Australian', 1, 0),
        (6, 'Austria', 'Austrian', 1, 0),
        (7, 'Bangladesh', 'Bangladeshi', 1, 0),
        (8, 'Belgium', 'Belgian', 1, 0),
        (9, 'Bolivia', 'Bolivian', 1, 0),
        (10, 'Botswana', 'Batswana', 1, 0),
        (11, 'Brazil', 'Brazilian', 1, 0),
        (12, 'Bulgaria', 'Bulgarian', 1, 0),
        (13, 'Cambodia', 'Cambodian', 1, 0),
        (14, 'Cameroon', 'Cameroonian', 1, 0),
        (15, 'Canada', 'Canadian', 1, 0),
        (16, 'Chile', 'Chilean', 1, 0),
        (17, 'China', 'Chinese', 1, 0),
        (18, 'Colombia', 'Colombian', 1, 0),
        (19, 'Costa Rica', 'Costa Rican', 1, 0),
        (20, 'Croatia', 'Croatian', 1, 0),
        (21, 'Cuba', 'Cuban', 1, 0),
        (22, 'Czech Republic', 'Czech', 1, 0),
        (23, 'Denmark', 'Danish', 1, 0),
        (24, 'Dominican Republic', 'Dominican', 1, 0),
        (25, 'Ecuador', 'Ecuadorian', 1, 0),
        (26, 'Egypt', 'Egyptian', 1, 0),
        (27, 'El Salvador', 'Salvadorian', 1, 0),
        (28, 'England', 'English', 1, 0),
        (29, 'Estonia', 'Estonian', 1, 0),
        (30, 'Ethiopia', 'Ethiopian', 1, 0),
        (31, 'Fiji', 'Fijian', 1, 0),
        (32, 'Finland', 'Finnish', 1, 0),
        (33, 'France', 'French', 1, 0),
        (34, 'Germany', 'German', 1, 0),
        (35, 'Ghana', 'Ghanaian', 1, 0),
        (36, 'Greece', 'Greek', 1, 0),
        (37, 'Guatemala', 'Guatemalan', 1, 0),
        (38, 'Haiti', 'Haitian', 1, 0),
        (39, 'Honduras', 'Honduran', 1, 0),
        (40, 'Hungary', 'Hungarian', 1, 0),
        (41, 'Iceland', 'Icelandic', 1, 0),
        (42, 'India', 'Indian', 1, 0),
        (43, 'Indonesia', 'Indonesian', 1, 0),
        (44, 'Iran', 'Iranian', 1, 0),
        (45, 'Iraq', 'Iraqi', 1, 0),
        (46, 'Ireland', 'Irish', 1, 0),
        (47, 'Israel', 'Israeli', 1, 0),
        (48, 'Italy', 'Italian', 1, 0),
        (49, 'Jamaica', 'Jamaican', 1, 0),
        (50, 'Japan', 'Japanese', 1, 0),
        (51, 'Jordan', 'Jordanian', 1, 0),
        (52, 'Kenya', 'Kenyan', 1, 0),
        (53, 'Kuwait', 'Kuwaiti', 1, 0),
        (54, 'Laos', 'Lao', 1, 0),
        (55, 'Latvia', 'Latvian', 1, 0),
        (56, 'Lebanon', 'Lebanese', 1, 0),
        (57, 'Libya', 'Libyan', 1, 0),
        (58, 'Lithuania', 'Lithuanian', 1, 0),
        (59, 'Madagascar', 'Malagasy', 1, 0),
        (60, 'Malaysia', 'Malaysian', 1, 0),
        (61, 'Mali', 'Malian', 1, 0),
        (62, 'Malta', 'Maltese', 1, 0),
        (63, 'Mexico', 'Mexican', 1, 0),
        (64, 'Mongolia', 'Mongolian', 1, 0),
        (65, 'Morocco', 'Moroccan', 1, 0),
        (66, 'Mozambique', 'Mozambican', 1, 0),
        (67, 'Namibia', 'Namibian', 1, 0),
        (68, 'Nepal', 'Nepalese', 1, 0),
        (69, 'Netherlands', 'Dutch', 1, 0),
        (70, 'New Zealand', 'New Zealand', 1, 0),
        (71, 'Nicaragua', 'Nicaraguan', 1, 0),
        (72, 'Nigeria', 'Nigerian', 1, 0),
        (73, 'Norway', 'Norwegian', 1, 0),
        (74, 'Pakistan', 'Pakistani', 1, 0),
        (75, 'Panama', 'Panamanian', 1, 0),
        (76, 'Paraguay', 'Paraguayan', 1, 0),
        (77, 'Peru', 'Peruvian', 1, 0),
        (78, 'Philippines', 'Philippine', 1, 0),
        (79, 'Poland', 'Polish', 1, 0),
        (80, 'Portugal', 'Portuguese', 1, 0),
        (81, 'Romania', 'Romanian', 1, 0),
        (82, 'Russia', 'Russian', 1, 0),
        (83, 'Saudi Arabia', 'Saudi', 1, 0),
        (84, 'Scotland', 'Scottish', 1, 0),
        (85, 'Senegal', 'Senegalese', 1, 0),
        (86, 'Serbia', 'Serbian', 1, 0),
        (87, 'Singapore', 'Singaporean', 1, 0),
        (88, 'Slovakia', 'Slovak', 1, 0),
        (89, 'South Africa', 'South African', 1, 0),
        (90, 'South Korea', 'Korean', 1, 0),
        (91, 'Spain', 'Spanish', 1, 0),
        (92, 'Sri Lanka', 'Sri Lankan', 1, 0),
        (93, 'Sudan', 'Sudanese', 1, 0),
        (94, 'Sweden', 'Swedish', 1, 0),
        (95, 'Switzerland', 'Swiss', 1, 0),
        (96, 'Syria', 'Syrian', 1, 0),
        (97, 'Taiwan', 'Taiwanese', 1, 0),
        (98, 'Tajikistan', 'Tajikistani', 1, 0),
        (99, 'Thailand', 'Thai', 1, 0),
        (100, 'Tonga', 'Tongan', 1, 0),
        (101, 'Tunisia', 'Tunisian', 1, 0),
        (102, 'Turkey', 'Turkish', 1, 0),
        (103, 'Ukraine', 'Ukrainian', 1, 0),
        (104, 'United Arab Emirates', 'Emirati', 1, 0),
        (105, 'United Kingdom', 'British', 1, 0),
        (106, 'United States', 'American', 1, 0),
        (107, 'Uruguay', 'Uruguayan', 1, 0),
        (108, 'Venezuela', 'Venezuelan', 1, 0),
        (109, 'Vietnam', 'Vietnamese', 1, 0),
        (110, 'Wales', 'Welsh', 1, 0),
        (111, 'Zambia', 'Zambian', 1, 0),
        (112, 'Zimbabwe', 'Zimbabwean', 1, 0),
        (128, 'Other', 'Other', 1, 1)";
        if (count($country) == 0) {
            DB::insert($sql);
        }
    }
}