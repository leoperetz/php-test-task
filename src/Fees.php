<?php

namespace LeoProject;

use Symfony\Component\Yaml\Yaml;

class Fees
{
    private $filepath;
    private $config;
    private $rates;
    private $result;
    private $usersCheck = []; // temporary array used while check discount for users

    /**
     * prepare internal variables
     */
    public function __construct(string $filename, $config)
    {
        $fullpath = getcwd() . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($fullpath)) {
            throw new \Exception('No filename: ' . $fullpath);
        }
        $this->filepath = $fullpath;
        $this->config = $config;
        $this->rates = get_object_vars($config->rates);
    }

    /**
     * convert currency to/from EUR
     * @param $currParam Currency code
     * @param $amountParam Amount value
     * @param bool $back If true, it converts EUR to $currParam
     * @return float Amount
     */
    public function convertCurrency($currParam, $amountParam, $back = false)
    {
        $amount = $amountParam;
        $curr = strtolower($currParam);
        $rate = $this->rates[$curr] ?? 1;
        if ($rate <> 0 && $rate <> 1) {
            $amount = $back ? ($amount * $rate) : ($amount / $rate);
        }
        return round($amount, 3, PHP_ROUND_HALF_UP);
    }

    /**
     * get user' operations data accumulated for week
     * @param $date
     * @param $userId
     * @return array
     */
    public function getUserWeekData($date, $userId)
    {
        $weekNumber = date('W', strtotime($date));
        $userWeeks = $this->usersCheck[$userId] ?? [];
        return [
            $weekNumber,
            $userWeeks[$weekNumber] ?? [],
            $userWeeks
        ];
    }

    /**
     * add data to tmp array while check for cash_out for natural persons:
     * make array like
     * [
     *   user id (int): [
     *     week number in year (int): {
     *       count of operations in week: (int)
     *       total amount: (float)
     *     }
     *   ]
     * ]
     * @param $date YYY-MM-DD
     * @param $id User ID
     * @param $amount Already converted to EUR
     */
    public function addUserToCheck($date, $userId, $amount)
    {
        list($weekNumber, $weekData, $userWeeks) = $this->getUserWeekData($date, $userId);
        $userWeeks[$weekNumber] = [
            'total' => ($weekData['total'] ?? 0) + $amount,
            'count' => ($weekData['count'] ?? 0) + 1
        ];
        $this->usersCheck[$userId] = $userWeeks;
        print_r($this->usersCheck);
    }

    /**
     * check user's sum in week for discount
     * @return bool Discount is allowed
     */
    public function checkUserWeekDiscountSum($date, $userId, $amount)
    {
        list($weekNumber, $weekData, $userWeeks) = $this->getUserWeekData($date, $userId);
        $total = $weekData['total'] ?? 0;
        $count = $weekData['count'] ?? 0;
        $free = $this->config->fees->out->natural->free;
        if ($count > 3) {
            return $amount;
        }
        if ($total > $free) {
            $over = $total - $free;
            $userWeeks[$weekNumber] = [
                'total' => $total - $over,
                'count' => $count
            ];
            $this->usersCheck[$userId] = $userWeeks;
            return $over;
        }
        return 0;
    }

    /**
     * calculate fee for one parsed row
     * @param $row Array of data
     * @return double Sum of fee
     */
    public function calculateRow($row)
    {
        list($date, $id, $type, $op, $amount, $curr) = $row;
        $amount = $this->convertCurrency($curr, $amount); // convert to EUR
        $sum = 0;
        // set fee
        if ($op === 'cash_in') {
            $max = $this->config->fees->in->max;
            $sum = $amount * $this->config->fees->in->fee / 100;
            if ($sum > $max) {
                $sum = $max;
            }
        } else {
            // cash_out
            if ($type === 'legal') {
                $min = $this->config->fees->out->legal->min;
                $sum = $amount * $this->config->fees->out->legal->fee / 100;
                if ($sum < $min) {
                    $sum = $min;
                }
            } else {
                // natural
                $this->addUserToCheck($date, $id, $amount);
                $sum = $this->checkUserWeekDiscountSum($date, $id, $amount);
                $sum = $sum * $this->config->fees->out->natural->fee / 100;
            }
        }
        return $this->convertCurrency($curr, $sum, true); // convert back to given currency
    }

    /**
     * get CSV from file, parse it and calculate fees row by row
     */
    public function calculate()
    {
        if ($this->fullpath ?? false) {
            throw new \Exception('No file passed');
        }
        $this->result = [];
        $this->usersCheck = [];
        // open the file
        if (($handle = fopen($this->filepath, "r")) !== false) {
            // get & parse data row by row
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                // check columns count
                if (count($data) < 6) {
                    throw new \Exception('Incorrect number of columns in given file');
                }
                array_push($this->result, $this->calculateRow($data));
            }
            fclose($handle);
        }
    }

    /**
     *
     */
    public function getResult()
    {
        return $this->result;
    }
}
