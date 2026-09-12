<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Pakistan cities / towns for checkout and profile pickers.
 */
class PakistanCities extends BaseConfig
{
    /**
     * Comprehensive list of cities and major towns across Pakistan.
     *
     * @var list<string>
     */
    public array $cities = [
        // Islamabad Capital Territory
        'Islamabad',

        // Punjab
        'Lahore', 'Faisalabad', 'Rawalpindi', 'Multan', 'Gujranwala', 'Sialkot', 'Bahawalpur',
        'Sargodha', 'Sheikhupura', 'Jhang', 'Gujrat', 'Sahiwal', 'Wah Cantonment', 'Rahim Yar Khan',
        'Okara', 'Dera Ghazi Khan', 'Kasur', 'Chiniot', 'Kamoke', 'Hafizabad', 'Sadiqabad',
        'Burewala', 'Khanewal', 'Muzaffargarh', 'Pakpattan', 'Ahmadpur East', 'Kot Addu',
        'Wazirabad', 'Jhelum', 'Gojra', 'Mandi Bahauddin', 'Taxila', 'Bahawalnagar', 'Kharian',
        'Attock', 'Vehari', 'Kamalia', 'Chishtian', 'Jaranwala', 'Hasilpur', 'Arifwala',
        'Sambrial', 'Muridke', 'Khanpur', 'Toba Tek Singh', 'Shorkot', 'Daska',
        'Bhakkar', 'Murree', 'Chakwal', 'Mianwali', 'Layyah', 'Narowal', 'Khushab', 'Nankana Sahib',
        'Lodhran', 'Rajanpur', 'Gujar Khan', 'Pattoki', 'Renala Khurd', 'Depalpur', 'Mailsi',
        'Shujaabad', 'Kabirwala', 'Jampur', 'Taunsa', 'Dunyapur', 'Kot Momin', 'Pindi Bhattian',
        'Phalia', 'Malakwal', 'Talagang', 'Fateh Jang', 'Hassan Abdal',
        'Kot Radha Kishan', 'Raiwind', 'Shahkot', 'Sangla Hill', 'Chichawatni', 'Haroonabad',
        'Fort Abbas', 'Minchinabad', 'Jalalpur Jattan', 'Lalamusa', 'Sarai Alamgir', 'Dinga',
        'Pindi Gheb', 'Jand', 'Isa Khel', 'Kalabagh', 'Piplan', 'Kundian', 'Noorpur Thal',
        'Quaidabad', 'Mian Channu', 'Jahanian', 'Abdul Hakim', 'Qadirpur Ran', 'Alipur',
        'Jatoi', 'Kot Sultan', 'Karor Lal Esan', 'Choa Saidan Shah', 'Kallar Kahar', 'Lawrancepur',
        'Kahuta', 'Kotli Sattian', 'Rawat', 'Humak', 'Bhara Kahu', 'Tarnol', 'Golra',

        // Sindh
        'Karachi', 'Hyderabad', 'Sukkur', 'Larkana', 'Nawabshah', 'Mirpur Khas', 'Jacobabad',
        'Shikarpur', 'Khairpur', 'Dadu', 'Thatta', 'Badin', 'Tando Adam', 'Tando Allahyar',
        'Tando Muhammad Khan', 'Kotri', 'Jamshoro', 'Sehwan', 'Moro', 'Naushahro Feroze',
        'Sanghar', 'Shahdadpur', 'Umerkot', 'Ghotki', 'Rohri', 'Pano Aqil', 'Kandhkot',
        'Kashmore', 'Mehar', 'Kambar', 'Shahdadkot', 'Ratodero', 'Hala', 'Matiari',
        'Digri', 'Kunri', 'Samaro', 'Chhor', 'Mirpur Mathelo', 'Daharki', 'Ubauro',
        'Mehrabpur', 'Kandiaro', 'Bhiria', 'Thari Mirwah', 'Pir Jo Goth', 'Gambat',
        'Kingri', 'Kot Diji', 'Sobhodero', 'Sakrand', 'Daur', 'Qazi Ahmed', 'Daulatpur',
        'Sijawal Junejo', 'Johi', 'Khairpur Nathan Shah', 'Sann', 'Manjhand', 'Nooriabad',
        'Gharo', 'Mirpur Sakro', 'Sujawal', 'Jati', 'Shah Bandar', 'Keti Bandar',
        'Lyari', 'Malir', 'Korangi', 'Landhi', 'Orangi', 'Gulshan-e-Iqbal', 'North Nazimabad',
        'Clifton', 'Defence (Karachi)', 'Gulistan-e-Johar', 'F.B. Area', 'Saddar (Karachi)',

        // Khyber Pakhtunkhwa
        'Peshawar', 'Mardan', 'Mingora', 'Abbottabad', 'Kohat', 'Dera Ismail Khan', 'Swabi',
        'Charsadda', 'Nowshera', 'Mansehra', 'Bannu', 'Chitral', 'Timergara', 'Batagram',
        'Haripur', 'Tank', 'Hangu', 'Karak', 'Lakki Marwat', 'Buner', 'Shangla', 'Dir',
        'Upper Dir', 'Lower Dir', 'Malakand', 'Swat', 'Batkhela', 'Chakdara', 'Topi',
        'Jehangira', 'Akora Khattak', 'Risalpur', 'Tangi', 'Shabqadar', 'Utmanzai',
        'Takht Bhai', 'Katlang', 'Rustam', 'Lund Khwar', 'Hoti', 'Shewa Adda',
        'Parachinar', 'Landi Kotal', 'Jamrud', 'Bara', 'Wana', 'Miranshah', 'Razmak',
        'Ghalegay', 'Kabal', 'Matta', 'Khwazakhela', 'Bahrain', 'Kalam', 'Alpuri',
        'Besham', 'Dassu', 'Pattan', 'Oghi', 'Balakot', 'Ghari Habibullah', 'Havelian',
        'Nawanshahr', 'Ayubia', 'Nathia Gali', 'Kaghan', 'Naran', 'Battagram',

        // Balochistan
        'Quetta', 'Turbat', 'Khuzdar', 'Chaman', 'Gwadar', 'Sibi', 'Zhob', 'Loralai',
        'Dera Murad Jamali', 'Dera Allah Yar', 'Usta Muhammad', 'Hub', 'Pasni', 'Ormara',
        'Jiwani', 'Panjgur', 'Kharan', 'Nushki', 'Mastung', 'Kalat', 'Pishin', 'Qila Abdullah',
        'Qila Saifullah', 'Muslim Bagh', 'Zhob', 'Musakhel', 'Barkhan', 'Kohlu', 'Dera Bugti',
        'Sui', 'Jaffarabad', 'Nasirabad', 'Jhal Magsi', 'Kachhi', 'Bolan', 'Mach',
        'Harnai', 'Ziarat', 'Awaran', 'Lasbela', 'Uthal', 'Bela', 'Wadh', 'Surab',
        'Dalbandin', 'Taftan', 'Washuk', 'Basima', 'Besima',

        // Azad Jammu & Kashmir
        'Muzaffarabad', 'Mirpur', 'Kotli', 'Rawalakot', 'Bhimber', 'Bagh', 'Pallandri',
        'Hattian Bala', 'Neelum', 'Athmuqam', 'Hajira', 'Forward Kahuta', 'Sehnsa',
        'Dadyal', 'New Mirpur City', 'Chakswari', 'Islamgarh',

        // Gilgit-Baltistan
        'Gilgit', 'Skardu', 'Hunza', 'Aliabad', 'Karimabad', 'Chilas', 'Diamer',
        'Ghanche', 'Khaplu', 'Shigar', 'Astore', 'Gupis', 'Yasin', 'Ishkoman',
        'Nagar', 'Passu', 'Sost', 'Gulmit',

        // Additional major / industrial towns
        'Port Qasim', 'Bin Qasim', 'Gadani', 'Hattar', 'Rashakai', 'Raiwind',
        'Sundar', 'Manga Mandi', 'Kot Lakhpat', 'Thokar Niaz Baig', 'Model Town (Lahore)',
        'Johar Town', 'DHA Lahore', 'Bahria Town Lahore', 'Bahria Town Rawalpindi',
        'Bahria Town Karachi', 'Gulberg Lahore', 'Faisal Town', 'Iqbal Town',
        'Cantt Lahore', 'Cantt Rawalpindi', 'Cantt Peshawar', 'Cantt Quetta',
        'Cantt Multan', 'Cantt Hyderabad', 'Cantt Sialkot', 'Cantt Gujranwala',
    ];

    /**
     * Sorted unique city list.
     *
     * @return list<string>
     */
    public function sorted(): array
    {
        $cities = array_values(array_unique(array_filter(array_map('trim', $this->cities))));
        natcasesort($cities);

        return array_values($cities);
    }

    public function isValid(string $city): bool
    {
        $city = trim($city);
        if ($city === '') {
            return false;
        }

        foreach ($this->sorted() as $item) {
            if (strcasecmp($item, $city) === 0) {
                return true;
            }
        }

        return false;
    }
}
