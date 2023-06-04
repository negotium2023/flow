<?php

use App\ActionableBoolean;
use App\ActionableDate;
use App\ActionableDocument;
use App\ActionableDropdown;
use App\ActionableDropdownItem;
use App\ActionableNotification;
use App\ActionableTemplateEmail;
use App\ActionableMultipleAttachment;
use App\ActionableText;
use Illuminate\Database\Seeder;
use App\Process;
use App\Step;
use App\Activity;
use League\Csv\Reader;
use League\Csv\Statement;

class ProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Process::insert([
            [
                'name' => 'UHY process v0.6',
                'office_id' => 1,
                'not_started_colour' => '409FFF',
                'started_colour' => 'FFFF46',
                'completed_colour' => '3CFF46',
                'deleted_at' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        Step::insert([
            [
                'name' => 'Lead',
                'order' => 1,
                'process_id' => 1
            ],
            [
                'name' => 'Fee proposal',
                'order' => 2,
                'process_id' => 1
            ],
            [
                'name' => 'Prospective',
                'order' => 3,
                'process_id' => 1
            ],
            [
                'name' => 'Service agreed',
                'order' => 4,
                'process_id' => 1
            ],
            [
                'name' => 'Approved',
                'order' => 5,
                'process_id' => 1
            ]
        ]);

        $csv = Reader::createFromPath(database_path('/data/uhy-process.csv', 'r'));
        $csv->setHeaderOffset(0);
        $stmt = (new Statement());
        $records = $stmt->process($csv);

        $activities = [];
        foreach ($records as $record_key => $record) {
            array_push($activities, [
                'step_id' => $record['step_id'],
                'order' => $record['order'],
                'name' => $record['name'],
                'actionable_id' => $record['actionable_id'],
                'actionable_type' => $record['actionable_type'],
                'dependant_activity_id' => $record['dependant_activity_id'] == '' ? null : $record['dependant_activity_id']
            ]);
        }

        Activity::insert($activities);

        ActionableDate::insert([
            [
                'id' => 1,
            ],
            [
                'id' => 2,
            ],
            [
                'id' => 3,
            ],
            [
                'id' => 4,
            ]
        ]);

        ActionableText::insert([
            [
                'id' => 1,
            ],
            [
                'id' => 2,
            ],
            [
                'id' => 3,
            ],
            [
                'id' => 4,
            ],
            [
                'id' => 5,
            ],
            [
                'id' => 6,
            ],
            [
                'id' => 7,
            ]
        ]);

        ActionableDropdown::insert([
            [
                'id' => 1,
            ],
            [
                'id' => 2,
            ],
            [
                'id' => 3,
            ],
            [
                'id' => 4,
            ],
            [
                'id' => 5,
            ],
            [
                'id' => 6,
            ],
            [
                'id' => 7,
            ]
        ]);

        ActionableBoolean::insert([
            [
                'id' => 1,
            ],
            [
                'id' => 2,
            ],
            [
                'id' => 3,
            ],
            [
                'id' => 4,
            ],
            [
                'id' => 5,
            ],
            [
                'id' => 6,
            ],
            [
                'id' => 7,
            ],
            [
                'id' => 8,
            ]
        ]);

        ActionableTemplateEmail::insert([
            [
                'id' => 1,
            ],
            [
                'id' => 2,
            ],
            [
                'id' => 3,
            ],
            [
                'id' => 4,
            ],
            [
                'id' => 5,
            ],
            [
                'id' => 6,
            ],
            [
                'id' => 7,
            ],
            [
                'id' => 8,
            ],
            [
                'id' => 9,
            ],
            [
                'id' => 10,
            ],
            [
                'id' => 11,
            ],
            [
                'id' => 12,
            ],
            [
                'id' => 13,
            ]
        ]);

        ActionableDocument::insert([
            [
                'id' => 1,
            ]
        ]);

        ActionableNotification::insert([
            [
                'id' => 1,
            ],
            [
                'id' => 2,
            ]
        ]);

        ActionableMultipleAttachment::insert([
            [
                'id' => 1,
            ]
        ]);

        ActionableDropdownItem::insert([
            ////////////////////////////////////////////////////////////////////////////////////
            /// Services
            ////////////////////////////////////////////////////////////////////////////////////
            [
                'name' => 'Accounts prep, bookkeeping, taxation, corporate compliance',
                'actionable_dropdown_id' => 2
            ],
            [
                'name' => 'Audit dormant accounts',
                'actionable_dropdown_id' => 2
            ],
            [
                'name' => 'Audit',
                'actionable_dropdown_id' => 2
            ],
            [
                'name' => 'Bookkeeping, taxation, accounts, payroll',
                'actionable_dropdown_id' => 2
            ],
            [
                'name' => 'General ledger & financial statement preparation',
                'actionable_dropdown_id' => 2
            ],
            [
                'name' => 'Bookkeeping (Monthly/Quarterly/Annual)',
                'actionable_dropdown_id' => 2
            ],
            [
                'name' => 'Accounting system setup for new businesses.',
                'actionable_dropdown_id' => 2
            ],
            [
                'name' => 'Computerized payroll services',
                'actionable_dropdown_id' => 2
            ],
            [
                'name' => 'Business tax return preparation (Sales & Use/Business Property)',
                'actionable_dropdown_id' => 2
            ],
            ////////////////////////////////////////////////////////////////////////////////////
            /// Company industry
            ////////////////////////////////////////////////////////////////////////////////////
            [
                'name' => '01 - Crop and animal production, hunting and related service activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '02 - Forestry and logging',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '03 - Fishing and aquaculture',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'B - Mining and quarrying',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '05 - Mining of coal and lignite',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '06 - Extraction of crude petroleum and natural gas',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '07 - Mining of metal ores',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '08 - Other mining and quarrying',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '09 - Mining support service activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'C - Manufacturing',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '10 - Manufacture of food products',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '11 - Manufacture of beverages',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '12 - Manufacture of tobacco products',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '13 - Manufacture of textiles',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '14 - Manufacture of wearing apparel',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '15 - Manufacture of leather and related products',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '16 - Manufacture of wood and of products of wood and cork, except furniture; manufacture of articles of straw and plaiting materials',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '17 - Manufacture of paper and paper products',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '18 - Printing and reproduction of recorded media',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '19 - Manufacture of coke and refined petroleum products',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '20 - Manufacture of chemicals and chemical products',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '21 - Manufacture of basic pharmaceutical products and pharmaceutical preparations',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '22 - Manufacture of rubber and plastics products',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '23 - Manufacture of other non-metallic mineral products',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '24 - Manufacture of basic metals',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '25 - Manufacture of fabricated metal products, except machinery and equipment',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '26 - Manufacture of computer, electronic and optical products',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '27 - Manufacture of electrical equipment',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '28 - Manufacture of machinery and equipment n.e.c.',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '29 - Manufacture of motor vehicles, trailers and semi-trailers',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '30 - Manufacture of other transport equipment',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '31 - Manufacture of furniture',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '32 - Other manufacturing',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '33 - Repair and installation of machinery and equipment',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'D - Electricity, gas, steam and air conditioning supply',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '35 - Electricity, gas, steam and air conditioning supply',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'E - Water supply; sewerage, waste management and remediation activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '36 - Water collection, treatment and supply',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '37 - Sewerage',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '38 - Waste collection, treatment and disposal activities; materials recovery',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '39 - Remediation activities and other waste management services',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'F - Construction',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '41 - Construction of buildings',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '42 - Civil engineering',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '43 - Specialised construction activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'G - Wholesale and retail trade; repair of motor vehicles and motorcycles',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '45 - Wholesale and retail trade and repair of motor vehicles and motorcycles',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '46 - Wholesale trade, except of motor vehicles and motorcycles',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '47 - Retail trade, except of motor vehicles and motorcycles',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'H - Transportation and storage',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '49 - Land transport and transport via pipelines',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '50 - Water transport',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '51 - Air transport',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '52 - Warehousing and support activities for transportation',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '53 - Postal and courier activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'I - Accommodation and food service activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '55 - Accommodation',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '56 - Food and beverage service activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'J - Information and communication',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '58 - Publishing activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '59 - Motion picture, video and television programme production, sound recording and music publishing activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '60 - Programming and broadcasting activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '61 - Telecommunications',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '62 - Computer programming, consultancy and related activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '63 - Information service activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'K - Financial and insurance activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '64 - Financial service activities, except insurance and pension funding',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '65 - Insurance, reinsurance and pension funding, except compulsory social security',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '66 - Activities auxiliary to financial service and insurance activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'L - Real estate activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '68 - Real estate activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'M - Professional, scientific and technical activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '69 - Legal and accounting activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '70 - Activities of head offices; management consultancy activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '71 - Architectural and engineering activities; technical testing and analysis',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '72 - Scientific research and development',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '73 - Advertising and market research',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '74 - Other professional, scientific and technical activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '75 - Veterinary activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'N - Administrative and support service activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '77 - Rental and leasing activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '78 - Employment activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '79 - Travel agency, tour operator, reservation service and related activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '80 - Security and investigation activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '81 - Services to buildings and landscape activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '82 - Office administrative, office support and other business support activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'O - Public administration and defence; compulsory social security',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '84 - Public administration and defence; compulsory social security',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'P - Education',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '85 - Education',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'Q - Human health and social work activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '86 - Human health activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '87 - Residential care activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '88 - Social work activities without accommodation',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'R - Arts, entertainment and recreation',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '90 - Creative, arts and entertainment activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '91 - Libraries, archives, museums and other cultural activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '92 - Gambling and betting activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '93 - Sports activities and amusement and recreation activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'S - Other service activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '94 - Activities of membership organisations',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '95 - Repair of computers and personal and household goods',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '96 - Other personal service activities',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'T - Activities of households as employers; undifferentiated goods- and services-producing activities of households for own use',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '97 - Activities of households as employers of domestic personnel',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '98 - Undifferentiated goods- and services-producing activities of private households for own use',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => 'U - Activities of extraterritorial organisations and bodies',
                'actionable_dropdown_id' => 1
            ],
            [
                'name' => '99 - Activities of extraterritorial organisations and bodies',
                'actionable_dropdown_id' => 1
            ],
            ////////////////////////////////////////////////////////////////////////////////////
            /// director
            ////////////////////////////////////////////////////////////////////////////////////
            [
                'name' => 'AF',
                'actionable_dropdown_id' => 3
            ],
            [
                'name' => 'BR',
                'actionable_dropdown_id' => 3
            ],
            [
                'name' => 'GE',
                'actionable_dropdown_id' => 3
            ],
            [
                'name' => 'MB',
                'actionable_dropdown_id' => 3
            ],
            [
                'name' => 'RB',
                'actionable_dropdown_id' => 3
            ],
            [
                'name' => 'RD',
                'actionable_dropdown_id' => 3
            ],
            [
                'name' => 'TMcD',
                'actionable_dropdown_id' => 3
            ],
            [
                'name' => 'TMcD',
                'actionable_dropdown_id' => 3
            ],
            [
                'name' => 'BRe / RC',
                'actionable_dropdown_id' => 3
            ],
            ////////////////////////////////////////////////////////////////////////////////////
            /// Request sent
            ////////////////////////////////////////////////////////////////////////////////////
            [
                'name' => 'No',
                'actionable_dropdown_id' => 4
            ],
            [
                'name' => 'Yes, ID-Pal',
                'actionable_dropdown_id' => 4
            ],
            [
                'name' => 'Yes, Email',
                'actionable_dropdown_id' => 4
            ],
            ////////////////////////////////////////////////////////////////////////////////////
            /// Review completed by
            ////////////////////////////////////////////////////////////////////////////////////
            [
                'name' => 'orlawynne@fdw.ie',
                'actionable_dropdown_id' => 5
            ],
            [
                'name' => 'francoisvanheerden@fdw.ie',
                'actionable_dropdown_id' => 5
            ],
            [
                'name' => 'nicolamernagh@fdw.ie',
                'actionable_dropdown_id' => 5
            ],
            ////////////////////////////////////////////////////////////////////////////////////
            /// Compliance Officer approves/declines
            ////////////////////////////////////////////////////////////////////////////////////
            [
                'name' => 'Approved',
                'actionable_dropdown_id' => 6
            ],
            [
                'name' => 'Declined',
                'actionable_dropdown_id' => 6
            ],
            ////////////////////////////////////////////////////////////////////////////////////
            /// Reason if declined
            ////////////////////////////////////////////////////////////////////////////////////
            [
                'name' => 'AML Insufficient',
                'actionable_dropdown_id' => 7
            ],
            [
                'name' => 'CRF Insufficient',
                'actionable_dropdown_id' => 7
            ],
            [
                'name' => 'Further Information Required',
                'actionable_dropdown_id' => 7
            ],
        ]);
    }
}
