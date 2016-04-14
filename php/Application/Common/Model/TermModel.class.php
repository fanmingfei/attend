<?php
namespace Common\Model;
use Think\Model\RelationModel;
class TermModel extends RelationModel {

    function getCurrentLessionNum() {

        $currTime = date('H:i', time());

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

    function getCurrentWeek() {
        $term = $this->find(1);
        $start = $term['starttime'];
        $end = $term['endtime'];

        $time = time();

        if ($start > $time || $end < $time) {
            return false;
        }

        $week = ceil(($time - $start) / 60 / 60 / 24 / 7);
        return $week;

    }

}