<?php

function test(){
    $descriptorspec = array(
        0 => array("pipe", "r"),    // stdin is a pipe that the child will read from
        1 => array("pipe", "w"),    // stdout is a pipe that the child will write to
        2 => array("pipe", "w")     // stderr is a pipe that the child will write to
    );
    $cmd = "rtmpdump  -r \"rtmp://60.9.1.22/liverecord/Y-657635-20170922170232?wsSecret=fbd183f1e00c7331f5a2b3b0e970f77c&eTime=59c4da5c&wsiphost=ipdbm&wsHost=dev-drtmp.huanpeng.com\"  -B 1 -m 3 -o /data/tmp/8.flv";

    $process = proc_open($cmd, $descriptorspec, $pipes);
    if (is_resource($process)) {
        $stdin = stream_get_contents($pipes[0]);var_dump($stdin);
        $stdout = stream_get_contents($pipes[1]);var_dump($stdout);
        $stderr = stream_get_contents($pipes[2]);var_dump($stderr);
        fclose($pipes[0]);  fclose($pipes[1]);  fclose($pipes[2]);
        // It is important that you close any pipes before calling proc_close in order to avoid a deadlock
        $return_value = proc_close($process);
    }
}

test();