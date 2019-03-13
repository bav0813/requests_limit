<?php
    /**
     * Created by PhpStorm.
     * User: andrey
     * Date: 10.02.18
     * Time: 22:00
     */

    require_once "block_with_limit_wait.php";

    $limit=5;          //не более  5 запросов
    $time_limit=5*60;  //в течение 5 минут


    function getFilenameByIp(){
        global $type;

        if ($type=='serialize') {
            $folder='serial';
        }
        else {
            $folder='json';
        }



        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
         $filename=$folder.DIRECTORY_SEPARATOR.$ip.'.txt';
        return $filename;
    }

    function readFromFile($fname){
        global $type;

        $h1 = fopen($fname, 'r');
        $data=fread($h1,filesize($fname));
        if ($type=='serialize') {
            $data_arr = unserialize($data);
        }
        elseif ($type=='json'){


            $data_arr=json_decode($data);

        }
        fclose($h1);

        return $data_arr;
    }

    function updateFile($fname,$data){
        global $type;
        $h1 = fopen($fname, 'w+');

        array_push($data,time());
        rsort($data);
        if ($type=='serialize') {
            $data_arr = serialize($data);
        }
        elseif ($type=='json') {
            $data_arr=json_encode($data);
        }
        fwrite($h1, $data_arr);
        fclose($h1);

    }

    function createFile($fname,$data){
        global $type;
        $data_arr=[];
        $h1 = fopen($fname, 'w');
        array_push($data_arr,$data);

        if ($type=='serialize') {
            $data_arr = serialize($data_arr);
        }
        elseif ($type=='json') {
            $data_arr=json_encode($data_arr);
        }

        fwrite($h1,$data_arr);

        fclose($h1);

    }

    function checkAttemptsCount($data){

        global $time_limit, $limit,$type;
        $curr_time=time();
        echo 'current time: '.date('H:i:s',$curr_time) . '<br>';
        $attempts_cnt=1;
        $time_threhold=$curr_time-$time_limit;
        echo "timelimit,sec: $time_limit <br>";
        echo 'threhold: '. date('H:i:s',$time_threhold) . '<hr>';
        $data_length=count($data);
        for ($i=0;$i<$data_length;$i++){

            if ($data[$i]>$time_threhold){
                $attempts_cnt++;
            }
            //удаляем устаревшие записи
            else{
                echo 'Removing outdated records:....'.date('H:i:s',$data[$i]).  '<br>';
                unset($data[$i]);
            }

        }
        if ($attempts_cnt<=$limit) {
            updateFile(getFilenameByIp(),$data);
            echo "<b>Access granted with $attempts_cnt attempts</b><br>";
        }
        else {
          
            $data_length=count($data);
            $N = ($data[$data_length-1] + $time_limit)-$curr_time;

            echo "<b style='color:red'> Вы слишком часто отправляете форму, подождите $N секунды</b><br>";
        }


    }


    // при выборе radiobutton для  варианта serialize:
    if ($_POST['case2']==="serialize") {
        $type='serialize';
    }

    // при выборе radiobutton варианта JSON:
    elseif ($_POST['case2']==="json") {
        $type='json';
    }
    $fname = getFilenameByIp();

    // проверка наличия файла с логами запросов от данного IP
    if (file_exists($fname)) {
        $request_info = readFromFile($fname);
        checkAttemptsCount($request_info);
        //вывод данных из файла
        echo "log предыдущих запросов: <br>";
        foreach ($request_info as $k => $value) {
            echo date('H:i:s', $value) . '<br>';
        }
    } else {
        //если файла с логами запросов от данного IP небыло - создаем
        $update_info = time();
         
         if (!is_dir($type)) {
                mkdir($type,0777);
            }
        createFile($fname, $update_info);
        echo "file $fname created";
    }








