const { exec } = require('child_process');
const vision = require('@google-cloud/vision');
var fs = require('fs');
const fetch = require("node-fetch");


async function takeStill () {
    var child = exec('libcamera-jpeg -n -o ./images/realtime.jpg --shutter 5000000 --gain 0.5 --width 700 --height 500');

    child.stdout.on('data', function (data) {
        console.log('child process exited with ' +
            `code ${data}`);
    });

    child.on('exit', function (code, signal) {
        console.log('Image Capture   ' + Date.now());

    });
}

async function setEndpoint() {
    // Specifies the location of the api endpoint
    const clientOptions = { 
        // apiEndpoint: 'eu-vision.googleapis.com',
        keyFilename: "./gcp-key.json" 
    };

    // Creates a client
    const client = new vision.ImageAnnotatorClient(clientOptions);

    // Performs text detection on the image file
    const [result] = await client.textDetection('./images/realtime.jpg');
    const labels = result.textAnnotations;
    //console.log('Text:');

    var license_number = null;

    labels.forEach(function (a, b) {
        if (b == 0) {
            license_number = a.description;
        }
    });

    return license_number;
}


async function sendToPHP() {
    await takeStill();
    try{
        var license_value = await setEndpoint();
    }
    catch(error){
        console.log(error);
        return;
    }
    if (license_value == null){
        console.log("no license value");
        return;
    }
 

    var result ="";
    var license_number = license_value.split(/\n|\r|\t/g);
    console.log("this is from googleapi");
    console.log(license_number);
    console.log("this is the end of license value");
    var pattern = /\d{4} \w{2}/;
    var taxi_pattern = /\w{2}\s*\d{4}/;
    var license_plate = license_number.find(value => pattern.test(value) || taxi_pattern.test(value) ); 
    console.log(license_plate);
    
    if (license_plate == undefined){
        return; 
    }
    var response = await fetch("http://localhost/post-pi-data.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8"
        },
        body:`license=${license_plate}&api_key=tPmAT5Ab3j7F9`
    });
    var res = await response.text(); 
    console.log(res);
}
setInterval(sendToPHP, 5000);







