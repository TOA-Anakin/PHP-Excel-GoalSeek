<?php

require(__DIR__ . '/../src/PHPExcelGoalSeek.php');

// First, wrap your functions in your own class that extends PHPExcelGoalSeek
class GoalSeek extends \davidjr82\PHPExcelGoalSeek\PHPExcelGoalSeek {

    function __construct($data) {
        $this->data = $data;
    }

    function safeDiv($a, $b) {
        return $b == 0 ? 0 : ($a / $b);
    }
    
    function sign($val) {
        if ($val > 0) {
            return 1;
        } elseif ($val < 0) {
            return -1;
        } else {
            return 0;
        }
    }

    function callbackTest($P_z_ref) {
        $data = $this->data;

        $q_m_V_arg_in = $data['q_m_V_arg_in'];
        $q_m_V_arg_out = $data['q_m_V_arg_out'];
        $SumOf__q_V_sup = $data['SumOf__q_V_sup'];
        $SumOf__q_V_eta = $data['SumOf__q_V_eta'];
        $P_e_lea = $data['P_e_lea'];
        $ro_a_ref = $data['ro_a_ref'];
        $h_zone_defaultLeak = $data['h_zone_defaultLeak'];
        $g = $data['g'];
        $T_e_ref = $data['T_e_ref'];
        $T_e = $data['T_e'];
        $C_lea_h_zone = $data['C_lea_h_zone'];
        $n_lea = $data['n_lea'];
        $ro_lea_in = $data['ro_lea_in'];
        $ro_lea_out = $data['ro_lea_out'];

        $q_V_lea_in = 0;
        $q_V_lea_out = 0;
        for ($i = 1; $i <= 5; $i++) {
            $P_e_lea_tmp = $P_e_lea[$i];
            $P_i_lea_tmp = $P_z_ref - $ro_a_ref * $h_zone_defaultLeak[$i] * $g * $this->safeDiv($T_e_ref, $T_e);;
            $C_lea_h_zone_tmp = $C_lea_h_zone[$i];

            $delta_p_lea = $P_e_lea_tmp - $P_i_lea_tmp;	// tlakový rozdíl
            $q_V_lea = $C_lea_h_zone_tmp * $this->sign($delta_p_lea) * pow(abs($delta_p_lea), $n_lea);	// objemový průtok pro netěsnosti v obálce budovy

            if ($q_V_lea > 0) {
                $q_V_lea_in += $q_V_lea;
            } elseif ($q_V_lea < 0) {
                $q_V_lea_out += $q_V_lea;
            }
        }

        $q_m_V_lea_in = $q_V_lea_in * $ro_lea_in;
        $q_m_V_lea_out = $q_V_lea_out * $ro_lea_out;

        $solution = $q_m_V_arg_in + $q_m_V_arg_out + $q_m_V_lea_in + $q_m_V_lea_out + $SumOf__q_V_sup + $SumOf__q_V_eta;

        return $solution;
    }
}

$data = [
    'q_m_V_arg_in' => 0,
    'q_m_V_arg_out' => 0,
    'SumOf__q_V_sup' => 639.54,
    'SumOf__q_V_eta' => -606.82,
    'P_e_lea' => [
        '1' => -17.57,
        '2' => -27.69,
        '3' => -61.14,
        '4' => -71.25,
        '5' => -92.20
    ],
    'ro_a_ref' => 1.204,
    'h_zone_defaultLeak' => [
        '1' => 1.75,
        '2' => 1.75,
        '3' => 5.25,
        '4' => 5.25,
        '5' => 7
    ],
    'g' => 9.81,
    'T_e_ref' => 293.15,
    'T_e' => 20 + 273.15,
    'C_lea_h_zone' => [
        '1' => 11.7987,
        '2' => 11.7987,
        '3' => 11.7987,
        '4' => 11.7987,
        '5' => 26.9786
    ],
    'n_lea' => 0.667,
    'ro_lea_in' => 1.2689,
    'ro_lea_out' => 1.2040
];

$goalseek = new GoalSeek($data);

$expected_result = 0;

$input = $goalseek->calculate('callbackTest', $expected_result, 6);

// Voilá!
echo "\$input: " . $input . "<br />";

// Let's test our input it is close
$actual_result = $goalseek->callbackTest($input);
// Searched result of function
echo "Searched result of callbackTest(\$input) = " . $expected_result . "<br />";
// Actual result of function with calculated goalseek
echo "Actual result of callbackTest(" . $input . ") = " . $actual_result . "<br />";
// If difference is too high, you can improve the class and send me it your modifications ;)
echo "Difference = " . ($actual_result - $expected_result);

