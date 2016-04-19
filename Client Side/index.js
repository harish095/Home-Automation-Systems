var five = require("johnny-five");
var firebase = require("firebase");

var board = new five.Board();
var led1, led2;

board.on("ready", function () {
    console.log("Board successfully connected.");

    led1 = new five.Led(3);
    led2 = new five.Led(5);

    var led1Ref = new Firebase("https://home-automation-system.firebaseio.com/devices/led1");
    var led2Ref = new Firebase("https://home-automation-system.firebaseio.com/devices/led2");

    led1Ref.on("value", function (snapshot) {
        var ledState = snapshot.val();
        if (ledState.status == true) {
            led1.on();
            led1.brightness(ledState.brightness);
            console.log("Led 1 turned on or brightness changed.");
        }
        else if (ledState.status == false) {
            led1.off();
            console.log("Led 1 turned off.");
        }
    });

    led2Ref.on("value", function (snapshot) {
        var ledState = snapshot.val();
        if (ledState.status == true) {
            led2.on();
            led2.brightness(ledState.brightness);
            console.log("Led 2 turned on or brightness changed.");
        }
        else if (ledState.status == false) {
            led2.off();
            console.log("Led 2 turned off.");
        }
    });
});

console.log("Waiting for the device to connect...");