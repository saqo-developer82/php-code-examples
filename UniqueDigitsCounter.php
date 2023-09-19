<?php

class UniqueDigitsCounter
{
    /**
     * Calculates the number of unique numbers that can be formed with a given number of digits.
     *
     * @param int $d The number of digits.
     * @return int The number of unique numbers.
     */
    function uniqueNumCount($d) {
        if ($d == 1) {
            return 10;
        }

        $cnt = 9;

        for ($i = 0; $i < $d - 1; ++$i) {
            $cnt *= 9 - $i;
        }

        return $cnt;
    }

    /**
     * Counts the number of digits in a given number.
     *
     * @param mixed $number The number to count digits from.
     * @return int The number of digits in the given number.
     */
    function countDigits($number) {
        return strlen((string) abs($number));
    }

    /**
     * Counts the number of unique numbers within a given range.
     *
     * @param int $n The maximum number of digits to consider.
     * @return int The total count of unique numbers.
     */
    function count($n) {
        if ($n == 0) {
            return 1;
        }

        $maxNumber = pow(10, $n);
        $numbersCnt = [];

        for ($x = 0; $x < $maxNumber; ++$x) {
            $digitsCnt = $this->countDigits($x);

            if ($digitsCnt > 10) {
                break;
            }

            if (!isset($numbersCnt[$digitsCnt])) {
                $numbersCnt[$digitsCnt] = $this->uniqueNumCount($digitsCnt);
            }
        }

        return array_sum($numbersCnt);
    }
}

$result = (new UniqueDigitsCounter())->count(3);

print_r($result);