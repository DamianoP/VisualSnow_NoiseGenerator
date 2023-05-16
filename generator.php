<?php
$xSteps=isset($_GET["xSteps"])?$_GET["xSteps"]:1;
$ySteps=isset($_GET["ySteps"])?$_GET["ySteps"]:1;
$maxDuration=isset($_GET["maxDuration"])?$_GET["maxDuration"]:60;
$intensity=isset($_GET["intensity"])?$_GET["intensity"]:180;
$refresh=isset($_GET["refresh"])?$_GET["refresh"]:1;
?>
<html lang="en">
<head>
    <title></title>
    <style>
        * { margin: 0; padding: 0;}

        body, html { height:100%; }

        #canvasVS {
            position:absolute;
            width:100%;
            height:100%;
        }
        .controls{
            position:absolute;
            left:0;
            top:0;
            background-color:white;
            z-index: 2;
            color:black;
        }
    </style>
</head>
<body>
<div class="controls">
    <table>
        <tr>
            <td>X steps</td>
            <td><input type="number" id="xSteps" value="<?= $xSteps ?>"/></td>
        </tr>
        <tr>
            <td>Y steps</td>
            <td><input type="number" id="ySteps" value="<?= $ySteps ?>"/></td>
        </tr>
        <tr>
            <td>Max duration (s)</td>
            <td><input type="number" id="maxDuration" min="10" max="100000" value="<?= $maxDuration ?>"/></td>
        </tr>
        <tr>
            <td>Intensity (0-255)</td>
            <td><input type="number" id="intensity" value="<?= $intensity ?>" min="0" max="255"/></td>
        </tr>
        <tr>
            <td>Refresh (ms)</td>
            <td><input type="number" id="refresh" min="1" max="50" value="<?= $refresh ?>"/></td>
        </tr>
        <tr>
            <td colspan="2" style="margin:auto;text-align: center"><button onclick="reloadPage()"> Confirm </button></td>
        </tr>
    </table>

</div>
<canvas id="canvasVS" ></canvas>

<script>
    let url="please insert your public url that point to this page";

    function reloadPage(){
        xSteps=document.getElementById("xSteps").value;
        ySteps=document.getElementById("ySteps").value;
        maxDuration=document.getElementById("maxDuration").value;
        intensity=document.getElementById("intensity").value;
        refresh=document.getElementById("refresh").value;
        let str="?xSteps="+xSteps+"&ySteps="+ySteps+"&maxDuration="+maxDuration+"&intensity="+intensity+"&refresh="+refresh;
        location.href=url+str;
    }

    let xSteps=Number.parseInt(document.getElementById("xSteps").value);
    let ySteps=Number.parseInt(document.getElementById("ySteps").value);
    let maxDuration=Number.parseInt(document.getElementById("maxDuration").value);
    let intensity=Number.parseInt(document.getElementById("intensity").value); // 0 - 255
    let refresh=Number.parseInt(document.getElementById("refresh").value);

    let dimension   = [document.documentElement.clientWidth, document.documentElement.clientHeight];
    let c           = document.getElementById("canvasVS");
    c.width         = dimension[0];
    c.height        = dimension[1];
    let ctx         = c.getContext("2d", {alpha: false});
    let idata       = ctx.createImageData(c.width, c.height);
    let buffer32    = new Uint8ClampedArray(idata.data.buffer);
    let counter     = 0;
    let img1        = new Image();

    let timeStart,timeEnd;
    let shouldIContinue=true;
    function exercise1() {
        for(let x=0;x<c.width;x+=xSteps){
            for(let y=0;y<c.height;y+=ySteps){
                let i = 4 * (x + y * c.width);
                if(Math.random()<0.5){
                    setColor(i,1);
                }
                else{
                    setColor(i,0);
                }
            }
        }
        //ctx.putImageData(idata, 0, 0);
        createImageBitmap(idata).then(function(imgBitmap){
            ctx.drawImage(imgBitmap, 0,0);
            imgBitmap.close();
        });
        counter++;
        if(counter%100==0){
            console.log("iteration n."+counter);
        }
        if(shouldIContinue){ setTimeout(function(){ exercise1();},refresh); } // nuovo rumore in 5ms
        //if(counter<1000){ setTimeout(function(){ exercise1();},1);}
        //else{ const end = performance.now();console.log(`Execution time: ${end - start} ms`);}
    }

    function setAlpha() {
        for (let x = 0; x < c.width; x++) {
            for (let y = 0; y < c.height; y++) {
                let i = 4 * (x + y * c.width);
                buffer32[i + 0] = 0;
                buffer32[i + 1] = 0;
                buffer32[i + 2] = 0;
                buffer32[i + 3] = 255;
            }
        }
    }
    function setColor(i,color){ //0=black; 1=colored
        if(color==0) {
            buffer32[i] = 0; // R
            buffer32[i+1] = 0; // G
            buffer32[i+2] = 0; // B
        }else{
            buffer32[i]=intensity; // R
            buffer32[i+1]=intensity; // G
            buffer32[i+2]=intensity; // B
        }
        //buffer32[i+3]=255; // alpha
    }
    function startTimer(){
        timeStart=new Date();
    }
    function checkTimer(){
        let timePassed=diff_seconds(new Date(), timeStart);
        console.log("Timer: "+timePassed);
        if(timePassed<maxDuration){
            setTimeout(function(){ checkTimer();  },1000);
        }else{
            shouldIContinue=false;
            alert("Exercise ended");
        }
    }
    setAlpha();
    const start = performance.now();
    startTimer();
    exercise1();
    setTimeout(function(){ checkTimer();  },1000);


    function diff_minutes(dt2, dt1){
        let diff =(dt2.getTime() - dt1.getTime()) / 1000;
        diff /= (60);
        return Math.abs(Math.round(diff));
    }

    function diff_seconds(dt2, dt1){
        let diff =(dt2.getTime() - dt1.getTime()) / 1000;
        return Math.abs(Math.round(diff));
    }

</script>
</body>
</html>
