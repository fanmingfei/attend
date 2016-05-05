<?php
namespace Common\Model;
use Think\Model\RelationModel;
class TermModel extends RelationModel {

    function getCurrentLessionNum($time) {

        $currTime = $time ? $time : date('H:i', time());

        $timearr = explode(':', $currTime);
        $currentTimeStr = intval($timearr[0]) * 60 + intval($timearr[1]);
        $term = $this->find(1);

        $tmp = $term;

        unset($tmp['starttime']);
        unset($tmp['endtime']);


        $array = array();

        foreach ($tmp as $key => $value) {
            $arr = explode(':', $value);
            $array[$key] = intval($arr[0]) * 60 + intval($arr[1]);
        }

        $array1[] = array($array['onestart'] - 10, $array['oneend']);
        $array1[] = array($array['twostart'] - 10, $array['twoend']);
        $array1[] = array($array['threestart'] - 10, $array['threeend']);
        $array1[] = array($array['fourstart'] - 10, $array['fourend']);
        $array1[] = array($array['fivestart'] - 10, $array['fiveend']);


        $lession = 0;
        foreach ($array1 as $key => $value) {
            if ($value[0] < $currentTimeStr && $currentTimeStr < $value[1]) {
                $lession = $key + 1;
            }
        }
        return $lession;
    }

    function getCurrentWeek($time) {
        $term = $this->find(1);
        $start = $term['starttime'];
        $end = $term['endtime'];

        $time = $time ? $time : time();

        if ($start > $time || $end < $time) {
            return false;
        }

        $week = ceil(($time - $start) / 60 / 60 / 24 / 7);
        return $week;
    }
    function getWeekByTime($time) {
        $term = $this->find(1);
        $start = $term['starttime'];
        $end = $term['endtime'];

        if ($start > $time || $end < $time) {
            return false;
        }

        $week = ceil(($time - $start) / 60 / 60 / 24 / 7);
        return $week;
    }

    function getAllLessionTimeOneDay($time) {
        $time = strtotime(date('Y-m-d', $time));
        $term = $this->find(1);

        $tmp = $term;

        unset($tmp['starttime']);
        unset($tmp['endtime']);


        $array = array();

        foreach ($tmp as $key => $value) {
            $arr = explode(':', $value);
            $array[$key] = intval($arr[0]) * 60*60 + intval($arr[1]) * 60;
        }

        $array1[] = $time + $array['onestart'];
        $array1[] = $time + $array['twostart'];
        $array1[] = $time + $array['threestart'];
        $array1[] = $time + $array['fourstart'];
        $array1[] = $time + $array['fivestart'];

        return $array1;

    }
    function getAllLessionEndTimeOneDay($time) {
        $time = strtotime(date('Y-m-d', $time));
        $term = $this->find(1);

        $tmp = $term;

        unset($tmp['starttime']);
        unset($tmp['endtime']);


        $array = array();

        foreach ($tmp as $key => $value) {
            $arr = explode(':', $value);
            $array[$key] = intval($arr[0]) * 60*60 + intval($arr[1]) * 60;
        }

        $array1[] = $time + $array['oneend'];
        $array1[] = $time + $array['twoend'];
        $array1[] = $time + $array['threeend'];
        $array1[] = $time + $array['fourend'];
        $array1[] = $time + $array['fiveend'];

        return $array1;

    }

    function getLessionTimeByRange($start, $end) {

        $startDate = date('Y-m-d', $start);
        $endDate = date('Y-m-d', $end);

        $startTime = strtotime($startDate);
        $endTime = strtotime($endDate);

        $lessionsTime = array();

        if ($startDate !== $endDate) {
            $lessionsTime = array_merge($lessionsTime, $this->getLessionByTimeAtStartDay($start));
            for ($s = $startTime + 86400; $s < $endTime; $s += 86400) {
                $theDayLessions = $this->getAllLessionTimeOneDay($s);
                $lessionsTime = array_merge($lessionsTime, $theDayLessions);
            }
            $lessionsTime = array_merge($lessionsTime, $this->getLessionByTimeAtEndDay($end));
        } else {
            $lessionsTime = $this->getLessionByTimeInOneDay($start, $end);
        }



        return $lessionsTime;

    }

    function getLessionByTimeAtStartDay($time) {
        $lessionTime = $this->getAllLessionEndTimeOneDay($time);
        $array = array();
        foreach ($lessionTime as $key => $value) {
            if ($time < $value) {
                $array[] = $value-10;
            }
        }
        return $array;

    }
    function getLessionByTimeAtEndDay($time) {
        $lessionTime = $this->getAllLessionTimeOneDay($time);
        $array = array();
        foreach ($lessionTime as $key => $value) {
            if ($time > $value) {
                $array[] = $value;
            }
        }
        return $array;
    }

    function getLessionByTimeInOneDay($start, $end)
    {
        $lessionStartTime = $this->getAllLessionTimeOneDay($start);
        $lessionEndStart = $this->getAllLessionEndTimeOneDay($start);

        $array = array();
        foreach ($lessionEndStart as $key => $value) {
            if ($start < $value) {
                if ($end > $lessionStartTime[$key]) {
                    $array[] = $lessionStartTime[$key];
                }
            }
        }

        return $array;
        
    }

    

}