<!DOCTYPE html>

</html>
<script type="text/javascript" src="https://livejs.com/live.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.12.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-automl@1.3.0/dist/tf-automl.min.js"></script>
<script src='https://unpkg.com/tesseract.js@2.1.4/dist/tesseract.min.js'></script>
<!-- CSS only -->

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css" />
<style>
    thead {
        font-size: 30px;
    }

    h3.license {
        font-size: 16px;
        line-height: 30px;
    }

    mark {
        font-size: 25px;
        line-height: 30px;
    }

    .tesseract {
        font-size: 15px;
    }
    .img-container {
        display: flex;
        width: 100%;
        padding: 0 40px 0 40px;
        justify-content: space-evenly;
        /* background: lightblue; */
    }
</style>

<body>
<?php include("header.php");?>
<div class="img-container">
    <div>
        <img id="license-plate-image" crossorigin="anonymous" src="http://192.168.100.250/app/images/realtime.jpg">
    </div>
    <div>
        <canvas id="canvas"></canvas>
    </div>
</div>

    <input type="button" id="run-model" value="run-model">
</body>



<script>

    function log(a) {
        document.querySelector("pre").append("\n\n" + a)
    }

    async function run() {

        // Localize the License Plate

        const model = await tf.automl.loadObjectDetection('http://192.168.100.250/app/model/model.json');
        const img = document.getElementById('license-plate-image');
        const options = {
            score: 0.5,
            iou: 0.5,
            topk: 20
        };
        const predictions = await model.detect(img, options);


        var img3 = document.getElementById('license-plate-image');
        var width = img3.clientWidth;
        var height = img3.clientHeight;



        var c = document.getElementById("canvas");
        var ctx = c.getContext("2d");
        ctx.canvas.width = width;
        ctx.canvas.height = height;

        var img3 = document.getElementById("license-plate-image");


        if (!!predictions && !!predictions[0]) {
            var left = predictions[0].box.left - 15;
            var top = predictions[0].box.top - 15;
            var width = predictions[0].box.width + 35;
            var height = predictions[0].box.height + 35;

            ctx.drawImage(img3, left, top, width, height, left, top, width, height);

        }

        const exampleImage = 'http://192.168.100.250:8080/images/realtime.jpg';

        const worker = Tesseract.createWorker({
            logger: m => console.log(m)
        });
        Tesseract.setLogging(true);
        work();

        async function work() {
            await worker.load();
            await worker.loadLanguage('eng');
            await worker.initialize('eng');



            await worker.setParameters({
                tessedit_pageseg_mode: 13,
            });

            var img = c.toDataURL("image/png");

            // take cropped
            result = await worker.recognize(img);

            console.log(result);

                    await worker.terminate();

        }
    }
    var model_runner= document.getElementById("run-model");
    model_runner.addEventListener("click",run);
</script>